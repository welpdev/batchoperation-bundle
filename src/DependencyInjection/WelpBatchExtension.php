<?php

namespace Welp\BatchBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class WelpBatchExtension extends Extension
{
    private $container;
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->container = $container;
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $bundles = $container->getParameter('kernel.bundles');

        $container->setParameter('welp_batch.entity_manager', $config['entity_manager']);
        $container->setParameter('welp_batch.broker_type', $config['broker_type']);
        $container->setParameter('welp_batch.batch_entity.batch', $config['batch_entity']['batch']);
        $container->setParameter('welp_batch.broker_connection', $config['broker_connection']);
        $container->setParameter('welp_batch.batch_results_folder', $config['batch_results_folder']);


        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load($config['broker_type'].'.yml');
        $loader->load('managers.yml');
        $loader->load('listeners.yml');

        if ($config['broker_type'] == 'rabbitmq') {
            if (!isset($bundles['OldSoundRabbitMqBundle'])) { // TODO check wihch exception to raise
                throw new \Exception('You must install OldSoundRabbitMqBundle in order to use the broker_type rabbitmq');
            }
            $this->loadRMQProducerDynamically($config['manage_entities'], $config['broker_connection']);
            $this->loadRMQConsumerDynamically($config['manage_entities'], $config['broker_connection']);
        }

        //TODO other broker type
    }


    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias()
    {
        return 'welp_batch';
    }

    /**
     * This function create rabbitMQ Producer automatically. It use the manage_entity in the config to set up the consumer
     * @param  array  $managedEntity  Manage entity from configuration
     * @param  string $connectionName Connection name of the rabbitMQ connection (get from the configuration)
     */
    public function loadRMQProducerDynamically(array $managedEntity, $connectionName)
    {
        foreach ($managedEntity as $key => $entity) { // for each entity, we create a new producer
            foreach ($entity['actions'] as $action) {
                $definition = new Definition('OldSound\RabbitMqBundle\RabbitMq\Producer');
                $definition->addTag('old_sound_rabbit_mq.base_amqp');
                $definition->addTag('old_sound_rabbit_mq.producer');
                $definition->addMethodCall('setExchangeOptions', array($this->normalizeArgumentKeys(array(
                    'name' => 'welp.batch.'.$key.'.'.$action,
                    'type'=> 'direct'
                ))));
                $definition->addMethodCall('setQueueOptions', array(array(
                    'name' => 'welp.batch.'.$key.'.'.$action,
                    'routing_keys' => ['welp.batch.'.$key.'.'.$action]
                )));

                $definition->addArgument(new Reference(sprintf('old_sound_rabbit_mq.connection.%s', $connectionName)));
                $definition->addMethodCall('disableAutoSetupFabric');
                $producerServiceName = sprintf('old_sound_rabbit_mq.%s_producer', 'welp_batch_'.$key.'_'.$action);
                $this->container->setDefinition($producerServiceName, $definition);
            }
        }
    }

    /**
     * This function create rabbitMQ Consumer automatically. It use the manage_entity in the config to set up the consumer
     * @param  array  $managedEntity  Manage entity from configuration
     * @param  string $connectionName Connection name of the rabbitMQ connection (get from the configuration)
     */
    public function loadRMQConsumerDynamically(array $managedEntity, $connectionName)
    {
        foreach ($managedEntity as $key => $entity) {
            //create the service that the producer will call
            $serviceName = $this->createConsumerService($key, $entity);
            foreach ($entity['actions'] as $action) {
                $definition = new Definition('OldSound\RabbitMqBundle\RabbitMq\Consumer');
                $definition->addTag('old_sound_rabbit_mq.base_amqp');
                $definition->addTag('old_sound_rabbit_mq.consumer');

                $definition->addMethodCall('setExchangeOptions', array($this->normalizeArgumentKeys(array(
                    'name' => 'welp.batch.'.$key.'.'.$action,
                    'type'=> 'direct'
                ))));

                $definition->addMethodCall('setQueueOptions', array(array(
                    'name' => 'welp.batch.'.$key.'.'.$action,
                    'routing_keys' => ['welp.batch.'.$key.'.'.$action]
                )));

                /*$definition->addMethodCall('setQosOptions', array(
                    0,
                    $entity['batch_size'],
                    false
                ));*/

                $definition->addMethodCall('setCallback', array(array(new Reference($serviceName), 'execute')));

                $definition->addArgument(new Reference(sprintf('old_sound_rabbit_mq.connection.%s', $connectionName)));


                $name = sprintf('old_sound_rabbit_mq.%s_consumer', 'welp_batch.'.$key.'.'.$action);

                $this->container->setDefinition($name, $definition);
                $this->addDequeuerAwareCall($serviceName, $name);
            }
        }
    }

    /**
     * This function create consumer service. This service will be used by the rabbitMQ consumer
     * @param  string $name   name of the service (it will be prefixed by welp.batch)
     * @param  array $entity entity(found in configuration)
     * @return String         Name of the service
     */
    public function createConsumerService($name, $entity)
    {
        $definition = new Definition('Welp\BatchBundle\Consumer\AMQP\RabbitMQConsumer', array(new Reference('service_container'),$entity['entity_name'],$entity['form_name'],'%welp_batch.entity_manager%'));
        $this->container->setDefinition('welp_batch.'.$name, $definition);

        return 'welp_batch.'.$name;
    }

    /**
     * Symfony 2 converts '-' to '_' when defined in the configuration. This leads to problems when using x-ha-policy
     * parameter. So we revert the change for right configurations.
     *
     * Credit to php-amqplib/RabbitMqBundle for this method
     *
     * @param array $config
     *
     * @return array
     */
    private function normalizeArgumentKeys(array $config)
    {
        if (isset($config['arguments'])) {
            $arguments = $config['arguments'];
            // support for old configuration
            if (is_string($arguments)) {
                $arguments = $this->argumentsStringAsArray($arguments);
            }
            $newArguments = array();
            foreach ($arguments as $key => $value) {
                if (strstr($key, '_')) {
                    $key = str_replace('_', '-', $key);
                }
                $newArguments[$key] = $value;
            }
            $config['arguments'] = $newArguments;
        }
        return $config;
    }

    /**
     * Add proper dequeuer aware call
     *
     * Credit to php-amqplib/RabbitMqBundle for this method
     *
     * @param string $callback
     * @param string $name
     */
    protected function addDequeuerAwareCall($callback, $name)
    {
        if (!$this->container->has($callback)) {
            return;
        }
        $callbackDefinition = $this->container->findDefinition($callback);
        $refClass = new \ReflectionClass($callbackDefinition->getClass());
        if ($refClass->implementsInterface('OldSound\RabbitMqBundle\RabbitMq\DequeuerAwareInterface')) {
            $callbackDefinition->addMethodCall('setDequeuer', array(new Reference($name)));
        }
    }
}
