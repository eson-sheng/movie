<?php

declare(strict_types=1);

use Movie\Controller\DanmakuController;
use Movie\Controller\OssController;
use Movie\Controller\VideoController;
use Movie\Database\Connection;
use Movie\Http\HttpException;
use Movie\Http\JsonResponse;
use Movie\Http\Request;
use Movie\Http\Router;
use Movie\Repository\DanmakuRepository;
use Movie\Security\CsrfToken;
use Movie\Service\VideoCatalog;
use Movie\Service\OssUrlSigner;

require dirname(__DIR__, 2) . '/vendor/autoload.php';

session_name('movie_session');
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
]);
session_start();

$config = require dirname(__DIR__) . '/config/app.php';
$request = Request::fromGlobals();
$router = new Router();
$catalog = new VideoCatalog($config['video_path'], $config['media_base_url']);
$csrf = new CsrfToken();
$videoController = new VideoController($catalog);
$ossController = new OssController(new OssUrlSigner($config['oss']));

$router->add('GET', '/api/v1/health', static fn () => JsonResponse::send(['status' => 'ok']));
$router->add('GET', '/api/v1/csrf-token', static fn () => JsonResponse::send(['token' => $csrf->issue()]));
$router->add('GET', '/api/v1/videos', [$videoController, 'index']);
$router->add('GET', '/api/v1/videos/{id}', [$videoController, 'show']);
$router->add('GET', '/api/v1/oss', [$ossController, 'redirect']);

try {
    if (str_contains($request->path, '/danmaku')) {
        $repository = new DanmakuRepository(Connection::create($config['database']));
        $danmakuController = new DanmakuController($repository, $catalog, $csrf);
        $router->add('GET', '/api/v1/videos/{id}/danmaku', [$danmakuController, 'index']);
        $router->add('POST', '/api/v1/videos/{id}/danmaku', [$danmakuController, 'store']);
    }
    $router->dispatch($request);
} catch (HttpException $exception) {
    JsonResponse::send(null, $exception->status, $exception->getMessage());
} catch (Throwable $exception) {
    error_log((string) $exception);
    JsonResponse::send(null, 500, '服务器内部错误');
}
