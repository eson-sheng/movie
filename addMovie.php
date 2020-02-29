<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2020-02-27
 * Time: 00:24
 */

// 设置命令行需要的参数
$long_opt = array(
    'path:',
);
$params = getopt('', $long_opt);

// 判断参数不能为空
if (empty($params)) {
    echo "\n\nThis is a simple add Movie tool (●ﾟωﾟ●)\n
usage `php addMovie.php --path \"{path}\"` \n\n";
    exit();
}

// 判断参数是否是视频文件
$path = $params['path'];
if (!is_file($path)) {
    echo _error_ex('Error: There is no such file ! ╮(╯_╰)╭');
    exit();
}

// 判断只能是MP4文件
$ext = pathinfo($path, PATHINFO_EXTENSION);
if ($ext != 'mp4') {
    echo _error_ex('Error: This tool only supports MP4 files ! (╯︵╰)');
    exit();
}

// 定义需要的变量信息
$filename = pathinfo($path, PATHINFO_FILENAME);
$hash = md5($filename);
$hlsPath = __DIR__ . '/public/video/hls';

// 创建hash名称的资源目录
if (!is_dir("{$hlsPath}/{$hash}")) {
    mkdir("{$hlsPath}/{$hash}");
}

// 生成视频的缩略图 index.png
$ffmpeg = "ffmpeg -i \"%s\" -y -f mjpeg -ss 8 -t 0.128 -s 192x108 \"%s\" 2>&1";
$cmd = sprintf(
    $ffmpeg,
    $path,
    "{$hlsPath}/{$hash}/index.png"
);
echo _shell_ex([$cmd]);

// 创建index.json文件 - 标记信息
file_put_contents(
    "{$hlsPath}/{$hash}/index.json",
    json_encode([
        'name' => $filename,
        'hash' => $hash,
    ])
);

// 创建加密密钥文件
chdir("{$hlsPath}/{$hash}");
echo _shell_ex([
    'openssl rand 16 > enc.key',
    'xxd enc.key',
    'openssl rand -hex 16 > enc.iv.txt',
    'xxd enc.iv.txt',
]);

// 生成hls_key_info_file文件
$iv = file_get_contents("{$hlsPath}/{$hash}/enc.iv.txt");
file_put_contents(
    "{$hlsPath}/{$hash}/enc.keyinfo",
    "enc.key\nenc.key\n{$iv}"
);

if (!is_file($path)) {
    $path = __DIR__ . "/{$path}";
}

// 切割MP4文件为ts文件，生成index.m3u8文件
$cmd = "ffmpeg -i {$path} -strict -2 -hls_time 2 -hls_list_size 0 -hls_key_info_file enc.keyinfo -hls_segment_filename \"index-%d.ts\" index.m3u8";

// 实时显示终端执行结果
ob_start();
$proc = popen($cmd, 'r');
while (!feof($proc)) {
    echo fread($proc, 4096);
    flush();
}
ob_end_flush();

// 如果索引文件生成则成功
if (file_exists("{$hlsPath}/{$hash}/index.m3u8")) {
    echo "\n\033[47;30m SUCCESS \033[0m\n\n";
}

/**
 * @desc 执行多个 shell 命令
 * @param array $cmds
 * @return string
 */
function _shell_ex ($cmds)
{
    $return_str = "\n";
    foreach ($cmds as $cmd) {
        $tmp_return = shell_exec("{$cmd} 2>&1");
        $return_str .= "{$tmp_return}\n";
    }
    return $return_str;
}

/**
 * @desc 命令行显示错误提示
 * @param $tip
 * @return string
 */
function _error_ex ($tip)
{
    return "\n\n\033[41;37m {$tip} \033[0m\n\n";
}
