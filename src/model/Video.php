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

    /**
     * @desc 获取目录下所有视频文件名称
     * @param string $path
     * @return array
     */
    public function getVideoList ($path = null)
    {
        $list = [];

        if (empty($path)) {
            $path = $this->videoPath;
        }

        $handle = opendir($path);
        if ($handle) {
            while (($file = readdir($handle)) == true) {
                if ($file != '.' && $file != '..') {
                    $p = "{$path}/{$file}";
                    if (is_dir($p)) {
                        $this->getVideoList($p);
                    } else {
                        if (
                            $file != '.DS_Store' &&
                            $file != '.gitignore'
                        ) {
                            $list[] = pathinfo($file, PATHINFO_FILENAME);
                            $this->getVideoPhoto($p);
                        }
                    }
                }
            }
        }
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
//            dd($output,$return_val);
        }

        return "{$thum_path}/{$file_name}.jpg";
    }
}