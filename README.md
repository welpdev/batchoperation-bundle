# icalBundle

Symfony Bundle to manage batch operation. You can choose whatever broker you want ( default is RabbitMq)

## Setup

Add bundle to your project:

```bash
composer require batchOperation-bundle
```

Add `Welp\BatchBundle\WelpBatchBundle` to your `AppKernel.php`:

```php
$bundles = [
    // ...
    new Welp\BatchBundle\WelpBatchBundle()(),
];
```

## Configuration

In your `config.yml`:

```yaml
welp_batch:
    entity_manager: doctrine.orm.entity_manager #name of the entity manager service
    broker_type: rabbitmq #type of the broker
    broker_connection: default #name of the connection to the broker
    batch_entity: #entity which extends the batc/operation Model
        batch: AppBundle\Entity\Batch
        operation: AppBundle\Entity\Operation
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

### manage_entitites explanation

you can add as many entities as you like. Each of them must have the following attributes :
* entity_name : full name of the entity
* form_name : full name of the form corresponding to this entity. This form will be used to bind the given parameters to the new entity (see section TODO for more details)
*  actions : array of action. At the moment, we onl support create and delete
* batch_size : number of message to take from the queue at a time.

## Rest Controller

We provide two rest controllers ( write we FOSRestBundle).
you can import these controllers in your app like this

```yaml
welp_batch_api:
    resource: "@WelpBatchBundle/Resources/config/routing.yml"
    prefix:   /api
```
* Batch : get, post, delete
* Operation : get

Note : If you use nelmioApiDocBundle, docs will automatically be generated.

## Principe

The purpose is to manage a very long list of create/delete for you entity. for this we use these steps

1. Configure consumers/producers queue
2. Call the batch_service with an array of operation
3. Create and save the batch
4. for each operations, create and save + send a message to the queue with the producer
5. The consumer take the message, and execute the given operation.

### Configure consumers/producers queue

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

it will automatically queues named
* welp.batch.need.create
* welp.batch.need.delete
* welp.batch.proposition.create
