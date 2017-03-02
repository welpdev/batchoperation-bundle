# WelpBatchBundle

[![Build Status](https://travis-ci.org/welpdev/batchoperation-bundle.svg?branch=master)](https://travis-ci.org/welpdev/batchoperation-bundle)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/welpdev/batchoperation-bundle/master/LICENSE)
[![Documentation](https://img.shields.io/badge/documentation-gh--pages-blue.svg)](https://welpdev.github.io/batchoperation-bundle)

Symfony Bundle to manage batch operation. You can choose whatever broker you want (default is RabbitMq)

## Features

- [x] Support RabbitMq
    - [x] Automatically create producer
    - [x] Automatically create consumer
    - [x] Automatically create queue
    - [x] Support create/Delete Action
    - [x] Dispatch Event with the entity created/deleted
    - [ ] Support custom action
    - [ ] Exploit Batch Size
- [x] Manage Batch, batch Status, Batch Event
- [x] Rest Controller
- [ ] Support Redis as a broker
- [ ] Support other Broker
- [ ] Delete Batch and operations from queue, and revert actions

## Quickstart

### Add the bundle to your project

```bash
composer require welp/batch-operation-bundle
```

Add `Welp\BatchBundle\WelpBatchBundle` to your `AppKernel.php`:

```php
$bundles = [
    // ...
    new Welp\BatchBundle\WelpBatchBundle(),
];
```
### Extends the BatchModel

Create a entity which extends out `Welp\BatchBundle\Model\BatchModel`

```php
use Doctrine\ORM\Mapping as ORM;
use Welp\BatchBundle\Model\Batch as BaseBatch;

/**
 * @ORM\Entity()
 * @ORM\Table(name="batch")
 * @ORM\HasLifecycleCallbacks
 */
class Batch extends BaseBatch{
    ...
}
```

### Minimal Configuration

```yaml
welp_batch:
    entity_manager: doctrine.orm.entity_manager
    batch_entity:
        batch: MyBundle\Entity\Batch
```

## Full Documentation

you can find the full documentation at <https://welpdev.github.io/batchoperation-bundle/>
