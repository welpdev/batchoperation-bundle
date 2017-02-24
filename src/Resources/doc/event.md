# Event

We raise 3 events during the processing of the operations.

* WelpBatchEvent::WELP_BATCH_OPERATION_STARTED
* WelpBatchEvent::WELP_BATCH_OPERATION_FINISHED
* WelpBatchEvent::WELP_BATCH_OPERATION_ERROR
* WelpBatchEvent::WELP_BATCH_ENTITY_CREATED
* WelpBatchEvent::WELP_BATCH_ENTITY_DELETED

## Where to find Events ?

All our events are declared in the `Welp\BatchBundle\WelpBatchEvent`

## When are they raised ?

### WelpBatchEvent::WELP_BATCH_OPERATION_STARTED

This one is raised at the beginning of an operation.

### WelpBatchEvent::WELP_BATCH_OPERATION_FINISHED

This one is raised at the end of an operation (even if an error is encounter in the operation)

### WelpBatchEvent::WELP_BATCH_OPERATION_ERROR

This one is raised when a action launch by an operation throw an error. Possible error can be :

* Entity not found (the id given in the payload doesn't match any id the database)
* Form error (the payload given to be bind to the form is nor correct => Extra Fields, required field not given)

### WelpBatchEvent::WELP_BATCH_ENTITY_CREATED

This one is raised when a entity is created by a consumer

### WelpBatchEvent::WELP_BATCH_ENTITY_DELETED

This one is raised when a entity is deleted by a consumer

## Event Listener of our bundle

Our event listener is here : `Welp\BatchBundle\EventListener\OperationListener`

```php
return [
    WelpBatchEvent::WELP_BATCH_OPERATION_STARTED => 'startOperation',
    WelpBatchEvent::WELP_BATCH_OPERATION_FINISHED => 'finishOperation',
    WelpBatchEvent::WELP_BATCH_OPERATION_ERROR => 'errorOperation',
];
```

### WelpBatchEvent::WELP_BATCH_OPERATION_STARTED

When this event is raised, if the batch is not started, we change the status of the batch

### WelpBatchEvent::WELP_BATCH_OPERATION_FINISHED

When this event is raised, we update the Status of the batch. If all operations are finished, we create the file containing the results

### WelpBatchEvent::WELP_BATCH_OPERATION_ERROR

When this event is raised, we update the status of the batch, and we add an error to the errors array of the batch. This array will then be used for formatting the result file.


## Event Entity

All our event are in the namespace `Welp\BatchBundle\Event`

### BatchEvent

This event contain the batch, and the operationId. It is use when the `WelpBatchEvent::WELP_BATCH_OPERATION_STARTED` and the `WelpBatchEvent::WELP_BATCH_OPERATION_FINISHED` are dispatch.

You can access those properties with our getters

```php
public function test(BatchEvent $event)
{
    $batch = $event->getBatch();
    $operationId = $event->getOperationId();
}
```

### BatchErrorEvent

This event contain the batch, the error and the operationId. It is use when the `WelpBatchEvent::WELP_BATCH_OPERATION_ERROR` is dispatch

You can access those properties with our getters

```php
public function test(BatchErrorEvent $event)
{
    $batch = $event->getBatch();
    $operationId = $event->getOperationId();
    $error = $event->getError();
}
```

### BatchEntityCreatedEvent

This event contain the created entity and the className of the created entity. It is use when the `WelpBatchEvent::WELP_BATCH_ENTITY_CREATED` is dispatch

You can access those properties with our getters

```php
public function test(BatchEntityCreatedEvent $event)
{
    $entity = $event->getEntity();
    $className = $event->getClassName();
}
```

### BatchEntityDeletedEvent

This event contain the created entity and the className of the created entity. It is use when the `WelpBatchEvent::WELP_BATCH_ENTITY_DELETED` is dispatch

You can access those properties with our getters

```php
public function test(BatchEntityDeletedEvent $event)
{
    $entity = $event->getEntity();
    $className = $event->getClassName();
}
```

## Synchronisation with your application

If you want to launch some custom action when a entity is created/deleted, we advise you to listen to the `WelpBatchEvent::WELP_BATCH_ENTITY_CREATED` and the `WelpBatchEvent::WELP_BATCH_ENTITY_DELETED` events.

You will be able to get the entity, and made custom actions on it.
