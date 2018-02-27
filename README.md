# PHPUnit Dependency Injection

Provides a PHPUnit listener to inject services from the Symfony dependency injection container to PHPUnit test cases.

Services are injected for any service tagged with `@inject`.

## Installation

```bash
composer require zalas/phpunit-dependency-injection
```

## Configuration

Enable the service injector listener
in the [PHPUnit configuration file](https://phpunit.de/manual/current/en/appendixes.configuration.html):

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/6.5/phpunit.xsd">

    <!-- ... -->

    <listeners>
        <listener class="Zalas\PHPUnit\DependencyInjection\TestListener\ServiceInjectorListener" />
    </listeners>
</phpunit>
```

## Usage

Tag selected properties with `@inject` to get services injected:

```php
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Zalas\PHPUnit\DependencyInjection\TestCase\ServiceContainerTestCase;

class ServiceInjectorTest extends TestCase implements ServiceContainerTestCase
{
    /**
     * @var SerializerInterface
     * @inject
     */
    private $serializer;

    /**
     * @var LoggerInterface
     * @inject logger
     */
    private $logger;

    public function testThatServicesAreInjected()
    {
        $this->assertInstanceOf(SerializerInterface::class, $this->serializer, 'The service is injectd by its type');
        $this->assertInstanceOf(LoggerInterface::class, $this->logger, 'The service is injected by its id');
    }

    public function createServiceContainer(): ContainerInterface
    {
        // create a service container here
    }
}
```

The service is found by its type, or an id if it's given.

### Symfony


```php
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\SerializerInterface;
use Zalas\PHPUnit\DependencyInjection\Symfony\TestCase\KernelServiceContainer;
use Zalas\PHPUnit\DependencyInjection\TestListener\ServiceContainerTestCase;

class ServiceInjectorTest extends KernelTestCase implements ServiceContainerTestCase
{
    use KernelServiceContainer;

    /**
     * @var SerializerInterface
     * @inject
     */
    private $serializer;

    /**
     * @var LoggerInterface
     * @inject logger
     */
    private $logger;

    public function testThatServicesAreInjected()
    {
        $this->assertInstanceOf(SerializerInterface::class, $this->serializer, 'The service is injectd by its type');
        $this->assertInstanceOf(LoggerInterface::class, $this->logger, 'The service is injected by its id');
    }
}
```
