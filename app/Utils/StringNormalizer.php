<?php

namespace App\Utils;

use Symfony\Component\String\UnicodeString;

class StringNormalizer
{
    public static function normalize($string)
    {
        // Asegurar de que $string sea una cadena
        $string = $string ?? '';

        // Utilizar Symfony String para normalizar la cadena
        return (string) (new UnicodeString($string))->lower()->ascii();
    }

    public static function areEqualWithoutAccents($string1, $string2)
    {
        // Normalizar las dos cadenas y compararlas
        return static::normalize($string1) === static::normalize($string2);
    }
}