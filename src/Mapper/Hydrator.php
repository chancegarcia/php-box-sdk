<?php

namespace Box\Mapper;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;
use stdClass;

class Hydrator
{
    /**
     * @param class-string<T>|T $target
     * @param array|stdClass $data
     *
     * @throws ReflectionException
     * @return T
     *
     * @template T of object
     */
    public function hydrate(string|object $target, array|stdClass $data): object
    {
        if (is_string($target)) {
            $target = new $target();
        }

        $data = (array) $data;

        foreach ($data as $key => $value) {
            $camelKey = ModelMapper::toClassVar($key);
            $setter = 'set' . ucfirst($camelKey);

            if (method_exists($target, $setter)) {
                $reflection = new ReflectionClass($target);
                if ($reflection->hasMethod($setter) && $reflection->getMethod($setter)->isPublic()) {
                    $this->hydrateViaSetter($target, $setter, $camelKey, $value);
                    continue;
                }
            }

            if (property_exists($target, $camelKey)) {
                $this->hydrateViaProperty($target, $camelKey, $value);
            }
        }

        return $target;
    }

    private function hydrateViaSetter(object $target, string $setter, string $propertyName, mixed $value): void
    {
        $reflection = new ReflectionClass($target);
        $method = $reflection->getMethod($setter);
        $parameters = $method->getParameters();

        if (count($parameters) === 0) {
            $target->$setter($value);
            return;
        }

        $type = $parameters[0]->getType();
        $hydratedValue = $this->hydrateValue($type, $value, $target, $propertyName);

        if ($hydratedValue === null && $value !== null && $this->isEnumResolutionFailure($type)) {
            return;
        }

        $target->$setter($hydratedValue);
    }

    private function isEnumResolutionFailure(?ReflectionType $type): bool
    {
        if ($type instanceof ReflectionNamedType && !$type->isBuiltin() && enum_exists($type->getName())) {
            return true;
        }

        return false;
    }

    private function hydrateViaProperty(object $target, string $propertyName, mixed $value): void
    {
        $reflectionProperty = new ReflectionProperty($target, $propertyName);
        $type = $reflectionProperty->getType();

        $hydratedValue = $this->hydrateValue($type, $value, $target, $propertyName);
        $reflectionProperty->setValue($target, $hydratedValue);
    }

    private function hydrateValue(?ReflectionType $type, mixed $value, object $target, string $propertyName): mixed
    {
        if ($value === null && $type?->allowsNull()) {
            return null;
        }

        if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
            $className = $type->getName();

            if (
                is_subclass_of($className, Collection::class) ||
                $className === Collection::class ||
                $className === ArrayCollection::class
            ) {
                return $this->hydrateCollection($className, $value, $target, $propertyName);
            }

            if (is_array($value) || $value instanceof stdClass) {
                return $this->hydrate($className, $value);
            }

            if ($className === DateTimeImmutable::class && is_string($value)) {
                try {
                    return new DateTimeImmutable($value);
                } catch (Exception) {
                    return $value;
                }
            }

            if (is_scalar($value) && enum_exists($className)) {
                return $className::tryFrom($value);
            }
        }

        if ($type instanceof ReflectionUnionType) {
            foreach ($type->getTypes() as $unionType) {
                if ($unionType instanceof ReflectionNamedType && $unionType->isBuiltin() && $unionType->getName() === 'array' && is_array($value)) {
                    return $value;
                }
            }

            foreach ($type->getTypes() as $unionType) {
                if ($unionType instanceof ReflectionNamedType && !$unionType->isBuiltin()) {
                    $className = $unionType->getName();
                    if (is_array($value) || $value instanceof stdClass) {
                        return $this->hydrate($className, $value);
                    }
                }
            }
        }

        return $value;
    }

    private function hydrateCollection(
        string $collectionClass,
        mixed $value,
        object $target,
        string $propertyName
    ): Collection {
        $items = is_array($value) ? $value : [$value];
        $isGeneric = $collectionClass === Collection::class || $collectionClass === ArrayCollection::class;
        $collection = ($isGeneric)
            ? new ArrayCollection()
            : new $collectionClass();

        $itemType = $this->inferItemType($target, $propertyName);

        foreach ($items as $item) {
            if ($itemType && (is_array($item) || $item instanceof stdClass)) {
                $collection->add($this->hydrate($itemType, $item));
            } else {
                $collection->add($item);
            }
        }

        return $collection;
    }

    private function inferItemType(object $target, string $propertyName): ?string
    {
        $reflection = new ReflectionClass($target);

        // 1. Check PHPDoc on property
        if ($reflection->hasProperty($propertyName)) {
            $prop = $reflection->getProperty($propertyName);
            $doc = $prop->getDocComment();
            if ($doc && preg_match('/@var\s+([\\\\A-Za-z0-9_]+)\[\]|Collection<([\\\\A-Za-z0-9_]+)>/', $doc, $matches)) {
                $type = $matches[1] ?: $matches[2];
                return $this->resolveType($type, $reflection);
            }
        }

        // 2. Check PHPDoc on setter
        $setter = 'set' . ucfirst($propertyName);
        if ($reflection->hasMethod($setter)) {
            $method = $reflection->getMethod($setter);
            $doc = $method->getDocComment();
            if ($doc && preg_match('/@param\s+([\\\\A-Za-z0-9_]+)\[\]|Collection<([\\\\A-Za-z0-9_]+)>/', $doc, $matches)) {
                $type = $matches[1] ?: $matches[2];
                return $this->resolveType($type, $reflection);
            }
        }

        return null;
    }

    private function resolveType(string $type, ReflectionClass $context): string
    {
        if (str_starts_with($type, '\\')) {
            return ltrim($type, '\\');
        }

        $namespace = $context->getNamespaceName();
        if ($namespace && class_exists($namespace . '\\' . $type)) {
            return $namespace . '\\' . $type;
        }

        if (class_exists($type)) {
            return $type;
        }

        return $type;
    }
}
