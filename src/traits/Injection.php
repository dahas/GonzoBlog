<?php declare(strict_types=1);

namespace Gonzo\Sources\traits;

use Gonzo\Sources\{Request, Response, Session};
use ReflectionClass;

trait Injection {

    public function triggerServiceInjection(Request $request, Response $response, Session $session)
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
                $this->$pName = new $service($request, $response, $session);
            }
        }
    }
}