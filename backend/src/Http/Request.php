<?php

declare(strict_types=1);

namespace Movie\Http;

final class Request
{
    private ?array $json = null;

    public function __construct(
        public readonly string $method,
        public readonly string $path,
        public readonly array $query,
        private readonly array $server,
    ) {
    }

    public static function fromGlobals(): self
    {
        $path = rawurldecode((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));

        return new self(
            strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'),
            $path ?: '/',
            $_GET,
            $_SERVER,
        );
    }

    public function json(): array
    {
        if ($this->json !== null) {
            return $this->json;
        }

        $decoded = json_decode((string) file_get_contents('php://input'), true);
        return $this->json = is_array($decoded) ? $decoded : [];
    }

    public function header(string $name): ?string
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        $value = $this->server[$key] ?? null;
        return is_string($value) && $value !== '' ? $value : null;
    }

    public function ip(): string
    {
        $candidate = $this->server['REMOTE_ADDR'] ?? '0.0.0.0';
        return filter_var($candidate, FILTER_VALIDATE_IP) ? $candidate : '0.0.0.0';
    }

    public function userAgent(): string
    {
        return substr((string) ($this->server['HTTP_USER_AGENT'] ?? ''), 0, 256);
    }

    public function referer(): string
    {
        return substr((string) ($this->server['HTTP_REFERER'] ?? ''), 0, 256);
    }
}
