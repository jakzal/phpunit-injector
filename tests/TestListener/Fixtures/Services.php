<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\Injector\Tests\TestListener\Fixtures;

class Services
{
    /**
     * @var Service1
     * @inject
     */
    private $service1;

    /**
     * @var Service2
     * @inject foo.service2
     */
    private $service2;

    public function getService1(): ?Service1
    {
        return $this->service1;
    }

    public function getService2(): ?Service2
    {
        return $this->service2;
    }
}
