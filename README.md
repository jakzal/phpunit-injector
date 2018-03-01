# PHPUnit Dependency Injection

Provides a PHPUnit listener to inject services from a PSR-11 dependency injection container to PHPUnit test cases.

Services are injected to test cases that implement `Zalas\PHPUnit\Injector\TestCase\ServiceContainerTestCase`
for any property tagged with `@inject`.

## Installation

```bash
composer require --dev zalas/phpunit-dependency-injection
```

## Configuration

Enable the service injector listener
in the [PHPUnit configuration file](https://phpunit.de/manual/current/en/appendixes.configuration.html):

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/6.5/phpunit.xsd">

    <!-- ... -->

    <listeners>
        <listener class="Zalas\PHPUnit\Injector\TestListener\ServiceInjectorListener" />
    </listeners>
</phpunit>
```

## Usage

To inject services using any PSR-11 service container, implement the `Zalas\PHPUnit\Injector\TestCase\ServiceContainerTestCase`
and tag selected properties with `@inject`:

```php
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Zalas\PHPUnit\Injector\TestCase\ServiceContainerTestCase;

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

The service is found by its type, or an id if it's given next to the `@inject` tag.

### Symfony

The simplest way to inject services from a Symfony service container is to extend the `Symfony\Bundle\FrameworkBundle\Test\KernelTestCase`
and include the `Zalas\PHPUnit\Injector\Symfony\TestCase\KernelServiceContainer` trait to get the default
`Zalas\PHPUnit\Injector\TestListener\ServiceContainerTestCase` implementation.

```php
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\SerializerInterface;
use Zalas\PHPUnit\Injector\Symfony\TestCase\KernelServiceContainer;
use Zalas\PHPUnit\Injector\TestListener\ServiceContainerTestCase;

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

To make this work the `Zalas\PHPUnit\Injector\Symfony\Compiler\ExposeServicesForTestsPass` needs to be
registered in test environment:

```php
use Zalas\PHPUnit\Injector\Symfony\Compiler\ExposeServicesForTestsPass;

class Kernel extends BaseKernel
{
    // ...

    protected function build(ContainerBuilder $container)
    {
        if ('test' === $this->getEnvironment()) {
            $container->addCompilerPass(new ExposeServicesForTestsPass());
        }
    }
}
```

The compiler pass makes sure that even private services are available to be used in tests.

## Contributing

Please read the [Contributing guide](CONTRIBUTING.md) to learn about contributing to this project.
Please note that this project is released with a [Contributor Code of Conduct](CODE_OF_CONDUCT.md).
By participating in this project you agree to abide by its terms.
