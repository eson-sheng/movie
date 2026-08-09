<?php

declare(strict_types=1);

namespace Movie\Http;

final class Router
{
    private array $routes = [];

    public function add(string $method, string $pattern, callable $handler): void
    {
        $this->routes[] = [strtoupper($method), $pattern, $handler];
    }

    public function dispatch(Request $request): mixed
    {
        foreach ($this->routes as [$method, $pattern, $handler]) {
            if ($request->method !== $method) {
                continue;
            }

            $regex = preg_replace('~\{([a-zA-Z_][a-zA-Z0-9_]*)}~', '(?P<$1>[^/]+)', $pattern);
            if (preg_match('~^' . $regex . '$~', $request->path, $matches) !== 1) {
                continue;
            }

            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            return $handler($request, $params);
        }

        throw new HttpException(404, '接口不存在');
    }
}
