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
}
