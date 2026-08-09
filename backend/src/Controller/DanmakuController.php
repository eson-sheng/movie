<?php

declare(strict_types=1);

namespace Movie\Controller;

use Movie\Http\HttpException;
use Movie\Http\JsonResponse;
use Movie\Http\Request;
use Movie\Repository\DanmakuRepository;
use Movie\Security\CsrfToken;
use Movie\Service\VideoCatalog;

final class DanmakuController
{
    public function __construct(
        private readonly DanmakuRepository $repository,
        private readonly VideoCatalog $catalog,
        private readonly CsrfToken $csrf,
    ) {
    }

    public function index(Request $request, array $params): never
    {
        $id = $params['id'];
        $this->catalog->find($id);
        JsonResponse::send($this->repository->findByVideo($id));
    }

    public function store(Request $request, array $params): never
    {
        $body = $request->json();
        $token = $request->header('X-CSRF-Token') ?? ($body['token'] ?? null);
        if (!$this->csrf->verify(is_string($token) ? $token : null)) {
            throw new HttpException(419, '页面令牌已失效，请刷新后重试');
        }

        $body['id'] = $params['id'];
        $this->catalog->find($body['id']);
        $danmaku = $this->validate($body);
        $this->repository->create(
            $danmaku,
            $request->ip(),
            $request->referer(),
            $request->userAgent(),
        );

        JsonResponse::send(null, 201, '弹幕发送成功');
    }

    private function validate(array $data): array
    {
        $author = trim((string) ($data['author'] ?? ''));
        $text = trim((string) ($data['text'] ?? ''));
        $time = $data['time'] ?? null;
        $color = $data['color'] ?? null;
        $type = $data['type'] ?? null;

        if ($author === '' || mb_strlen($author) > 32) {
            throw new HttpException(422, '弹幕作者格式错误');
        }
        if ($text === '' || mb_strlen($text) > 128) {
            throw new HttpException(422, '弹幕内容格式错误');
        }
        if (!is_numeric($time) || (float) $time < 0) {
            throw new HttpException(422, '弹幕时间格式错误');
        }
        if (!is_int($color) || $color < 0 || $color > 16777215) {
            throw new HttpException(422, '弹幕颜色格式错误');
        }
        if (!is_int($type) || !in_array($type, [0, 1, 2], true)) {
            throw new HttpException(422, '弹幕类型格式错误');
        }

        return [
            'id' => $data['id'],
            'author' => $author,
            'text' => $text,
            'time' => (float) $time,
            'color' => $color,
            'type' => $type,
        ];
    }
}
