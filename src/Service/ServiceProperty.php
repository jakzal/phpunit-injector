<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Service;

final class ServiceProperty
{
    /**
     * @var string
     */
    private $propertyName;

    /**
     * @var string
     */
    private $serviceId;

    public function __construct(string $propertyName, string $serviceId)
    {
        $this->propertyName = $propertyName;
        $this->serviceId = $serviceId;
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function getServiceId(): string
    {
        return $this->serviceId;
    }
}