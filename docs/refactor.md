# 前后端分离改造说明

## 当前结构

- `backend/`：PHP 8.2+ JSON API，使用 Composer PSR-4 自动加载。
- `frontend/`：Vue 3、Vite、TypeScript 和 DPlayer。
- `public/video/`：继续保存现有 MP4、HLS 和缩略图，本阶段不搬动大文件。
- `public/index.php`：旧版页面入口，暂时保留用于回滚。
- `public/api.php`：新版 API 的生产入口。
- `public/app/`：`yarn build` 生成的 Vue 生产文件，不手工编辑。

## 本地开发

安装依赖：

```bash
composer install
cd frontend && yarn install
```

启动 PHP（终端一）：

```bash
php -S 127.0.0.1:8080 -t public dev-router.php
```

启动 Vue（终端二）：

```bash
cd frontend
yarn dev
```

打开 Vite 输出的地址。开发服务器会把 `/api` 和 `/video` 代理到 PHP 服务。

## 配置

新版 API 兼容读取原来的 `config/database.php` 和 `config/local_database.php`。生产环境也可使用以下环境变量覆盖：

- `MOVIE_DB_HOST`
- `MOVIE_DB_PORT`
- `MOVIE_DB_NAME`
- `MOVIE_DB_USERNAME`
- `MOVIE_DB_PASSWORD`

不要提交包含真实密码的 `config/local_database.php`。

## API

| 方法 | 路径 | 说明 |
| --- | --- | --- |
| GET | `/api/v1/health` | 健康检查 |
| GET | `/api/v1/csrf-token` | 获取当前 Session 的 CSRF Token |
| GET | `/api/v1/videos` | 视频列表 |
| GET | `/api/v1/videos/{id}` | 视频播放信息 |
| GET | `/api/v1/videos/{id}/danmaku` | 弹幕列表 |
| POST | `/api/v1/videos/{id}/danmaku` | 发送弹幕 |

发送弹幕必须携带同一 Session 获取的 `X-CSRF-Token`。所有接口统一返回：

```json
{
  "code": 0,
  "message": "OK",
  "data": {}
}
```

## 构建与部署

```bash
composer install --no-dev --optimize-autoloader
cd frontend
yarn install --frozen-lockfile
yarn build
```

参考 `deploy/nginx.conf.example` 配置 Nginx：

- `/` 返回 Vue SPA；
- `/api/` 交给 `public/api.php`；
- `/video/` 直接由 Nginx 返回视频资源；
- Vue 路由刷新时回退到 `public/app/index.html`。

切换完成并稳定运行一段时间后，再删除旧的 PHP 视图和旧静态脚本。

## 下一阶段

当前视频列表仍从现有目录及 HLS `index.json` 读取。MP4 缺少缩略图时，列表请求会调用 FFmpeg 生成一次；已有缩略图不会重复生成。下一阶段可增加 `videos` 表，并让 `addMovie` 在转码成功后登记数据库，从而避免每次请求扫描目录。
