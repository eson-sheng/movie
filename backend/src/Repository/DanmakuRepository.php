<?php

declare(strict_types=1);

namespace Movie\Repository;

use PDO;

final class DanmakuRepository
{
    public function __construct(private readonly PDO $database)
    {
    }

    public function findByVideo(string $videoId): array
    {
        $statement = $this->database->prepare(
            'SELECT `time`, `type`, `color`, `author`, `content`
             FROM `danmaku` WHERE `vid` = :vid AND `status` = 1 ORDER BY `id` ASC'
        );
        $statement->execute(['vid' => $videoId]);

        return array_map(static fn (array $row): array => [
            (float) $row['time'],
            (int) $row['type'],
            (int) $row['color'],
            $row['author'],
            $row['content'],
        ], $statement->fetchAll());
    }

    public function create(array $danmaku, string $ip, string $referer, string $userAgent): void
    {
        $statement = $this->database->prepare(
            'INSERT INTO `danmaku`
                (`vid`, `author`, `time`, `content`, `color`, `type`, `ip`, `referer`, `equipment`)
             VALUES
                (:vid, :author, :time, :content, :color, :type, :ip, :referer, :equipment)'
        );
        $statement->execute([
            'vid' => $danmaku['id'],
            'author' => $danmaku['author'],
            'time' => $danmaku['time'],
            'content' => $danmaku['text'],
            'color' => $danmaku['color'],
            'type' => $danmaku['type'],
            'ip' => $ip,
            'referer' => $referer,
            'equipment' => $userAgent,
        ]);
    }
}
