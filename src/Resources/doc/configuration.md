# Configuration

In your `config.yml`:

## Full Configuration

```yaml
welp_batch:
    entity_manager: doctrine.orm.entity_manager #name of the entity manager service
    broker_type: rabbitmq #type of the broker
    broker_connection: default #name of the connection to the broker
    batch_entity: MyBundle\Entity\Batch #entity which extends the batch Model
    batch_results_folder: %kernel.root_dir%/../batch-results/ #Folder where we store the results files
        batch: MyBundle\Entity\Batch
        operation: MyBundle\Entity\Operation
    manage_entities: #Batchable entity
        need:
            entity_name: MyBundle\Entity\Need
            form_name: MyBundle\Form\NeedType
            batch_size: 10
            actions: ['create','delete']
        proposition:
            entity_name: MyBundle\Entity\Proposition
            form_name: MyBundle\Form\PropositionType
            batch_size: 10
            actions: ['create']
```

## Manage_entitites explanation

You can add as many entities as you like. Each of them must have the following attributes :

* entity_name : full name of the entity
* form_name : full name of the form corresponding to this entity. This form will be used to bind the given parameters to the new entity (see section TODO for more details)
* actions : array of action. At the moment, we only support create and delete
* batch_size : number of message to take from the queue at a time.
