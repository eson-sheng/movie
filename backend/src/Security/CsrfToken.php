<?php

declare(strict_types=1);

namespace Movie\Security;

final class CsrfToken
{
    private const KEY = 'movie_csrf_token';

    public function issue(): string
    {
        if (empty($_SESSION[self::KEY])) {
            $_SESSION[self::KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::KEY];
    }

    public function verify(?string $token): bool
    {
        return is_string($token)
            && isset($_SESSION[self::KEY])
            && hash_equals($_SESSION[self::KEY], $token);
    }
}
