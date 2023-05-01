<?php declare(strict_types=1);

namespace Gonzo\Sources\interfaces;

interface CookieInterface {

    public function set(): void;
    public function read(): array;
    public function write(array $data): void;
    public function destroy(): void;
}