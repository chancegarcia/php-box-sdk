<?php

namespace Box\Model;

use Box\Logger\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

trait BaseModelTrait
{
    protected ?LoggerInterface $logger = null;

    /**
     * @return LoggerInterface|null
     */
    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface|null $logger
     */
    public function setLogger(?LoggerInterface $logger = null): void
    {
        $this->logger = $logger;
    }

    public function toClassVar(string $str): string
    {
        $aTokens = explode("_", $str);
        $sFirst = array_shift($aTokens);
        $aTokens = array_map('ucfirst', $aTokens);
        array_unshift($aTokens, $sFirst);

        return implode("", $aTokens);
    }

    public function toBoxVar(string $str): string
    {
        $aTokens = preg_split('/(?<=\\w)(?=[A-Z])/', $str);
        $sFirst = array_shift($aTokens);
        $aTokens = array_map('lcfirst', $aTokens);
        array_unshift($aTokens, $sFirst);

        return implode("_", $aTokens);
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
        // check if value is object or array and map
        // or maybe have a map array of properties/keys that call new classes to map to
        foreach ($aData as $k => $v)
        {
            $sClassProp = $this->toClassVar($k);
            $sSetterMethod = "set" . ucfirst($sClassProp);
            if (method_exists($this, $sSetterMethod))
            {
                $this->{$sSetterMethod}($v);
            }
            elseif (property_exists($this, $sClassProp))
            {
                $this->{$sClassProp} = $v;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isInt(mixed $number = null): bool
    {
        if (!is_numeric($number))
        {
            return false;
        }

        if (is_string($number) && str_contains($number, "."))
        {
            return false;
        }

        if (!is_int($number) && !is_string($number))
        {
            return false;
        }

        if (is_string($number) && !is_int((int)$number))
        {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function removeEmpty(array $haystack = []): array
    {
        foreach ($haystack as $k => $v)
        {
            if (is_array($v))
            {
                $haystack[$k] = $this->removeEmpty($v);
            }

            if (is_string($v))
            {
                $v = trim($v);
            }

            if (empty($v))
            {
                unset($haystack[$k]);
            }
        }

        return $haystack;
    }
}
