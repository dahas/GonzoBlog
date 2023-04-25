<?php declare(strict_types=1);

namespace Gonzo\Sources\attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Route
{
    public function __construct(
        public string|array $path = "/",
        public string|array $method = "get"
    )
    {
    }

    public function getPaths(): array
    {
        return is_string($this->path) ? [$this->path] : $this->path;
    }

    public function getMethods(): array
    {
        return is_string($this->method) ? [$this->method] : $this->method;
    }
}