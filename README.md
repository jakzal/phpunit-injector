# PHPUnit Injector

[![Build Status](https://travis-ci.org/jakzal/phpunit-injector.svg?branch=master)](https://travis-ci.org/jakzal/phpunit-injector)

Provides a PHPUnit listener to inject services from a PSR-11 dependency injection container to PHPUnit test cases.

Services are injected to test cases that implement `Zalas\Injector\PHPUnit\TestCase\ServiceContainerTestCase`
to any property tagged with `@inject`.

[Symfony DependencyInjection component](https://github.com/symfony/dependency-injection) integration is also provided.

## Installation

### Composer

```bash
composer require --dev zalas/phpunit-injector
```

### Phar

The extension is also distributed as a PHAR, which can be downloaded from the most recent
[Github Release](https://github.com/jakzal/phpunit-injector/releases).

Put the extension in your PHPUnit extensions directory.
Remember to instruct PHPUnit to load extensions in your `phpunit.xml`:

```xml
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/7.0/phpunit.xsd"
         extensionsDirectory="tools/phpunit.d"
>
</phpunit>
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

The `createServiceContainer` method would be usually provided by a base test case or a trait.
In case of Symfony, such a trait is provided by this package (see the next section).

### Symfony Test Container (Symfony >= 4.1)

The `Zalas\Injector\PHPUnit\Symfony\TestCase\SymfonyTestContainer` trait provides
access to the test container ([introduced in Symfony 4.1](https://symfony.com/blog/new-in-symfony-4-1-simpler-service-testing)).
Including the trait in a test case implementing the `ServiceContainerTestCase` will make that services are injected
into annotated properties:

```php
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Zalas\Injector\PHPUnit\Symfony\TestCase\SymfonyTestContainer;
use Zalas\Injector\PHPUnit\TestCase\ServiceContainerTestCase;

class ServiceInjectorTest extends TestCase implements ServiceContainerTestCase
{
    use SymfonyTestContainer;

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

Note that `test` needs to be set to `true` in your test environment configuration for the framework bundle:

```yaml
framework:
    test: true
```

Even though services are automatically made private by Symfony, the test container makes them available in your tests.
Note that this only happens for private services that are actually used in your app (so are injected into
a public service, i.e. a controller). If a service is not injected anywhere, it's removed by the container compiler.

The kernel used to bootstrap the container is created in a similar way to the `KernelTestCase` known from the FrameworkBundle.
Similar environment variables are supported:

 * `KERNEL_CLASS` *required* - kernel class to instantiate to create the service container
 * `APP_ENV` default: test - kernel environment
 * `APP_DEBUG` default: false - kernel debug flag

These could for example be configured in `phpunit.xml`, or via [global variables](https://github.com/jakzal/phpunit-globals).

### Symfony Container (Symfony 3.4 & 4.0)

The `Zalas\Injector\PHPUnit\Symfony\TestCase\SymfonyContainer` trait gives access to the full Symfony Container
and can be used with any Symfony version.
Opposed to the Test Container approach for Symfony 4.1, this version provides access to each service even if it's
not used by your application anywhere and would normally be removed by the compiler.
This should be treated as a limitation rather than a feature.

```php
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Zalas\Injector\PHPUnit\Symfony\TestCase\SymfonyContainer;
use Zalas\Injector\PHPUnit\TestCase\ServiceContainerTestCase;

class ServiceInjectorTest extends TestCase implements ServiceContainerTestCase
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

Since the test container is not available until Symfony 4.1,
you'll also have to register the `Zalas\Injector\PHPUnit\Symfony\Compiler\ExposeServicesForTestsPass` compiler pass:

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

## Contributing

Please read the [Contributing guide](CONTRIBUTING.md) to learn about contributing to this project.
Please note that this project is released with a [Contributor Code of Conduct](CODE_OF_CONDUCT.md).
By participating in this project you agree to abide by its terms.
