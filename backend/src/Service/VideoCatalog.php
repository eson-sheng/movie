<?php

declare(strict_types=1);

namespace Movie\Service;

use Movie\Http\HttpException;

final class VideoCatalog
{
    public function __construct(
        private readonly string $videoPath,
        private readonly string $mediaBaseUrl,
    ) {
    }

    public function all(): array
    {
        $videos = [];

        foreach (glob($this->videoPath . '/*.{mp4,MP4}', GLOB_BRACE) ?: [] as $path) {
            if (!is_file($path)) {
                continue;
            }
            $name = pathinfo($path, PATHINFO_FILENAME);
            $videos[] = $this->mp4($name);
        }

        foreach (glob($this->videoPath . '/hls/*', GLOB_ONLYDIR) ?: [] as $directory) {
            $hash = basename($directory);
            $info = $this->readHls($hash);
            if ($info !== null) {
                $videos[] = $info;
            }
        }

        usort($videos, static fn (array $a, array $b): int => strnatcasecmp($a['name'], $b['name']));
        return $videos;
    }

    public function find(string $id): array
    {
        if (!preg_match('/^[a-f0-9]{32}$/', $id)) {
            throw new HttpException(404, '视频不存在');
        }

        $hls = $this->readHls($id);
        if ($hls !== null) {
            return $hls;
        }

        foreach (glob($this->videoPath . '/*.{mp4,MP4}', GLOB_BRACE) ?: [] as $path) {
            $name = pathinfo($path, PATHINFO_FILENAME);
            if (hash_equals(md5($name), $id)) {
                return $this->mp4($name);
            }
        }

        throw new HttpException(404, '视频不存在');
    }

    private function mp4(string $name): array
    {
        $encoded = rawurlencode($name);
        $coverPath = $this->videoPath . '/thum/' . $name . '.jpg';

        return [
            'id' => md5($name),
            'name' => $name,
            'type' => 'mp4',
            'playUrl' => $this->mediaBaseUrl . '/' . $encoded . '.mp4',
            'coverUrl' => is_file($coverPath) ? $this->mediaBaseUrl . '/thum/' . $encoded . '.jpg' : null,
            'danmakuEnabled' => true,
        ];
    }

    private function readHls(string $hash): ?array
    {
        if (!preg_match('/^[a-f0-9]{32}$/', $hash)) {
            return null;
        }

        $directory = $this->videoPath . '/hls/' . $hash;
        $metadataPath = $directory . '/index.json';
        if (!is_file($directory . '/index.m3u8') || !is_file($metadataPath)) {
            return null;
        }

        $metadata = json_decode((string) file_get_contents($metadataPath), true);
        if (!is_array($metadata) || !is_string($metadata['name'] ?? null)) {
            return null;
        }

        return [
            'id' => $hash,
            'name' => $metadata['name'],
            'type' => 'hls',
            'playUrl' => $this->mediaBaseUrl . '/hls/' . $hash . '/index.m3u8',
            'coverUrl' => is_file($directory . '/index.png')
                ? $this->mediaBaseUrl . '/hls/' . $hash . '/index.png'
                : null,
            'danmakuEnabled' => true,
        ];
    }
}
