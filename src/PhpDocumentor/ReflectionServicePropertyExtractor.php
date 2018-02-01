<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\PhpDocumentor;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\ContextFactory;
use Zalas\PHPUnit\DependencyInjection\Service\Foo;
use Zalas\PHPUnit\DependencyInjection\Service\MissingServiceIdException;
use Zalas\PHPUnit\DependencyInjection\Service\ServiceIdMissing;
use Zalas\PHPUnit\DependencyInjection\Service\ServiceProperty;
use Zalas\PHPUnit\DependencyInjection\Service\ServicePropertyExtractor;

final class ReflectionServicePropertyExtractor implements ServicePropertyExtractor
{
    /**
     * {@inheritdoc}
     */
    public function extract(string $class): array
    {
        $classReflection = new \ReflectionClass($class);
        $docBlockFactory = DocBlockFactory::createInstance(['inject' => Inject::class]);
        $context = (new ContextFactory())->createFromReflector($classReflection);

        return array_filter(array_map(function (\ReflectionProperty $propertyReflection) use ($docBlockFactory, $context) {
            return $this->createServiceProperty($propertyReflection, $docBlockFactory, $context);
        }, $classReflection->getProperties()));
    }

    private function createServiceProperty(\ReflectionProperty $propertyReflection, DocBlockFactory $docBlockFactory, Context $context): ?ServiceProperty
    {
        if (!$propertyReflection->getDocComment()) {
            return null;
        }

        $docBlock = $docBlockFactory->create($propertyReflection, $context);
        $inject = $this->getFirstTag($docBlock, 'inject');

        if (!$inject instanceof Inject) {
            return null;
        }

        $serviceId = $this->getServiceId((string) $inject, $docBlock);

        if (empty($serviceId)) {
            throw new MissingServiceIdException($propertyReflection->getDeclaringClass()->getName(), $propertyReflection->getName());
        }

        return new ServiceProperty($propertyReflection->getName(), $serviceId);
    }

    private function getServiceId(string $injectId, DocBlock $docBlock): ?string
    {
        return $injectId ? $injectId : $this->extractType($docBlock);
    }

    private function extractType(DocBlock $docBlock): ?string
    {
        $var = $this->getFirstTag($docBlock, 'var');

        return $var instanceof Var_ ? ltrim((string)$var->getType(), '\\') : null;
    }

    private function getFirstTag(DocBlock $docBlock, string $name): ?Tag
    {
        $tags = $docBlock->getTagsByName($name);

        return isset($tags[0]) ? $tags[0] : null;
    }
}