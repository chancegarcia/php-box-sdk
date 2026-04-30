<?php

namespace Box\Model;

use Box\Logger\LoggerAwareInterface;
use Box\Mapper\ModelMapper;
use Box\Trait\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

trait BaseModelTrait
{
    use LoggerAwareTrait;

    public function toClassVar(string $str): string
    {
        return ModelMapper::toClassVar($str);
    }

    public function toBoxVar(string $str): string
    {
        return ModelMapper::toBoxVar($str);
    }

    /**
     * @param array|\stdClass $aData
     */
    public function mapBoxToClass(array|\stdClass $aData): void
    {
        if ($this->getLogger() instanceof LoggerInterface)
        {
            $this->getLogger()->debug('map data: ' . var_export($aData, true), array(__METHOD__ . ":" . __LINE__));
        }

        ModelMapper::mapBoxToClass($this, $aData);
    }

    /**
     * {@inheritdoc}
     */
    public function isInt(mixed $number = null): bool
    {
        return ModelMapper::isInt($number);
    }

    /**
     * {@inheritdoc}
     */
    public function removeEmpty(array $haystack = []): array
    {
        return ModelMapper::removeEmpty($haystack);
    }
}
