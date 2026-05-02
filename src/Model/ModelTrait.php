<?php

namespace Box\Model;

use Box\Exception\BoxException;
use Box\Http\Response\BoxResponseInterface;
use Box\Mapper\ModelMapper;
use Box\Trait\BoxLoggerTrait;
use Psr\Log\LoggerInterface;

trait ModelTrait
{
    use BoxLoggerTrait;

    public function classArray(): array
    {
        $aModel = get_object_vars($this);
        $aArray = [];

        foreach ($aModel as $k => $v) {
            $sKey = $this->toBoxVar($k);
            $aArray[ $sKey ] = $v;
        }

        return $aArray;
    }

    public function toBoxArray(): array
    {
        $arr = $this->classArray();

        return ModelMapper::removeEmpty($arr, true);
    }

    /**
     * @return LoggerInterface|null
     */
    abstract public function getLogger(): ?LoggerInterface;

    public function validateClass(string $class, string $classType): bool
    {
        if (!class_exists($class)) {
            throw new BoxException("Unable to find class", BoxException::UNKNOWN_CLASS);
        }

        if (!is_subclass_of($class, $classType) && $class !== $classType) {
            throw new BoxException("Invalid Connection Class", BoxException::INVALID_CLASS_TYPE);
        }

        return true;
    }

    /**
     * @param array $params
     * @param string $numericPrefix
     *
     * @return string
     */
    public function buildQuery(array $params, string $numericPrefix = ''): string
    {
        return http_build_query($params, $numericPrefix, '&', PHP_QUERY_RFC3986);
    }

    public function getNewClass(?string $className = null, mixed $classConstructorOptions = null): mixed
    {
        if (null === $className) {
            throw new BoxException('undefined class name', BoxException::INVALID_INPUT);
        }

        $sMethod = 'get' . ucfirst($className) . 'Class';

        $sClass = $this->$sMethod();

        $instance = new $sClass($classConstructorOptions);
        if ($this->logger && method_exists($instance, 'setLogger')) {
            $instance->setLogger($this->logger);
        }

        return $instance;
    }
}
