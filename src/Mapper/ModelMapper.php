<?php

namespace Box\Mapper;

class ModelMapper
{
    public static function toClassVar(string $str): string
    {
        $aTokens = explode("_", $str);
        $sFirst = array_shift($aTokens);
        $aTokens = array_map('ucfirst', $aTokens);
        array_unshift($aTokens, $sFirst);

        return implode("", $aTokens);
    }

    public static function toBoxVar(string $str): string
    {
        $aTokens = preg_split('/(?<=\\w)(?=[A-Z])/', $str);
        $sFirst = array_shift($aTokens);
        $aTokens = array_map('lcfirst', $aTokens);
        array_unshift($aTokens, $sFirst);

        return implode("_", $aTokens);
    }

    public static function mapBoxToClass(object $target, array|\stdClass $aData): void
    {
        (new Hydrator())->hydrate($target, $aData);
    }

    public static function isInt(mixed $number = null): bool
    {
        if (!is_numeric($number)) {
            return false;
        }

        if (is_string($number) && str_contains($number, ".")) {
            return false;
        }

        if (!is_int($number) && !is_string($number)) {
            return false;
        }

        if (is_string($number) && !is_int((int)$number)) {
            return false;
        }

        return true;
    }

    /**
     * @param array $haystack
     * @param bool $strict If true, preserves 0, "0", and false.
     * @return array
     */
    public static function removeEmpty(array $haystack = [], bool $strict = false): array
    {
        foreach ($haystack as $k => $v) {
            if (is_array($v)) {
                $haystack[$k] = self::removeEmpty($v, $strict);
            }

            if (is_string($v)) {
                $v = trim($v);
            }

            if ($strict) {
                if (null === $v || (is_string($v) && '' === $v) || (is_array($v) && empty($v))) {
                    unset($haystack[$k]);
                }
            } else {
                if (empty($v)) {
                    unset($haystack[$k]);
                }
            }
        }

        return $haystack;
    }
}
