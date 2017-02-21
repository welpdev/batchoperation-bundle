# REST Controller

## Principe

This bundle provide a create method.
When using our REST Controller, you have to call the POST /batches with a json like this

## Batches

### POST

Note : All operations have to have those 3 parameters (type, action, payload)

#### Route

POST /batches

#### Request parameters

```json
{
    "operations":[{
        "type":"need",
        "action":"create",
        "payload":{
            "place":{"searchedBy":"route","route":"Rue de Dunkerque","locality":"Paris","administrativeArealevel1":"Île-de-France","country":"France","name":"Rue de Dunkerque, Paris, France","latitude":48.8807242, "longitude":2.351648399999931},
            "description":"le test du batch du need2",
            "title":"le test du batch du need2",
            "category":11,
            "author":2
        }
    },{
        "type":"proposition",
        "action":"create",
        "payload":{
            "place":{"searchedBy":"route","route":"Rue de Dunkerque","locality":"Paris","administrativeArealevel1":"Île-de-France","country":"France","name":"Rue de Dunkerque, Paris, France","latitude":48.8807242, "longitude":2.351648399999931},
            "description":"le test du batch du need2",
            "title":"le test du batch du need2",
            "category":11,
            "author":2
        }
    }]
}
```

#### Response

```json
{
  "success": true,
  "batch": {
    "id": 43,
    "status": "welp_batch_pending",
    "operations": [
      {
        "type": "need",
        "action": "create",
        "payload": {
          "place": {
            "searchedBy": "route",
            "route": "Rue de Dunkerque",
            "locality": "Paris",
            "administrativeArealevel1": "Île-de-France",
            "country": "France",
            "name": "Rue de Dunkerque, Paris, France",
            "latitude": 48.8807242,
            "longitude": 2.3516483999999
          },
          "description": "le test du batch du need2",
          "title": "le test du batch du need2",
          "category": 11,
          "author": 2
        },
        "operationId": 1
      },
      {
        "type": "proposition",
        "action": "create",
        "payload": {
          "place": {
            "searchedBy": "route",
            "route": "Rue de Dunkerque",
            "locality": "Paris",
            "administrativeArealevel1": "Île-de-France",
            "country": "France",
            "name": "Rue de Dunkerque, Paris, France",
            "latitude": 48.8807242,
            "longitude": 2.3516483999999
          },
          "description": "le test du batch du need2",
          "title": "le test du batch du need2",
          "category": 11,
          "author": 2
        },
        "operationId": 2
      }
    ],
    "total_operations": 2,
    "total_executed_operations": 0,
    "created_at": "2017-02-21T18:26:46+0100",
    "updated_at": "2017-02-21T18:26:46+0100"
  }
}
```

### GET

#### Route

GET /batches/{id}

#### Response

```json
{
  "success": true,
  "batch": {
    "id": 43,
    "status": "welp_batch_pending",
    "operations": [
      {
        "type": "need",
        "action": "create",
        "payload": {
          "place": {
            "searchedBy": "route",
            "route": "Rue de Dunkerque",
            "locality": "Paris",
            "administrativeArealevel1": "Île-de-France",
            "country": "France",
            "name": "Rue de Dunkerque, Paris, France",
            "latitude": 48.8807242,
            "longitude": 2.3516483999999
          },
          "description": "le test du batch du need2",
          "title": "le test du batch du need2",
          "category": 11,
          "authovdfvdfvdfvvvdvdsvdfsdfr": 2
        },
        "operationId": 1
      },
      {
        "type": "need",
        "action": "create",
        "payload": {
          "place": {
            "searchedBy": "route",
            "route": "Rue de Dunkerque",
            "locality": "Paris",
            "administrativeArealevel1": "Île-de-France",
            "country": "France",
            "name": "Rue de Dunkerque, Paris, France",
            "latitude": 48.8807242,
            "longitude": 2.3516483999999
          },
          "description": "le test du batch du need2",
          "title": "le test du batch du need2",
          "category": 11,
          "author": 2
        },
        "operationId": 2
      }
    ],
    "total_operations": 2,
    "total_executed_operations": 0,
    "created_at": "2017-02-21T18:26:46+0100",
    "updated_at": "2017-02-21T18:26:46+0100"
  }
}
```

### DELETE

#### Route

DELETE /batches/{id}

#### Response

```json
{
  "success": true,
  "message": "batch deleted"
}
```

## Results

This route is dedicated to get the results from a given batch.

### GET

#### ROUTE

GET /batches/{id}/results

#### Response

```json
{
  "success": true,
  "file": "[{\"operationId\":1,\"error\":true,\"text\":\"Form Error, check yout payload\"},{\"operationId\":2,\"error\":false,\"message\":\"operation OK\"}]"
}
```

## Ressources

We also provide embedded documentation with NelmioApiDocBundle. If you use it, a Batches Section will automatically be imported to your documentation
