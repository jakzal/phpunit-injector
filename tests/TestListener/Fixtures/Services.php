<?php
declare(strict_types=1);

namespace Zalas\Injector\PHPUnit\Tests\TestListener\Fixtures;

class Services
{
    /**
     * @inject
     */
    private Service1 $service1;

    /**
     * @inject foo.service2
     */
    private Service2 $service2;

    public function getService1(): ?Service1
    {
        return $this->service1;
    }

    public function getService2(): ?Service2
    {
        return $this->service2;
    }
}
