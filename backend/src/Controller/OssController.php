<?php

declare(strict_types=1);

namespace Movie\Controller;

use Movie\Http\HttpException;
use Movie\Http\Request;
use Movie\Service\OssUrlSigner;

final class OssController
{
    public function __construct(private readonly OssUrlSigner $signer)
    {
    }

    public function redirect(Request $request): never
    {
        $object = $request->query['object'] ?? $request->query['oss'] ?? null;
        if (!is_string($object) || $object === '') {
            throw new HttpException(422, '缺少 OSS 对象路径');
        }

        header('Cache-Control: no-store');
        header('Location: ' . $this->signer->sign($object), true, 302);
        exit;
    }
}
