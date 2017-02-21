# Configuration

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

## manage_entitites explanation

You can add as many entities as you like. Each of them must have the following attributes :

* entity_name : full name of the entity
* form_name : full name of the form corresponding to this entity. This form will be used to bind the given parameters to the new entity (see section TODO for more details)
*  actions : array of action. At the moment, we only support create and delete
* batch_size : number of message to take from the queue at a time.
