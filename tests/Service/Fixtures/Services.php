<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Tests\Service\Fixtures;

class Services
{
    /**
     * @var Service1
     */
    private $service1;

    /**
     * @var Service2
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