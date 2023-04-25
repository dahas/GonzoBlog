<?php

namespace Gonzo\Sources\interfaces;

interface ServiceInterface
{
    /**
     * Injects a service via an Attribute
     */
    public function injectServices(): void;
}