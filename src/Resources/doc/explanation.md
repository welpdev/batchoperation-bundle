# Explanation

The purpose is to manage a very long list of create/delete for you entity. For this we follow these steps

1. Configure consumers/producers queue
2. Call the batch_service with an array of operation
3. Create and save the batch
4. for each operations, create and save + send a message to the queue with the producer
5. The consumer take the message, and execute the given operation.

## Configure consumers/producers queue

We automatically create a pair of producers/consumers for each actions of a manage_entity. if you take this example

```yaml
    manage_entities: #Batchable entity
        need:
            entity_name: AppBundle\Entity\Need
            form_name: ApiBundle\Form\NeedType
            batch_size: 10
            actions: ['create','delete']
        proposition:
            entity_name: AppBundle\Entity\Proposition
            form_name: ApiBundle\Form\PropositionType
            batch_size: 10
            actions: ['create']
```

It will automatically create queues named

* welp.batch.need.create
* welp.batch.need.delete
* welp.batch.proposition.create

It will create consumer named

* old_sound_rabbit_mq.welp_batch.need.create_consumer
* old_sound_rabbit_mq.welp_batch.need.delete_consumer
* old_sound_rabbit_mq.welp_batch.proposition.create_consumer

It will create Producer named

* old_sound_rabbit_mq.welp_batch.need.create_producer
* old_sound_rabbit_mq.welp_batch.need.delete_producer
* old_sound_rabbit_mq.welp_batch.proposition.create_producer

It will create command named

* welp_batch:consumer:need.create
* welp_batch:consumer:need.delete
* welp_batch:consumer:proposition.create


## Call the batch_service

The service is called `welp_batch.batch_service`. It is the main entrance to this bundle. You can use it in your controller, or you can use our REST controller.

This service provide a create method. This method accept an array parameter, which contain all the operations you want to batch.
When using our REST Controller, you have to call the POST /batches with a json like this

```json
{
    "operations":[{
        "type":"need",
        "action":"create",
        "payload":{
            "place":{"searchedBy":"route","route":"Rue de Dunkerque","locality":"Paris","administrativeArealevel1":"Île-de-France","country":"France","name":"Rue de Dunkerque, Paris, France","latitude":48.8807242, "longitude":2.351648399999931},
            "description":"le test du batch du need2",
            "titldzdezdezdezddee":"le test du batch du need2",
            "category":11,
            "author":2
        }

    },{
        "type":"need",
        "action":"create",
        "payload":{
            "place":{"searchedBy":"route","route":"Rue de Dunkerque","locality":"Paris","administrativeArealevel1":"Île-de-France","country":"France","name":"Rue de Dunkerque, Paris, France","latitude":48.8807242, "longitude":2.351648399999931},
            "description":"le test du batch du need3",
            "titldzdezdezdezddee":"le test du batch du need3",
            "category":11,
            "author":2
        }
    }]
}
```


## Create and save the batch

With this example, it will add 2 operations to the queue `welp.batch.need.create`
When the service receive the request, it will create a batch.

Then, for each operations (two in the given example), it will create and save operations.
Those operations are transmit to the `welp_batch.producer` thanks to the `produce` method

## Publish to the broker

When the `produce($operation, $batchId, $type, $action)` method is called. The parameters will be add to an array formated like this :

```php
    $message = array();
    $message['batchId']=$batchId;
    $message['operationId']=$operation->getId();
    $message['operationPayload']=$operation->getPayload()
    $message['type']=$type;
    $message['action']=$action;
```

This message will then be publish to rabbitMQ, using the right queue, determine with the type of the entity and the action


## Execute actions

We automatically create consumers connected to all our queues.

There is two ways to launch a consumer :

* You can use the command `php app/console rabbitmq:consumer welp_batch.{entity}.{action}`. This will launch a php daemon.
* You can use the rabbitmq-cli-consumer (develop in GO language) to lauch this command `rabbitmq-cli-consumer -e "app/console welp_batch:consumer:{entity}.{action}" -c app/config/rabbitmq-cli-foo-create.conf -V`

You have to add the consumers to your whatever you use to launch command (for example, you can use supervisorD)

Consumers will get a message, and launch the `execute(AMQPMessage $msg)`.

The message will be unserialized, and the operation will be executed. Following the given action, the create or the delete method will be used.


## Update batch/operation status

During the process of the excution of the producers, we dispatch some events

* WELP_BATCH_OPERATION_STARTED
* WELP_BATCH_OPERATION_FINISHED
* WELP_BATCH_OPERATION_ERROR

Events are listened in the `OperationListener.php`.

When the `WELP_BATCH_OPERATION_STARTED` event is raised, we update the status of the operation, and if necessary, the status of the batch.

When the `WELP_BATCH_OPERATION_ERROR` event is raised, we update the status of the operation, and we add the code and the message of the error in the error array.

When the `WELP_BATCH_OPERATION_FINISHED` event is raised, we update the status of the operation. If all operations are finished, we update the status of the batch, and we merge all errors in the batch error array.

## Format Result

At the end of a batch, we automatically create a file names results-{batchId}-TodayDate. The file will be created in the given folder, the one you give in your configuration.
