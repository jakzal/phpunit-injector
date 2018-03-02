<?php
declare(strict_types=1);

namespace Zalas\Injector\PHPUnit\TestListener;

use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestListenerDefaultImplementation;
use Zalas\Injector\Factory\DefaultExtractorFactory;
use Zalas\Injector\PHPUnit\TestCase\ServiceContainerTestCase;
use Zalas\Injector\Service\Injector;

class ServiceInjectorListener implements TestListener
{
    use TestListenerDefaultImplementation;

    public function startTest(Test $test): void
    {
        if ($test instanceof ServiceContainerTestCase) {
            $injector = new Injector(new TestCaseContainerFactory($test), new DefaultExtractorFactory());
            $injector->inject($test);
        }
    }
}
