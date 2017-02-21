# Usage

The purpose is to manage a very long list of create/delete for you entity. for this we use these steps

1. Configure consumers/producers queue
2. Call the batch_service with an array of operation
3. Create and save the batch
4. for each operations, create and save + send a message to the queue with the producer
5. The consumer take the message, and execute the given operation.

## Configure consumers/producers queue

We automatically create a couple of producers/consumers for each actions of a manage_entity. if you take this example

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

it will automatically create queues named
* welp.batch.need.create
* welp.batch.need.delete
* welp.batch.proposition.create

## Call the batch_service

The service is called `welp_batch.batch_service`. It is the main entrance to this bundle. You can use it in your controller, or you can use our REST controller.

This service provide a create method. This method accept an array parameter, which contain all the operations you want to batch.
When using our REST Controller, you have to call the POST /batches with a json like this

```json
{
    "operations":[{
        "type":"need",
        "action":"create",
        "place":{"searchedBy":"route","route":"Rue de Dunkerque","locality":"Paris","administrativeArealevel1":"Île-de-France","country":"France","name":"Rue de Dunkerque, Paris, France","latitude":48.8807242, "longitude":2.351648399999931},
        "description":"le test du batch du need2",
        "titldzdezdezdezddee":"le test du batch du need2",
        "category":11,
        "author":2
    },{
        "type":"need",
        "action":"create",
        "place":{"searchedBy":"route","route":"Rue de Dunkerque","locality":"Paris","administrativeArealevel1":"Île-de-France","country":"France","name":"Rue de Dunkerque, Paris, France","latitude":48.8807242, "longitude":2.351648399999931},
        "description":"le test du batch du need2",
        "titldzdezdezdezddee":"le test du batch du need2",
        "category":11,
        "author":2
    }]
}
```



## Create and save the batch

With this example, it will add 2 operations to the queue welp.batch.need.create
When the service receive the request, it will create a batch. Then, for each operations (two in the given example), it will create and save operations.
Those operations are transmit to the `welp_batch.producer` thanks to the `produce` method

## Publish to the broker

When the `produce($operation, $batchId,$type,$action )` method is called. The parameters will be add to an array formated like this :

```php
    $message = array();
    $message['batchId']=$batchId;
    $message['operationId']=$operation->getId();
    $message['type']=$type;
    $message['action']=$action;
```

This message will then be publish to rabbitMQ, using the right queue, determine with the type and the action


## Execute actions

We automatically create consumers connected to all our queues.
You have to add the consumers to your supervisord.

Consumers will get a message, and laucnh the `execute(AMQPMessage $msg)`.

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
