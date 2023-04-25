<?php

namespace Gonzo\Sources\attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Inject {
    
    public function __construct(
        public string $service = "",
        public array $options = []
    ) {
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}