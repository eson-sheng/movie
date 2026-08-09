<?php

declare(strict_types=1);

namespace Movie\Http;

final class JsonResponse
{
    public static function send(mixed $data = null, int $status = 200, string $message = 'OK'): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        echo json_encode([
            'code' => $status < 400 ? 0 : $status,
            'message' => $message,
            'data' => $data,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        exit;
    }
}
