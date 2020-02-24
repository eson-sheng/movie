-- 建立数据库
CREATE DATABASE `movie` DEFAULT charset=utf8;
USE `movie`;

-- 弹幕管理表
DROP TABLE IF EXISTS `danmaku`;
CREATE TABLE IF NOT EXISTS `danmaku`(
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `vid` VARCHAR(32) NOT NULL COMMENT '视频ID',
  `time` FLOAT NOT NULL COMMENT '相对视频发送弹幕时间',
  `content` VARCHAR(128) NOT NULL COMMENT '弹幕内容',
  `color` VARCHAR(16) NOT NULL COMMENT '弹幕颜色',
  `author` VARCHAR(32) NOT NULL COMMENT '弹幕发送者',
  `ip` VARCHAR(32) NOT NULL COMMENT '弹幕发送的IP',
  `type` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '类型(单选):0=滚动,1=顶部,2=底部',
  `referer` VARCHAR(256) NOT NULL DEFAULT '' COMMENT '来源地址',
  `equipment` VARCHAR(256) NOT NULL DEFAULT '' COMMENT '使用设备',
  `creat_time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  `status` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '状态(单选):0=未激活,1=激活',
  PRIMARY KEY(`id`),
  KEY `vid` (`vid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='弹幕管理表';