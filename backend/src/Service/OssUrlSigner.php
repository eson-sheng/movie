<?php

declare(strict_types=1);

namespace Movie\Service;

use Movie\Http\HttpException;

final class OssUrlSigner
{
    public function __construct(
        private readonly array $config,
        private readonly int $expiresIn = 3600,
    ) {
    }

    public function sign(string $object): string
    {
        $object = ltrim(trim($object), '/');
        if (!$this->isAllowedObject($object)) {
            throw new HttpException(422, 'OSS 对象路径不合法');
        }

        $accessKeyId = trim((string) ($this->config['accessKeyId'] ?? ''));
        $accessKeySecret = (string) ($this->config['accessKeySecret'] ?? '');
        $endpoint = rtrim(trim((string) ($this->config['endpoint'] ?? '')), '/') . '/';
        $bucket = trim((string) ($this->config['bucket'] ?? ''));

        if ($accessKeyId === '' || $accessKeySecret === '' || $endpoint === '/' || $bucket === '') {
            throw new HttpException(503, 'OSS 配置不完整');
        }

        // 阿里云 OSS 公网域名支持 HTTPS，避免 HTTPS 页面加载 HTTP 切片时被浏览器拦截。
        $endpoint = preg_replace('~^http://~i', 'https://', $endpoint) ?? $endpoint;
        $expires = time() + $this->expiresIn;
        $stringToSign = "GET\n\n\n{$expires}\n/{$bucket}/{$object}";
        $signature = base64_encode(hash_hmac('sha1', $stringToSign, $accessKeySecret, true));

        return $endpoint . str_replace('%2F', '/', rawurlencode($object)) . '?' . http_build_query([
            'OSSAccessKeyId' => $accessKeyId,
            'Expires' => $expires,
            'Signature' => $signature,
        ], '', '&', PHP_QUERY_RFC3986);
    }

    private function isAllowedObject(string $object): bool
    {
        return strlen($object) <= 512
            && !str_contains($object, '..')
            && !str_contains($object, '\\')
            && preg_match('~^movie/[a-f0-9]{32}/(?:enc\.key|index-\d+\.ts)$~', $object) === 1;
    }
}
