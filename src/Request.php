<?php

namespace Gonzo\Sources;

use Gonzo\Sources\traits\Utils;

final class Request {

    use Utils;

    private string $uri;
    private string $referer;
    private string $method;
    private string $route;
    private array $segments = [];
    private array $data = [];

    public function __construct()
    {
        $this->uri = $_SERVER['REQUEST_URI'] ?? "";
        $this->referer = $_SERVER['HTTP_REFERER'] ?? "/";
        $this->method = strtolower($_SERVER['REQUEST_METHOD'] ?? "get");
        $this->parseUri($this->uri);
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getReferer(): string
    {
        return $this->referer;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getSegments(): array
    {
        return $this->segments;
    }

    public function getSegment(int $num): mixed
    {
        return $this->segments[$num] ?? null;
    }

    public function setData(array $data): void
    {
        $data = array_merge($this->data, $data);
        $this->data = $this->sanitizeRequest($data);
    }

    public function getData(): array
    {
        return $this->data;
    }

    private function parseUri(string $uri): void
    {
        $arrUri = parse_url($uri);
        $path = $arrUri['path'];
        $query = $arrUri['query'] ?? "";

        $this->route = $path;
        
        $this->segments = explode("/", substr($path, 1));

        $qArr = [];
        if ($query) {
            parse_str($query, $qArr);
        }

        $this->setData(array_merge($qArr, $_GET, $_POST));

        unset($_GET);
        unset($_POST);
        unset($_REQUEST);
    }
}