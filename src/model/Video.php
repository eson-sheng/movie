<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2020-02-24
 * Time: 22:43
 */

namespace model;


class Video
{
    private $videoPath = __DIR__ . '/../../public/video';
    private $thumPath = __DIR__ . '/../../public/video/thum';
    private $videoHlsPath = __DIR__ . '/../../public/video/hls';

    /**
     * @desc 获取目录下所有视频文件名称
     * @param null $path
     * @param array $list
     * @return array
     */
    public function getVideoList ($path = null, &$list = [])
    {
        $notPath = ['.', '..', 'thum'];
        $notFile = ['.DS_Store', '.gitignore'];

        if (empty($path)) {
            $path = $this->videoPath;
        }

        $handle = opendir($path);
        if ($handle) {
            while (($file = readdir($handle)) == true) {
                if (!in_array($file, $notPath)) {
                    $p = "{$path}/{$file}";
                    if (is_dir($p)) {
                        $this->getVideoList($p, $list);
                    } else {
                        if (!in_array($file, $notFile)) {
                            $ext = pathinfo($file, PATHINFO_EXTENSION);
                            if ($ext == 'm3u8') {
                                $hltVideoPath = pathinfo(
                                    $p,
                                    PATHINFO_DIRNAME
                                );
                                $hash = pathinfo(
                                    $hltVideoPath,
                                    PATHINFO_BASENAME
                                );
                                $list[] = $this->getVideoHlsInfo(
                                    $hash
                                );
                            }
                            if (in_array($ext, ['mp4', 'MP4'])) {
                                $list[] = pathinfo($file, PATHINFO_FILENAME);
                                $this->getVideoPhoto($p);
                            }
                        }
                    }
                }
            }
        }

        # 文件名称按顺序排列
        foreach ($list as $v) {
            if (empty($v['name'])) {
                $nat_sort_list[] = $v;
            } else {
                $sort_list[] = $v;
            }
        }

        natsort($nat_sort_list);
        $nat_sort_list = array_values($nat_sort_list);

        sort($sort_list);

        $list = array_merge($nat_sort_list, $sort_list);

        return $list;
    }

    /**
     * @desc 获取视频缩略图
     * @param $file
     * @return string
     */
    public function getVideoPhoto ($file)
    {
        $thum_path = $this->thumPath;
        if (!file_exists($thum_path)) {
            mkdir($thum_path, 0777);
        }

        $file_name = pathinfo($file, PATHINFO_FILENAME);
        if (!file_exists("{$thum_path}/{$file_name}.jpg")) {
            $ffmpeg = "ffmpeg -i \"%s\" -y -f mjpeg -ss 8 -t 0.128 -s 192x108 \"%s\" 2>&1";
            $cmd = sprintf(
                $ffmpeg,
                $file,
                "{$thum_path}/{$file_name}.jpg"
            );
            exec($cmd, $output, $return_val);
//            dump($output, $return_val);
        }

        return "{$thum_path}/{$file_name}.jpg";
    }

    /**
     * @desc 获取hls视频的信息
     * @param $hash
     * @return array
     */
    public function getVideoHlsInfo ($hash)
    {
        $json_str = file_get_contents(
            "{$this->videoHlsPath}/{$hash}/index.json"
        );
        $json = json_decode($json_str, true);
        return [
            'name' => $json['name'],
            'hash' => $json['hash'],
            'm3u8' => "{$json['hash']}/index.m3u8",
            'thum' => "{$json['hash']}/index.png",
        ];
    }

    /**
     * @desc 检查目录下是否有该视频
     * @param $name
     * @return bool
     */
    public function checkoutVideoName ($name)
    {
        $video_path = $this->videoPath;
        if (is_file("{$video_path}/{$name}.mp4")) {
            return true;
        }
        return false;
    }

    /**
     * @desc 检查hls视频的索引文件是否存在
     * @param $hash
     * @return bool
     */
    public function checkoutHlsVideo ($hash)
    {
        $video_hls_path = $this->videoHlsPath;
        if (is_file("{$video_hls_path}/{$hash}/index.m3u8")) {
            return true;
        }
        return false;
    }
}
