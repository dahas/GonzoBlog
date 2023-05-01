<?php declare(strict_types=1);

namespace Gonzo\Sources\interfaces;

interface SessionInterface {

    public function get(string $name): mixed;
    public function set(string $name, mixed $value): void;
    public function unset(string $name): void;
    public function issetRedirect(): bool;
    public function getRedirect(): string;
    public function setRedirect(string $route): void;
    public function unsetRedirect(): void;
    public function issetTemp(): bool;
    public function getTempRoute(): string;
    public function getTempData(string $route): mixed;
    public function setTempData(string $route, mixed $data): void;
    public function unsetTemp(): void;
    public function destroy(): void;
}