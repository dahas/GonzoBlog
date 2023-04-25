<?php

namespace Gonzo\Sources;

use Gonzo\Sources\interfaces\CookieInterface;

class Cookie implements CookieInterface {

    private array $data;

    public function __construct(
        private string $name,
        private int $expires = 0,
        private string $path = "/"
    ) {

    }

    /**
     */
    public function set(): void
    {
        setcookie($this->name, "", $this->expires, $this->path);
    }

    /**
     * @return array
     */
    public function read(): array
    {
        return $_COOKIE[$this->name];
    }

    /**
     */
    public function write(array $data): void
    {
        setcookie("{$this->name}[three]", "cookiethree");
        setcookie("{$this->name}[two]", "cookietwo");
        setcookie("{$this->name}[one]", "cookieone");
    }

    /**
     */
    public function destroy(): void
    {
    }
}