CREATE TABLE `regions`
(
    `id`          char(6) NOT NULL,
    `parent_id` char(6)      NOT NULL DEFAULT '0',
    `name`        varchar(60)  NOT NULL,
    `postal` char(6)      NOT NULL DEFAULT '',
    `tel`    char(4)      NOT NULL DEFAULT '',
    `letter`      varchar(255) NOT NULL DEFAULT '',
    `initial`     char(1)      NOT NULL DEFAULT '',
    `type`        tinyint(1) NOT NULL DEFAULT '1' COMMENT '类型 1标准的区域行政区划',
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;