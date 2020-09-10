<?php

namespace App\Tests\Helper;

use ReflectionClass;
use ReflectionException;

/**
 * Trait InvisiblePropertiesTrait
 * @package App\Tests\Helper
 */
trait InvisiblePropertiesTrait
{
    /**
     * @param string $propertyName
     * @param object $object
     * @return mixed
     * @throws ReflectionException
     */
    public function getInvisibleProperty(string $propertyName, object $object)
    {
        $objectReflection = new ReflectionClass($object);
        $property = $objectReflection->getProperty($propertyName);
        $property->setAccessible(true);
        return $property->getValue($object);
    }
}
