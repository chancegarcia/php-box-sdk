<?php

namespace Box\Model;

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
}
