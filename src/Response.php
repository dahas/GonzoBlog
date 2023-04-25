<?php

namespace Gonzo\Sources;

class Response {

    private string $status = "200 OK";
    private array $headers = [];
    private string $body = "";

    public function read(): string
    {
        return $this->body;
    }

    public function write(string $content): void
    {
        $this->body .= $content;
    }

    public function redirect(string $uri): void
    {
        header("location: $uri");
        exit;
    }

    public function setStatus(int $code, string $message = ""): void
    {
        if ($message) {
            $this->status = $code . " " . $message;
        } else {
            $this->status = $this->mapStatusCode($code);
        }
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getHeader(string $header): string
    {
        return $this->headers[$header];
    }

    public function addHeader(string $name, mixed $value): void
    {
        $this->headers[$name] = $value;
    }

    public function flush(): void
    {
        header("HTTP/1.1 {$this->status}");

        header("Cache-Control: max-age=2592000"); //30days (60sec * 60min * 24hours * 30days)

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        $this->headers = [];

        die($this->body);
    }

    private function mapStatusCode(int $code): string
    {
        $statusCodes = [
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',
            103 => 'Checkpoint',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => 'Switch Proxy',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            425 => 'Unordered Collection',
            426 => 'Upgrade Required',
            449 => 'Retry With',
            450 => 'Blocked by Windows Parental Controls',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            509 => 'Bandwidth Limit Exceeded',
            510 => 'Not Extended'
        ];

        return $code . " " . $statusCodes[$code];
    }
}