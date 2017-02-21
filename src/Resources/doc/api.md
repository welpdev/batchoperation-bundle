# REST Controller

## Principe

This bundle provide a create method.
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

## Required parameters

For each operation, you have to give the following required parameters are
* Type => it must be a manage_entity
* Action => create/delete. it must be in the action array in your manage_entity
* Remainning parameters => contains all the data you need to create/delete an entity

## Available routes

* Batch : GET /batches/{id}, POST /batches, DELETE /batches/{id}
* Operation : GET /operations/{id}

For more detailled options for these routes, you can use nelmioApiDocBundle. our routes are commented, and you will find the documentation.
