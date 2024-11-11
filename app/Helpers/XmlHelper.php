<?php

namespace App\Helpers;

use Spatie\ArrayToXml\ArrayToXml;

class XmlHelper
{
    public static function toXML(array $data, string $rootKey = '', ?string $encoding = null): string
    {
        return ArrayToXml::convert(
            array: $data,
            rootElement: $rootKey,
            xmlEncoding: $encoding
        );
    }

    public static function toObject(string $xmlString)
    {
        return simplexml_load_string($xmlString);
    }

    public static function toArray(string $xmlString): array
    {
        return json_decode(json_encode(self::toObject($xmlString)), true);
    }
}
