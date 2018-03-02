# PHPUnit Injector

[![Build Status](https://travis-ci.org/jakzal/phpunit-injector.svg?branch=master)](https://travis-ci.org/jakzal/phpunit-injector)

Provides a PHPUnit listener to inject services from a PSR-11 dependency injection container to PHPUnit test cases.

Services are injected to test cases that implement `Zalas\Injector\PHPUnit\TestCase\ServiceContainerTestCase`
to any property tagged with `@inject`.

[Symfony DependencyInjection component](https://github.com/symfony/dependency-injection) integration is also provided.

## Installation

```bash
composer require --dev zalas/phpunit-injector
```

## Configuration

Enable the service injector listener
in the [PHPUnit configuration file](https://phpunit.de/manual/current/en/appendixes.configuration.html):

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/7.0/phpunit.xsd">

    <!-- ... -->

    <listeners>
        <listener class="Zalas\Injector\PHPUnit\TestListener\ServiceInjectorListener" />
    </listeners>
</phpunit>
```

## Usage

To inject services using any PSR-11 service container, implement the `Zalas\Injector\PHPUnit\TestCase\ServiceContainerTestCase`
and tag selected properties with `@inject`:

```php
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Zalas\Injector\PHPUnit\TestCase\ServiceContainerTestCase;

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

The service is found by its type, or an id if it's given in the `@inject` tag.

### Symfony

The simplest way to inject services from a Symfony service container is to include
the `Zalas\Injector\PHPUnit\Symfony\TestCase\SymfonyContainer` trait to get the default
`Zalas\Injector\PHPUnit\TestListener\ServiceContainerTestCase` implementation:

```php
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Serializer\SerializerInterface;
use Zalas\Injector\PHPUnit\Symfony\TestCase\SymfonyContainer;
use Zalas\Injector\PHPUnit\TestListener\ServiceContainerTestCase;

class ServiceInjectorTest extends KernelTestCase implements ServiceContainerTestCase
{
    use SymfonyContainer;

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

To make this work the `Zalas\Injector\PHPUnit\Symfony\Compiler\ExposeServicesForTestsPass` needs to be
registered in test environment:

```php
use Zalas\Injector\PHPUnit\Symfony\Compiler\ExposeServicesForTestsPass;

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

The kernel is created in a similar way to the `KernelTestCase` known from the FrameworkBundle.
The same environment variables are supported:

 * `KERNEL_CLASS` *required* - kernel class to instantiate to create the service container
 * `APP_ENV` default: test - kernel environment
 * `APP_DEBUG` default: false - kernel debug flag

## Contributing

Please read the [Contributing guide](CONTRIBUTING.md) to learn about contributing to this project.
Please note that this project is released with a [Contributor Code of Conduct](CODE_OF_CONDUCT.md).
By participating in this project you agree to abide by its terms.
