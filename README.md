# 看电影

一个轻量、可自行部署的在线影片库，支持 MP4、HLS 加密切片和弹幕播放。

项目采用前后端分离架构：Vue 3 负责影片列表、搜索和播放页面，PHP 8 提供视频与弹幕 JSON API，Nginx 负责 SPA、API 和视频静态资源分发。

访问地址：[https://video.shengxuecheng.cn/](https://video.shengxuecheng.cn/)

## 功能

- 自动读取本地 MP4 和 HLS 影片
- 按影片名称自然排序和即时搜索
- DPlayer 视频播放与 hls.js 支持
- 进入播放页后优先尝试有声自动播放
- 浏览器阻止自动播放时提供“点击开始有声播放”按钮
- 默认循环播放
- 弹幕读取、发送和历史弹幕回放
- Session CSRF 防护
- MP4、HLS、封面统一由 Nginx 提供
- 桌面端和移动端响应式页面

## 技术栈

### 前端

- Vue 3
- Vite
- TypeScript
- Vue Router
- DPlayer
- hls.js
- Yarn

### 后端

- PHP 8.2+
- Composer / PSR-4
- PDO
- MySQL 8
- Nginx + PHP-FPM

### 视频处理

- FFmpeg
- OpenSSL

## 环境要求

| 组件 | 建议版本 |
| --- | --- |
| PHP | 8.2 或更高 |
| Composer | 2.x |
| Node.js | 22.x |
| Yarn | 1.22 或更高 |
| MySQL | 8.x |
| Nginx | 稳定版 |
| FFmpeg | 仅导入或转码影片时需要 |
| OpenSSL | 仅生成加密 HLS 时需要 |

## 目录结构

```text
movie/
├── backend/
│   ├── config/                PHP API 配置
│   ├── public/                PHP API 内部入口
│   └── src/
│       ├── Controller/        视频与弹幕控制器
│       ├── Database/          PDO 连接
│       ├── Http/              请求、路由和 JSON 响应
│       ├── Repository/        弹幕数据访问
│       ├── Security/          CSRF Token
│       └── Service/           视频目录服务
├── frontend/
│   ├── src/
│   │   ├── api/               前端 API 封装
│   │   ├── components/        页头与播放器组件
│   │   ├── router/            Vue Router
│   │   ├── styles/            全局样式
│   │   ├── types/             TypeScript 类型
│   │   └── views/             列表页与播放页
│   ├── package.json
│   ├── vite.config.ts
│   └── yarn.lock
├── public/
│   ├── app/                   Vue 生产构建结果
│   ├── video/
│   │   ├── hls/               HLS 影片
│   │   └── thum/              MP4 缩略图
│   ├── api.php                生产 API 入口
│   └── favicon.ico
├── config/                    数据库配置
├── deploy/                    Nginx 配置示例
├── docs/                      改造和部署补充文档
├── sql/movie.sql              弹幕表初始化 SQL
├── addMovie                   HLS 转码工具
├── dev-router.php             PHP 本地开发路由
├── composer.json
└── README.md
```

`public/app/`、`frontend/node_modules/` 和 `vendor/` 都是可重新生成的目录，不应手工修改。

## 安装

### 1. 安装 PHP 依赖

```bash
composer install
```

### 2. 安装前端依赖

```bash
cd frontend
yarn install --frozen-lockfile
```

### 3. 初始化数据库

在 MySQL 中执行：

```text
sql/movie.sql
```

新建本地数据库配置 `config/local_database.php`：

```php
<?php

return [
    'database' => 'movie',
    'username' => 'root',
    'password' => '你的数据库密码',
];
```

`config/local_database.php` 已被 Git 忽略，不要把真实密码提交到仓库。

生产环境也可以使用环境变量覆盖：

```text
MOVIE_DB_HOST
MOVIE_DB_PORT
MOVIE_DB_NAME
MOVIE_DB_USERNAME
MOVIE_DB_PASSWORD
```

## 本地开发

终端一启动 PHP API 和视频资源服务：

```bash
php -S 127.0.0.1:8080 -t public dev-router.php
```

终端二启动 Vue 开发服务器：

```bash
cd frontend
yarn dev
```

Vite 会把 `/api` 和 `/video` 请求代理到 `http://127.0.0.1:8080`。

## 添加影片

### MP4

将 MP4 文件放入：

```text
public/video/
```

对应缩略图放入：

```text
public/video/thum/影片文件名.jpg
```

例如：

```text
public/video/示例影片.mp4
public/video/thum/示例影片.jpg
```

影片 ID 由不含扩展名的文件名计算 MD5 得到。

### HLS

使用项目提供的工具：

```bash
php addMovie --path="/absolute/path/movie.mp4"
```

需要替换 HLS 切片地址时可以指定域名：

```bash
php addMovie \
  --path="/absolute/path/movie.mp4" \
  --domain="https://video.example.com"
```

生成目录结构：

```text
public/video/hls/{md5}/
├── enc.iv.txt
├── enc.key
├── enc.keyinfo
├── index.json
├── index.m3u8
├── index.png
└── index-*.ts
```

`index.json` 示例：

```json
{
  "name": "影片名称",
  "hash": "影片名称对应的 MD5"
}
```

视频列表请求只读取已有文件和元数据，不会在普通 HTTP 请求中运行 FFmpeg。

## API

| 方法 | 地址 | 说明 |
| --- | --- | --- |
| GET | `/api/v1/health` | 健康检查 |
| GET | `/api/v1/csrf-token` | 获取当前 Session 的 CSRF Token |
| GET | `/api/v1/videos` | 获取影片列表 |
| GET | `/api/v1/videos/{id}` | 获取影片播放信息 |
| GET | `/api/v1/videos/{id}/danmaku` | 获取历史弹幕 |
| POST | `/api/v1/videos/{id}/danmaku` | 发送弹幕 |

统一响应格式：

```json
{
  "code": 0,
  "message": "OK",
  "data": {}
}
```

发送弹幕前，需要从 `/api/v1/csrf-token` 获取 Token，并在同一个 Session 中通过请求头提交：

```http
X-CSRF-Token: token-value
```

## 检查与构建

PHP 配置与语法检查：

```bash
composer validate --strict
composer check
```

前端类型检查：

```bash
cd frontend
yarn type-check
```

生产构建：

```bash
cd frontend
yarn build
```

构建结果写入：

```text
public/app/
```

## 生产部署

安装生产 PHP 依赖并构建前端：

```bash
composer install --no-dev --optimize-autoloader

cd frontend
yarn install --frozen-lockfile
yarn build
```

Nginx 的站点根目录必须是项目的 `public`，不能直接指向 `public/app`，因为 `/api.php` 和 `/video` 也位于 `public` 下。

```nginx
server {
    listen 80;
    server_name video.example.com;

    charset utf-8;
    root /path/to/movie/public;
    index index.html;

    location ^~ /api/ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/api.php;
        fastcgi_param SCRIPT_NAME /api.php;
        fastcgi_pass unix:/var/tmp/php-fpm.sock;
    }

    location ^~ /video/ {
        try_files $uri =404;
        add_header Accept-Ranges bytes always;
        add_header Cache-Control "public, max-age=3600";
    }

    location ^~ /app/assets/ {
        try_files $uri =404;
        expires 1y;
        add_header Cache-Control "public, max-age=31536000, immutable";
    }

    location = /favicon.ico {
        try_files $uri =404;
        expires 7d;
    }

    location / {
        try_files $uri /app/index.html;
    }

    location ~ \.php$ {
        return 404;
    }
}
```

完整示例见 `deploy/nginx.conf.example`。不同服务器的 PHP-FPM 可能监听 TCP 端口或其他 Socket，请根据实际环境修改 `fastcgi_pass`。

检查并重载 Nginx：

```bash
nginx -t
nginx -s reload
```

部署后检查：

```text
https://video.example.com/
https://video.example.com/api/v1/health
```

## 常见问题

### 首页返回 403 directory index is forbidden

确认 SPA 配置没有使用 `$uri/`：

```nginx
location / {
    try_files $uri /app/index.html;
}
```

### 自动播放没有声音

Chrome、Safari 等浏览器会限制未经用户操作的有声自动播放。项目会优先尝试有声播放；被浏览器阻止时，会显示“点击开始有声播放”按钮。

### 修改前端后页面仍是旧版本

重新构建并强制刷新：

```bash
cd frontend
yarn build
```

macOS 浏览器可以使用 `Command + Shift + R` 强制刷新。

### 弹幕发送成功但历史弹幕不显示

确认 `/api/v1/videos/{id}/danmaku` 返回成功，并检查浏览器 Console 和 PHP/Nginx 错误日志。前端会把数据库返回的 DPlayer 数组格式转换为播放器对象格式。

## 安全提示

- 不要提交数据库密码、私钥或 HLS 密钥配置。
- 生产环境建议启用 HTTPS。
- 只将 `public/` 暴露为 Web 根目录。
- 不要允许 Web 用户直接访问 `backend/`、`config/`、`vendor/` 等目录。
- HLS 加密只用于媒体传输保护，不等同于完整 DRM。

## 关于码主

[北辰妙语](https://blog.shengxuecheng.cn/)
