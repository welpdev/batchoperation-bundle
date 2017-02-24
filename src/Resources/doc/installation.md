# Installation

## Install the bundle

Add the bundle to your project:

```bash
composer require batchOperation-bundle
```

Add `Welp\BatchBundle\WelpBatchBundle` to your `AppKernel.php`:

```php
$bundles = [
    // ...
    new Welp\BatchBundle\WelpBatchBundle(),
];
```

## Extends the models

In order to use this bundle, you have to extends our models : `Welp\BatchBundle\Model\Batch`

Example :

```php
/**
 * @ORM\Entity()
 * @ORM\Table(name="batch")
 * @ORM\HasLifecycleCallbacks
 */
class Batch extends BaseBatch
{

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Groups({"Default"})
     * @Serializer\Expose
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     * @Serializer\Groups({"Default"})
     * @Serializer\Expose
     */
    protected $status;

    /**
     * @var string
     *
     * @ORM\OneToMany(targetEntity="Operation", mappedBy="batch", cascade={"persist", "remove"})
     * @Serializer\Groups({"Default"})
     * @Serializer\Expose
     *
     */
    protected $operations;

    /**
     * @var integer
     *
     * @ORM\Column(name="total_operations", type="integer")
     * @Serializer\Groups({"Default"})
     * @Serializer\Expose
     */
    protected $totalOperations;

    /**
     * @var string
     *
     * @ORM\Column(name="total_executed_operations", type="integer")
     * @Serializer\Groups({"Default"})
     * @Serializer\Expose
     */
    protected $totalExecutedOperations;

    /**
     * @var string
     *
     * @ORM\Column(name="errors", type="array")
     * @Serializer\Groups({"Default"})
     * @Serializer\Expose
     */
    protected $errors;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Serializer\Groups({"Default"})
     * @Serializer\Expose
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime",nullable=true)
     * @Serializer\Groups({"Default"})
     * @Serializer\Expose
     */
    protected $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="started_at", type="datetime",nullable=true)
     * @Serializer\Groups({"Default"})
     * @Serializer\Expose
     */
    protected $startedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="finished_at", type="datetime",nullable=true)
     * @Serializer\Groups({"Default"})
     * @Serializer\Expose
     */
    protected $finishedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="group_name", type="string", length=255)
     * @Serializer\Groups({"Default"})
     * @Serializer\Expose
     */
    protected $group;
}
```

## Configuration

```yaml
welp_batch:
    entity_manager: doctrine.orm.entity_manager
    batch_entity:
        batch: MyBundle\Entity\Batch
```

take a look [here](configuration.md) for the full configuration of the bundle

## Consumer

We automatically create a producer for each queue. In order to launch them, you have two possibilities.

* You can use the command `php app/console rabbitmq:consumer welp_batch.{entity}.{action}`. This will launch a php daemon.
* You can use the rabbitmq-cli-consumer (develop in GO language) to lauch this command `rabbitmq-cli-consumer -e "app/console welp_batch:consumer:{entity}.{action}" -c app/config/rabbitmq-cli-foo-create.conf -V`

For example, rabbitlmq-cli-foo-create.conf

```
[rabbitmq]
host=localhost
port=5672/
username=user
password=pwd
vhost=vhost
queue=welp.batch.foo.create
compression=off

[logs]
error = /tmp/rabbitcli/error.log
info = /tmp/rabbitcli/info.log

```
