<?php declare(strict_types=1);

namespace Gonzo\Sources;

use ReflectionClass;

class ServiceBase {

    public function __construct()
    {
        /**
         * Use of a ReflectionClass to inject Services assigned to Attributes.
         */
        $rc = new ReflectionClass(get_class($this));
        $properties = $rc->getProperties();
        foreach ($properties as $property) {
            $pName = $property->name;
            $attributes = $property->getAttributes();
            foreach ($attributes as $attribute) {
                $instance = $attribute->newInstance();
                $service = $instance->service;
                $options = $instance->getOptions();
                if ($options) {
                    $this->$pName = new $service(options: $options);
                } else {
                    $this->$pName = new $service();
                }
            }
        }
    }
}