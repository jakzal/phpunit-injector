<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Service;

final class ServiceProperty
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $propertyName;

    /**
     * @var string
     */
    private $serviceId;

    /**
     * @throws MissingServicePropertyException
     */
    public function __construct(string $className, string $propertyName, string $serviceId)
    {
        if (!property_exists($className, $propertyName)) {
            throw new MissingServicePropertyException($className, $propertyName);
        }

        $this->propertyName = $propertyName;
        $this->serviceId = $serviceId;
        $this->className = $className;
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function getServiceId(): string
    {
        return $this->serviceId;
    }

    public function getClassName(): string
    {
        return $this->className;
    }
}