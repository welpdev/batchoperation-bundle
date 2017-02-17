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

        /*$container->setParameter('welp_batch.rabbitmq_producer_service_name', $config['rabbitmq_producer_service_name']);
        $container->setParameter('welp_batch.rabbitmq_producer_routing_keys', $config['rabbitmq_producer_routing_keys']);
        $container->setParameter('welp_batch.rabbitmq_producer_routing_keys', $config['rabbitmq_producer_routing_keys']);
        $container->setParameter('welp_batch.rabbitmq_producer_routing_keys', $config['rabbitmq_producer_routing_keys']);*/

        $container->setParameter('welp_batch.entity_manager', $config['entity_manager']);
        $container->setParameter('welp_batch.broker_type', $config['broker_type']);
        $container->setParameter('welp_batch.batch_entity.batch', $config['batch_entity']['batch']);
        $container->setParameter('welp_batch.batch_entity.operation', $config['batch_entity']['operation']);
        $container->setParameter('welp_batch.broker_connection', $config['broker_connection']);


        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load($config['broker_type'].'.yml');
        $loader->load('managers.yml');

        if ($config['broker_type'] == 'rabbitmq') {
            $this->loadRMQProducerDynamically($config['manage_entities'], $config['broker_connection']);
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
     * Symfony 2 converts '-' to '_' when defined in the configuration. This leads to problems when using x-ha-policy
     * parameter. So we revert the change for right configurations.
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
}
