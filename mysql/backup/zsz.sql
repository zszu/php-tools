/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50726
Source Host           : localhost:3306
Source Database       : base

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2020-11-23 09:02:35
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tsj_articles
-- ----------------------------
DROP TABLE IF EXISTS `tsj_articles`;
CREATE TABLE `tsj_articles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_by` smallint(6) unsigned DEFAULT '0' COMMENT '排序',
  `type` smallint(6) unsigned DEFAULT '0' COMMENT '分类id',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `subtitle` varchar(255) DEFAULT NULL COMMENT '副标题',
  `publisher` varchar(255) DEFAULT NULL COMMENT '作者',
  `summary` text COMMENT '简介',
  `content` text COMMENT '内容',
  `cover` varchar(255) DEFAULT NULL COMMENT '封面',
  `covers` varchar(255) DEFAULT NULL COMMENT '多图',
  `qr_code` varchar(255) DEFAULT NULL COMMENT '二维码',
  `bg_color` varchar(255) DEFAULT NULL COMMENT '背景色',
  `bg_pic` varchar(255) DEFAULT NULL COMMENT '背景图',
  `url` varchar(255) DEFAULT NULL COMMENT '链接',
  `tags` varchar(255) DEFAULT NULL COMMENT '标签',
  `views` int(10) unsigned DEFAULT '0' COMMENT '查看数',
  `index_show` tinyint(1) DEFAULT '0' COMMENT '首页显示',
  `created_at` int(11) unsigned DEFAULT '0',
  `updated_at` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1' COMMENT '状态：1 启用 0停用',
  `name` varchar(255) DEFAULT NULL COMMENT '所属栏目',
  `keywords` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='新闻表';

-- ----------------------------
-- Records of tsj_articles
-- ----------------------------
INSERT INTO `tsj_articles` VALUES ('13', '10', '25', 'api测试修改4', null, null, '', '', null, null, null, null, null, null, null, '0', '0', '1588061545', '1592553067', '1', null, '');
INSERT INTO `tsj_articles` VALUES ('14', '0', '0', 'api测试', null, null, null, null, null, null, null, null, null, null, null, '0', '0', '1592466298', '1592466298', '1', null, null);

-- ----------------------------
-- Table structure for tsj_type
-- ----------------------------
DROP TABLE IF EXISTS `tsj_type`;
CREATE TABLE `tsj_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `up_id` int(10) unsigned DEFAULT '0',
  `level` smallint(1) DEFAULT '1',
  `order_by` int(10) DEFAULT '0',
  `name` varchar(20) DEFAULT NULL,
  `title` varchar(50) NOT NULL,
  `subtitle` varchar(50) DEFAULT NULL,
  `cover` varchar(255) DEFAULT NULL,
  `summary` varchar(255) DEFAULT NULL,
  `bg_pic` varchar(255) DEFAULT NULL,
  `bg_color` varchar(10) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT '1',
  `updated_at` int(10) unsigned DEFAULT NULL,
  `created_at` int(10) unsigned DEFAULT NULL,
  `covers` varchar(255) DEFAULT NULL,
  `seoTitle` varchar(255) DEFAULT NULL,
  `seoDescription` varchar(255) DEFAULT NULL,
  `seoKeywords` varchar(255) DEFAULT NULL,
  `english_name` varchar(255) DEFAULT NULL COMMENT '拼音简写',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='分类表';

-- ----------------------------
-- Records of tsj_type
-- ----------------------------
INSERT INTO `tsj_type` VALUES ('2', '0', '1', null, null, '分类2', null, '', null, null, null, null, '1', '1579159328', '1572240124', null, '', '', '', null);
INSERT INTO `tsj_type` VALUES ('3', '0', '1', null, 'product', '1-2', null, null, null, null, null, null, '1', '1579159478', '1572240133', null, '', '', '', null);
INSERT INTO `tsj_type` VALUES ('25', '0', '1', null, 'news', '第二级子类', null, null, null, null, null, null, '1', '1575615344', '1575615344', null, '', '', '', null);
INSERT INTO `tsj_type` VALUES ('22', '26', '3', null, 'product', '第二级子类', null, null, null, null, null, null, '1', '1579161988', '1574648923', null, '', '', '', null);

-- ----------------------------
-- Table structure for tsj_user
-- ----------------------------
DROP TABLE IF EXISTS `tsj_user`;
CREATE TABLE `tsj_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_reset_token` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `access_token` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invalid_at` int(11) DEFAULT NULL,
  `mobile` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `group` tinyint(1) DEFAULT NULL,
  `avatar` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `created_ip` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login_at` int(11) DEFAULT NULL,
  `login_times` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `last_login_ip` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `allowance` int(11) DEFAULT NULL,
  `allowance_updated_at` int(11) DEFAULT NULL,
  `qq` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of tsj_user
-- ----------------------------
INSERT INTO `tsj_user` VALUES ('1', 'admin', '7FyBcW4u0KcCgp1NQ57F1OrgArgNDhtn', '$2y$13$l85pu7pFMGnkTpp8oeYjZOPN8NzH.1DJMOtpSowDKAFfGUuc9ASM2', null, 'h7DaDNW0nKOaeYNnaJ1rSUZKYiXPcbRi_1603078499', '1595659068', '', null, '9', 'http://yii.vue.test/data/upload/20101913135056283.jpg', '1', null, '1603261294', '314', '1597641216', '1603263217', '127.0.0.1', '99999', '1592560959', null);
