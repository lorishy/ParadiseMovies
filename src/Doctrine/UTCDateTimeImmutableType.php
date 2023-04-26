<?php 
// src/Doctrine/UTCDateTimeImmutableType.php
namespace App\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeImmutableType;

class UTCDateTimeImmutableType extends DateTimeImmutableType
{
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value !== null) {
            $value = $value->setTimezone(new \DateTimeZone('UTC'));
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $value = parent::convertToPHPValue($value, $platform);

        if ($value !== null) {
            $value = $value->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        }

        return $value;
    }
}