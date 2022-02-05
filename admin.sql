/*
 Navicat Premium Data Transfer

 Source Server         : 本地
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : 127.0.0.1:3306
 Source Schema         : tp51layui

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 17/11/2021 21:27:58
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cg_account_record
-- ----------------------------
DROP TABLE IF EXISTS `cg_account_record`;
CREATE TABLE `cg_account_record`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `desc` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '简介',
  `create_time` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `total_free` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `user_id` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 42 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '账户变动记录' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cg_account_record
-- ----------------------------
INSERT INTO `cg_account_record` VALUES (1, '平台充值', '1636558379', '购买商机-19元，余额143.01元', 3);
INSERT INTO `cg_account_record` VALUES (2, '购买商机', '1636558475', '-57元，余额86.01元', 3);
INSERT INTO `cg_account_record` VALUES (3, '购买商机', '1636558556', '-19元，余额67.01元', 3);
INSERT INTO `cg_account_record` VALUES (4, '平台充值', '1636558582', '账户余额+1元，余额67.02元', NULL);
INSERT INTO `cg_account_record` VALUES (5, '平台充值', '1636558748', '+1元，余额67.03元', 4);
INSERT INTO `cg_account_record` VALUES (6, '平台充值', '1636558819', '+0.01元，余额67.04元', 4);
INSERT INTO `cg_account_record` VALUES (7, '开通vip', '1636558839', '微信支付0.01元', 4);
INSERT INTO `cg_account_record` VALUES (8, '购买商机', '1636637214', '-19元，余额67元', 3);
INSERT INTO `cg_account_record` VALUES (9, '购买商机', '1636637254', '-20元，余额47元', 3);
INSERT INTO `cg_account_record` VALUES (10, '购买商机', '1636639559', '-19元，余额28元', 3);
INSERT INTO `cg_account_record` VALUES (11, '购买商机', '1636639566', '-19元，余额9元', 3);
INSERT INTO `cg_account_record` VALUES (12, '商机退款', NULL, '账户余额+19，余额28', 3);
INSERT INTO `cg_account_record` VALUES (13, '商机退款', NULL, '账户余额+19，余额47', 3);
INSERT INTO `cg_account_record` VALUES (14, '商机退款', '1636641369', '账户余额+19，余额66', 3);
INSERT INTO `cg_account_record` VALUES (15, '商机退款', '1636641392', '账户余额+20元，余额86', 3);
INSERT INTO `cg_account_record` VALUES (16, '商机退款', '1636641402', '账户余额+19元，余额105', 3);
INSERT INTO `cg_account_record` VALUES (17, '购买商机', '1636644122', '-19元，余额86元', 3);
INSERT INTO `cg_account_record` VALUES (18, '购买商机', '1636645814', '-19元，余额67元', 3);
INSERT INTO `cg_account_record` VALUES (19, '商机退款', '1636699942', '账户余额+19元，余额86', 3);
INSERT INTO `cg_account_record` VALUES (20, '购买商机', '1636774862', '-20元，余额66元', 3);
INSERT INTO `cg_account_record` VALUES (21, '购买商机', '1636788062', '-30元，余额36元', 3);
INSERT INTO `cg_account_record` VALUES (22, '购买商机', '1636788647', '-60元，余额939元', 3);
INSERT INTO `cg_account_record` VALUES (23, '购买商机', '1636789057', '-29元，余额38.04元', 4);
INSERT INTO `cg_account_record` VALUES (24, '商机退款', '1636789089', '账户余额+29元，余额67.04', 4);
INSERT INTO `cg_account_record` VALUES (25, '购买商机', '1636789109', '-58元，余额9.04元', 4);
INSERT INTO `cg_account_record` VALUES (26, '购买商机', '1636789292', '-29元，余额910元', 3);
INSERT INTO `cg_account_record` VALUES (27, '购买商机', '1636789957', '-20元，余额890元', 3);
INSERT INTO `cg_account_record` VALUES (28, '购买商机', '1636790125', '-60元，余额830元', 3);
INSERT INTO `cg_account_record` VALUES (29, '购买商机', '1636790226', '-38元，余额792元', 3);
INSERT INTO `cg_account_record` VALUES (30, '购买商机', '1636814186', '-60元，余额939.04元', 4);
INSERT INTO `cg_account_record` VALUES (31, '购买商机', '1636814226', '-29元，余额910.04元', 4);
INSERT INTO `cg_account_record` VALUES (32, '购买商机', '1636814241', '-19元，余额891.04元', 4);
INSERT INTO `cg_account_record` VALUES (33, '购买商机', '1636814277', '-38元，余额853.04元', 4);
INSERT INTO `cg_account_record` VALUES (34, '购买商机', '1636814346', '-29元，余额824.04元', 4);
INSERT INTO `cg_account_record` VALUES (35, '购买商机', '1636814354', '-63元，余额761.04元', 4);
INSERT INTO `cg_account_record` VALUES (36, '购买商机', '1636814359', '-54元，余额707.04元', 4);
INSERT INTO `cg_account_record` VALUES (37, '购买商机', '1636814456', '-297元，余额410.04元', 4);
INSERT INTO `cg_account_record` VALUES (38, '购买商机', '1636814479', '-57元，余额735元', 3);
INSERT INTO `cg_account_record` VALUES (39, '购买商机', '1636817336', '-19元，余额716元', 3);
INSERT INTO `cg_account_record` VALUES (40, '开通vip', '1636826716', '微信支付0.01元', 3);
INSERT INTO `cg_account_record` VALUES (41, '购买商机', '1636827463', '-38元，余额678元', 3);

-- ----------------------------
-- Table structure for cg_admin
-- ----------------------------
DROP TABLE IF EXISTS `cg_admin`;
CREATE TABLE `cg_admin`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '姓名',
  `phone` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '手机',
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '用户名',
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '密码',
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '邮箱',
  `group_id` int(10) NULL DEFAULT NULL,
  `str` varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cg_admin
-- ----------------------------
INSERT INTO `cg_admin` VALUES (1, '总管理', '', 'admin', 'admin123', '765130113@qq.com', 1, '3RTDtiFhGH7V');

-- ----------------------------
-- Table structure for cg_auth_menu
-- ----------------------------
DROP TABLE IF EXISTS `cg_auth_menu`;
CREATE TABLE `cg_auth_menu`  (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `pid` smallint(6) NOT NULL DEFAULT 0,
  `title` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '菜单名称',
  `link` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '链接',
  `icon` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '字体图标',
  `sort` smallint(6) NULL DEFAULT NULL,
  `show` int(11) NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `link`(`link`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 49 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '菜单表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cg_auth_menu
-- ----------------------------
INSERT INTO `cg_auth_menu` VALUES (1, 0, '系统设置', '/#Setting', 'layui-icon-home', 0, 1);
INSERT INTO `cg_auth_menu` VALUES (2, 1, '菜单管理', '/Admin/Menu/Index', NULL, 1, 1);
INSERT INTO `cg_auth_menu` VALUES (3, 1, '用户管理', '/Admin/Admin/Index', NULL, 2, 1);
INSERT INTO `cg_auth_menu` VALUES (4, 1, '用户组管理', '/Admin/Group/Index', NULL, 3, 1);
INSERT INTO `cg_auth_menu` VALUES (5, 0, '权限分组(所有权限)', '#auth', NULL, NULL, 0);
INSERT INTO `cg_auth_menu` VALUES (6, 5, '菜单新增权限', 'menuadd', NULL, NULL, 0);
INSERT INTO `cg_auth_menu` VALUES (7, 5, '菜单编辑权限', 'menuedit', NULL, NULL, 0);
INSERT INTO `cg_auth_menu` VALUES (8, 5, '用户创建权限', 'adminadd', NULL, NULL, 0);
INSERT INTO `cg_auth_menu` VALUES (9, 5, '用户修改权限', 'adminedit', NULL, NULL, 0);
INSERT INTO `cg_auth_menu` VALUES (10, 5, '用户组新增', 'groupadd', NULL, NULL, 0);
INSERT INTO `cg_auth_menu` VALUES (11, 5, '用户组编辑', 'groupedit', NULL, NULL, 0);
INSERT INTO `cg_auth_menu` VALUES (12, 5, '用户删除权限', 'del_admin', '', NULL, 0);
INSERT INTO `cg_auth_menu` VALUES (13, 5, '用户组删除权限', 'del_group', '', NULL, 0);
INSERT INTO `cg_auth_menu` VALUES (14, 5, '菜单删除权限', 'del_menu', '', NULL, 0);
INSERT INTO `cg_auth_menu` VALUES (33, 0, '资源发布', 'resources', '', NULL, 1);
INSERT INTO `cg_auth_menu` VALUES (38, 33, '资源列表', '/admin/resources/index', '', NULL, 1);
INSERT INTO `cg_auth_menu` VALUES (39, 0, '充值规则', 'pay_rule', '', NULL, 1);
INSERT INTO `cg_auth_menu` VALUES (40, 39, '规则列表', '/admin/pay_rule/index', '', NULL, 1);
INSERT INTO `cg_auth_menu` VALUES (41, 0, '全局配置', 'config', '', NULL, 1);
INSERT INTO `cg_auth_menu` VALUES (42, 41, '全局配置', '/admin/config/index', '', NULL, 1);
INSERT INTO `cg_auth_menu` VALUES (43, 0, '平台订单', 'payorder', '', NULL, 1);
INSERT INTO `cg_auth_menu` VALUES (44, 43, '充值订单', '/admin/pay_order/index', '', NULL, 1);
INSERT INTO `cg_auth_menu` VALUES (45, 43, '商机订单', '/admin/resources_order/index', '', NULL, 1);
INSERT INTO `cg_auth_menu` VALUES (46, 43, '商机退款', '/admin/refund_record/index', '', NULL, 1);
INSERT INTO `cg_auth_menu` VALUES (47, 0, '用户管理', 'user', '', NULL, 1);
INSERT INTO `cg_auth_menu` VALUES (48, 47, '用户列表', '/admin/user/index', '', NULL, 1);

-- ----------------------------
-- Table structure for cg_config
-- ----------------------------
DROP TABLE IF EXISTS `cg_config`;
CREATE TABLE `cg_config`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `value` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
  `remarks` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 21 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cg_config
-- ----------------------------
INSERT INTO `cg_config` VALUES (1, 'appid', 'wxbb6c579a61ee0a98', '公众号appid');
INSERT INTO `cg_config` VALUES (2, 'appsecret', 'f1b22b3f3872fc3ad95e5aefdd17930a', '公众号秘钥');
INSERT INTO `cg_config` VALUES (3, 'mch_id', '1614816039', '商户号mch_id');
INSERT INTO `cg_config` VALUES (4, 'key', 'shebEICAIgou8833cg12333asddmmsar', '微信商户key');
INSERT INTO `cg_config` VALUES (5, 'vip_money', '0.01', '开通vip所需金额');
INSERT INTO `cg_config` VALUES (6, 'about_us', '<p>ceshi 123123123</p>', '关于我们');
INSERT INTO `cg_config` VALUES (12, 'vip_time', '5', 'vip购买时间 分钟制');
INSERT INTO `cg_config` VALUES (13, 'kefu_img', 'http://xxxxxxx.top/00e93901213fb80e7bec2e06dd81382eb9389a503e9b.jpg', '客服二维码');
INSERT INTO `cg_config` VALUES (14, 'gold_vip_img', 'http://xxxxxxx.top/huiyuan.png', '购买会员背景图');
INSERT INTO `cg_config` VALUES (15, 'fuwuxieyi', '服务协议', '服务协议');
INSERT INTO `cg_config` VALUES (17, 'default_province_list', '998,999', '默认选择接听省份');
INSERT INTO `cg_config` VALUES (20, 'use_to_know', '<p>14</p>', '使用须知');

-- ----------------------------
-- Table structure for cg_group
-- ----------------------------
DROP TABLE IF EXISTS `cg_group`;
CREATE TABLE `cg_group`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '分组名称',
  `rules` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '权限规则',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户组------角色表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cg_group
-- ----------------------------
INSERT INTO `cg_group` VALUES (1, '超管组', NULL);

-- ----------------------------
-- Table structure for cg_pay_order
-- ----------------------------
DROP TABLE IF EXISTS `cg_pay_order`;
CREATE TABLE `cg_pay_order`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `pay_rule_id` int(11) NULL DEFAULT NULL COMMENT '充值规则id',
  `out_trade_no` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '商户订单编号',
  `total_fee` float(8, 2) NOT NULL COMMENT '支付金额',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '支付状态0未支付1已支付',
  `wx_id` int(11) NULL DEFAULT NULL COMMENT '微信商户订单号',
  `create_time` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '创建时间',
  `pay_time` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '支付时间',
  `pay_type` tinyint(1) NULL DEFAULT NULL COMMENT '1开通vip，2账户充值',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 108 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '用户充值记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cg_pay_order
-- ----------------------------
INSERT INTO `cg_pay_order` VALUES (40, 3, -1, '202111092250421274211159', 300.00, 0, NULL, '1636469442', NULL, NULL);
INSERT INTO `cg_pay_order` VALUES (41, 3, -1, '202111092251042165536516', 123.00, 0, NULL, '1636469464', NULL, NULL);
INSERT INTO `cg_pay_order` VALUES (42, 5, -1, '202111092309104013523174', 0.00, 0, NULL, '1636470550', NULL, NULL);
INSERT INTO `cg_pay_order` VALUES (43, 6, -1, '202111092324079588896566', 300.00, 0, NULL, '1636471447', NULL, NULL);
INSERT INTO `cg_pay_order` VALUES (44, 6, -1, '202111092324128396587488', 1000.00, 0, NULL, '1636471452', NULL, NULL);
INSERT INTO `cg_pay_order` VALUES (45, 6, -1, '202111092324164905356922', 5000.00, 0, NULL, '1636471456', NULL, NULL);
INSERT INTO `cg_pay_order` VALUES (46, 3, -1, '202111092326148806050925', 1.00, 0, NULL, '1636471574', NULL, NULL);
INSERT INTO `cg_pay_order` VALUES (47, 3, -1, '202111101112549860291562', 300.00, 0, NULL, '1636513974', NULL, NULL);
INSERT INTO `cg_pay_order` VALUES (48, 3, -1, '202111102120159412781349', 300.00, 0, NULL, '1636550415', NULL, NULL);
INSERT INTO `cg_pay_order` VALUES (49, 3, -1, '202111102202113625553579', 1.00, 0, NULL, '1636552931', NULL, NULL);
INSERT INTO `cg_pay_order` VALUES (50, 3, -1, '202111102203365586822318', 300.00, 0, NULL, '1636553016', NULL, NULL);
INSERT INTO `cg_pay_order` VALUES (51, 1, -1, '202111102206158228290657', 500.00, 0, NULL, '1636553175', NULL, NULL);
INSERT INTO `cg_pay_order` VALUES (52, 3, -1, '202111102208165895179034', 0.00, 0, NULL, '1636553296', NULL, NULL);
INSERT INTO `cg_pay_order` VALUES (53, 4, -1, '202111102217541319226450', 0.00, 0, NULL, '1636553874', NULL, NULL);
INSERT INTO `cg_pay_order` VALUES (54, 4, -1, '202111102218052903013912', 0.00, 0, NULL, '1636553885', NULL, NULL);
INSERT INTO `cg_pay_order` VALUES (55, 4, -1, '202111102220116228542795', 0.00, 0, NULL, '1636554011', NULL, NULL);
INSERT INTO `cg_pay_order` VALUES (56, 6, -1, '202111102221095403478685', 1.00, 0, NULL, '1636554069', NULL, NULL);
INSERT INTO `cg_pay_order` VALUES (57, 6, -1, '202111102221208990278164', 1.00, 0, NULL, '1636554080', NULL, NULL);
INSERT INTO `cg_pay_order` VALUES (58, 4, -1, '202111102221211667889496', 0.00, 0, NULL, '1636554081', NULL, NULL);
INSERT INTO `cg_pay_order` VALUES (59, 6, -1, '202111102221246013646117', 1.00, 0, NULL, '1636554084', NULL, NULL);
INSERT INTO `cg_pay_order` VALUES (60, 6, -1, '202111102221391847451365', 1.00, 0, NULL, '1636554099', NULL, NULL);
INSERT INTO `cg_pay_order` VALUES (61, 6, -1, '202111102222042220272415', 0.01, 1, NULL, '1636554124', '2021-11-10 22:31:44', NULL);
INSERT INTO `cg_pay_order` VALUES (62, 6, -1, '202111102222251476779596', 0.00, 0, NULL, '1636554145', NULL, NULL);
INSERT INTO `cg_pay_order` VALUES (63, 6, -1, '202111102223124321298304', 300.00, 0, NULL, '1636554192', NULL, NULL);
INSERT INTO `cg_pay_order` VALUES (64, 4, -1, '202111102244204380782552', 0.00, 0, NULL, '1636555460', NULL, NULL);
INSERT INTO `cg_pay_order` VALUES (65, 3, -1, '202111102244299950333915', 300.00, 0, NULL, '1636555469', NULL, NULL);
INSERT INTO `cg_pay_order` VALUES (66, 6, -1, '202111102249168514018813', 0.00, 0, NULL, '1636555756', NULL, NULL);
INSERT INTO `cg_pay_order` VALUES (67, 4, -1, '202111102252273067613952', 0.00, 0, NULL, '1636555947', NULL, NULL);
INSERT INTO `cg_pay_order` VALUES (68, 4, -1, '202111102253116410360540', 0.01, 0, NULL, '1636555991', NULL, NULL);
INSERT INTO `cg_pay_order` VALUES (69, 4, -1, '202111102253171907341706', 0.01, 0, NULL, '1636555997', NULL, NULL);
INSERT INTO `cg_pay_order` VALUES (70, 4, -1, '202111102254389393419763', 0.01, 0, NULL, '1636556078', NULL, 2);
INSERT INTO `cg_pay_order` VALUES (71, 4, -1, '202111102255067459657253', 0.01, 0, NULL, '1636556106', NULL, 2);
INSERT INTO `cg_pay_order` VALUES (72, 4, -1, '202111102256566979263158', 0.01, 0, NULL, '1636556216', NULL, 2);
INSERT INTO `cg_pay_order` VALUES (73, 4, -1, '202111102300476135727107', 0.01, 0, NULL, '1636556447', NULL, 2);
INSERT INTO `cg_pay_order` VALUES (74, 4, -1, '202111102302513108968593', 0.01, 1, NULL, '1636556571', '2021-11-10 23:02:57', 2);
INSERT INTO `cg_pay_order` VALUES (75, 4, -1, '202111102303381196079432', 0.01, 1, NULL, '1636556618', '2021-11-10 23:03:48', 1);
INSERT INTO `cg_pay_order` VALUES (76, 3, -1, '202111102306502384155130', 0.01, 0, NULL, '1636556810', NULL, 2);
INSERT INTO `cg_pay_order` VALUES (77, 4, -1, '202111102336186172683759', 0.01, 1, NULL, '1636558578', '2021-11-10 23:36:22', 2);
INSERT INTO `cg_pay_order` VALUES (78, 4, -1, '202111102339031620885552', 0.01, 1, NULL, '1636558743', '2021-11-10 23:39:08', 2);
INSERT INTO `cg_pay_order` VALUES (79, 4, -1, '202111102340141330193988', 0.01, 1, NULL, '1636558814', '2021-11-10 23:40:19', 2);
INSERT INTO `cg_pay_order` VALUES (80, 4, -1, '202111102340339454688877', 0.01, 1, NULL, '1636558833', '2021-11-10 23:40:39', 1);
INSERT INTO `cg_pay_order` VALUES (81, 6, -1, '202111110006592672232046', 0.01, 0, NULL, '1636560419', NULL, 1);
INSERT INTO `cg_pay_order` VALUES (82, 6, -1, '202111110008293372795750', 300.00, 0, NULL, '1636560509', NULL, 2);
INSERT INTO `cg_pay_order` VALUES (83, 6, -1, '202111110008381300587352', 10000.00, 0, NULL, '1636560518', NULL, 2);
INSERT INTO `cg_pay_order` VALUES (84, 6, -1, '202111110008469783594091', 200.00, 0, NULL, '1636560526', NULL, 2);
INSERT INTO `cg_pay_order` VALUES (85, 6, -1, '202111110008563112041393', 200.00, 0, NULL, '1636560536', NULL, 2);
INSERT INTO `cg_pay_order` VALUES (86, 6, -1, '202111110008564144798148', 200.00, 0, NULL, '1636560536', NULL, 2);
INSERT INTO `cg_pay_order` VALUES (87, 6, -1, '202111110008588232584724', 200.00, 0, NULL, '1636560538', NULL, 2);
INSERT INTO `cg_pay_order` VALUES (88, 6, -1, '202111110009018301276788', 200.00, 0, NULL, '1636560541', NULL, 2);
INSERT INTO `cg_pay_order` VALUES (89, 6, -1, '202111110009246089980773', 200.00, 0, NULL, '1636560564', NULL, 2);
INSERT INTO `cg_pay_order` VALUES (90, 6, -1, '202111110009268935699133', 200.00, 0, NULL, '1636560566', NULL, 2);
INSERT INTO `cg_pay_order` VALUES (91, 6, -1, '202111110009283916293304', 200.00, 0, NULL, '1636560568', NULL, 2);
INSERT INTO `cg_pay_order` VALUES (92, 6, -1, '202111110009283810329947', 200.00, 0, NULL, '1636560568', NULL, 2);
INSERT INTO `cg_pay_order` VALUES (93, 6, -1, '202111110009286033874094', 200.00, 0, NULL, '1636560568', NULL, 2);
INSERT INTO `cg_pay_order` VALUES (94, 6, -1, '202111110009283246324699', 200.00, 0, NULL, '1636560568', NULL, 2);
INSERT INTO `cg_pay_order` VALUES (95, 6, -1, '202111110009301230290940', 200.00, 0, NULL, '1636560570', NULL, 2);
INSERT INTO `cg_pay_order` VALUES (96, 6, -1, '202111110009435796430801', 200.00, 0, NULL, '1636560583', NULL, 2);
INSERT INTO `cg_pay_order` VALUES (97, 6, -1, '202111110009468304580048', 200.00, 0, NULL, '1636560586', NULL, 2);
INSERT INTO `cg_pay_order` VALUES (98, 6, -1, '202111110009494819827199', 200.00, 0, NULL, '1636560589', NULL, 2);
INSERT INTO `cg_pay_order` VALUES (99, 3, -1, '202111112122595966414882', 0.01, 0, NULL, '1636636979', NULL, 1);
INSERT INTO `cg_pay_order` VALUES (100, 3, -1, '20211113111291731', 3000.00, 0, NULL, '1636774907', NULL, 2);
INSERT INTO `cg_pay_order` VALUES (101, 3, -1, '20211113113170831', 2000.00, 0, NULL, '1636774912', NULL, 2);
INSERT INTO `cg_pay_order` VALUES (102, 3, -1, '20211113117220611', 0.01, 0, NULL, '1636774929', NULL, 2);
INSERT INTO `cg_pay_order` VALUES (103, 3, -1, '20211113117179403', 300.00, 0, NULL, '1636774946', NULL, 2);
INSERT INTO `cg_pay_order` VALUES (104, 3, -1, '20211113119617897', 0.01, 0, NULL, '1636774956', NULL, 1);
INSERT INTO `cg_pay_order` VALUES (105, 3, -1, '20211114024310187', 0.01, 1, NULL, '1636826711', '2021-11-14 02:05:16', 1);
INSERT INTO `cg_pay_order` VALUES (106, 3, -1, '20211114028540291', 300.00, 0, NULL, '1636827090', NULL, 2);
INSERT INTO `cg_pay_order` VALUES (107, 3, -1, '20211114197311878', 300.00, 0, NULL, '1636890678', NULL, 2);

-- ----------------------------
-- Table structure for cg_pay_rule
-- ----------------------------
DROP TABLE IF EXISTS `cg_pay_rule`;
CREATE TABLE `cg_pay_rule`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `min_pay` int(11) NULL DEFAULT NULL COMMENT '最低冲多少',
  `percentage` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '百分比',
  `give_money` int(11) NULL DEFAULT NULL COMMENT '送多少',
  `remarks` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '介绍',
  `sort` int(11) NULL DEFAULT 0 COMMENT '排序',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '充值规则' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cg_pay_rule
-- ----------------------------
INSERT INTO `cg_pay_rule` VALUES (3, 500, '送6%', 30, '实得530', 9);
INSERT INTO `cg_pay_rule` VALUES (4, 300, '送3%', 10, '实得310', 10);
INSERT INTO `cg_pay_rule` VALUES (5, 1000, '送9%', 90, '实得1090', 8);
INSERT INTO `cg_pay_rule` VALUES (6, 2000, '送12%', 240, '实得2240', 7);
INSERT INTO `cg_pay_rule` VALUES (7, 3000, '送14%', 440, '实得3440', 6);
INSERT INTO `cg_pay_rule` VALUES (8, 5000, '送16%', 810, '实得5810', 5);

-- ----------------------------
-- Table structure for cg_province
-- ----------------------------
DROP TABLE IF EXISTS `cg_province`;
CREATE TABLE `cg_province`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `province_id` int(11) NULL DEFAULT NULL,
  `province_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 33 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cg_province
-- ----------------------------
INSERT INTO `cg_province` VALUES (1, 998, '国外');
INSERT INTO `cg_province` VALUES (2, 999, '国内');
INSERT INTO `cg_province` VALUES (3, 1, '北京');
INSERT INTO `cg_province` VALUES (4, 2, '天津');
INSERT INTO `cg_province` VALUES (5, 3, '河北');
INSERT INTO `cg_province` VALUES (6, 4, '山西');
INSERT INTO `cg_province` VALUES (7, 5, '内蒙古');
INSERT INTO `cg_province` VALUES (8, 6, '辽宁');
INSERT INTO `cg_province` VALUES (9, 7, '吉林');
INSERT INTO `cg_province` VALUES (10, 8, '黑龙江');
INSERT INTO `cg_province` VALUES (11, 9, '上海');
INSERT INTO `cg_province` VALUES (12, 10, '江苏');
INSERT INTO `cg_province` VALUES (13, 11, '浙江');
INSERT INTO `cg_province` VALUES (14, 12, '安徽');
INSERT INTO `cg_province` VALUES (15, 13, '福建');
INSERT INTO `cg_province` VALUES (16, 14, '江西');
INSERT INTO `cg_province` VALUES (17, 15, '山东');
INSERT INTO `cg_province` VALUES (18, 16, '河南');
INSERT INTO `cg_province` VALUES (19, 17, '湖北');
INSERT INTO `cg_province` VALUES (20, 18, '湖南');
INSERT INTO `cg_province` VALUES (21, 19, '广东');
INSERT INTO `cg_province` VALUES (22, 20, '广西');
INSERT INTO `cg_province` VALUES (23, 21, '海南');
INSERT INTO `cg_province` VALUES (24, 22, '重庆');
INSERT INTO `cg_province` VALUES (25, 23, '四川');
INSERT INTO `cg_province` VALUES (26, 24, '贵州');
INSERT INTO `cg_province` VALUES (27, 25, '云南');
INSERT INTO `cg_province` VALUES (28, 27, '陕西');
INSERT INTO `cg_province` VALUES (29, 28, '甘肃');
INSERT INTO `cg_province` VALUES (30, 29, '青海');
INSERT INTO `cg_province` VALUES (31, 30, '宁夏');
INSERT INTO `cg_province` VALUES (32, 31, '新疆');

-- ----------------------------
-- Table structure for cg_refund_record
-- ----------------------------
DROP TABLE IF EXISTS `cg_refund_record`;
CREATE TABLE `cg_refund_record`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resources_id` int(11) NULL DEFAULT NULL COMMENT '商机id',
  `resources_order_id` int(11) NULL DEFAULT NULL COMMENT '购买记录id',
  `user_id` int(11) NULL DEFAULT NULL COMMENT '用户id',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0未审核。1已退款。2驳回',
  `create_time` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '申请时间',
  `refund_time` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '后台处理时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 15 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '退款申请记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cg_refund_record
-- ----------------------------
INSERT INTO `cg_refund_record` VALUES (1, 7, 1, 1, 0, '1636119062', NULL);
INSERT INTO `cg_refund_record` VALUES (2, 6, 1, 2, 0, '1636119321', NULL);
INSERT INTO `cg_refund_record` VALUES (3, 21, 6, 3, 1, '1636119321', '1636699942');
INSERT INTO `cg_refund_record` VALUES (4, 21, 7, 3, 1, '1636639007', '1636641402');
INSERT INTO `cg_refund_record` VALUES (5, 12, 13, 3, 1, '1636639256', '1636641392');
INSERT INTO `cg_refund_record` VALUES (6, 19, 12, 3, 1, '1636639314', '1636641369');
INSERT INTO `cg_refund_record` VALUES (7, 17, 15, 3, 1, '1636639711', '1636641312');
INSERT INTO `cg_refund_record` VALUES (8, 19, 14, 3, 1, '1636639712', '1636641283');
INSERT INTO `cg_refund_record` VALUES (9, 73, 21, 4, 1, '1636789079', '1636789089');
INSERT INTO `cg_refund_record` VALUES (10, 12, 18, 3, 0, '1636792271', NULL);
INSERT INTO `cg_refund_record` VALUES (11, 74, 20, 3, 0, '1636792347', NULL);
INSERT INTO `cg_refund_record` VALUES (12, 38, 23, 3, 0, '1636792355', NULL);
INSERT INTO `cg_refund_record` VALUES (13, 7, 35, 3, 0, '1636814511', NULL);
INSERT INTO `cg_refund_record` VALUES (14, 17, 26, 3, 0, '1636814512', NULL);

-- ----------------------------
-- Table structure for cg_resources
-- ----------------------------
DROP TABLE IF EXISTS `cg_resources`;
CREATE TABLE `cg_resources`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '商机标题',
  `contacts` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '联系人',
  `province` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '省',
  `city` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '市',
  `remarks` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '备注',
  `money` int(11) NOT NULL COMMENT '商机金额',
  `phone` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '联系方式',
  `buy_num` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '购买次数',
  `create_time` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '创建时间',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '状态，1正常 2售空',
  `shengyu_num` int(11) NULL DEFAULT NULL,
  `province_list` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '推送区域',
  `need_num` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '需求数量',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 76 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '商机列表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cg_resources
-- ----------------------------
INSERT INTO `cg_resources` VALUES (6, '采购一台1吨生物质蒸汽锅炉', '王先生', '山东菏泽', NULL, '我在山东菏泽，我这边是食品厂使用，需要采购一台1吨生物质蒸汽锅炉，请生产厂家或经销商与我联系，谢谢', 20, '15088888888', '3', '1635575067', 1, 0, '998,999', NULL);
INSERT INTO `cg_resources` VALUES (7, '锅炉暖气', '马先生', '江西赣州', NULL, '这边是……………………', 19, '15831484972', '3', '1635582274', 1, 0, '998,999', NULL);
INSERT INTO `cg_resources` VALUES (8, '测试商机1', '李先生', '河南郑州', NULL, '详细信息详细信息详细信息详细信息详细信息', 20, '15689231150', '3', '1635585626', 1, 0, '998,999,6,12,18,24', NULL);
INSERT INTO `cg_resources` VALUES (11, '需要一套电脑', '李先生', '北京东城区', NULL, '详细信息详细信息详细信息详细信息详细信息详细信息详细信息详细信息详细信息详细信息详细信息详细信息详细信息详细信息详细信息详细信息详细信息详细信息详细信息详细信息详细信息详细信息详细信息详细信息详细信息', 20, '15689231150', '3', '1635585626', 1, 0, '998,999', NULL);
INSERT INTO `cg_resources` VALUES (12, '电风扇电风扇电风扇电风扇电风扇电风扇电风扇电风扇电风扇电风扇电风扇电风扇电风扇电风扇电风扇电风扇电风扇', '李先生', '江西赣州', NULL, '我在山东菏泽，我这边是食品厂使用，需要采购一台1吨生物质蒸汽锅炉，请生产厂家或经销商与我联系，谢谢', 20, '12345888888', '4', '1635585626', 1, 0, '998,999', NULL);
INSERT INTO `cg_resources` VALUES (13, '吹风机吹风机吹风机吹风机吹风机吹风机吹风机吹风机吹风机吹风机吹风机', '刀先生', '湖南长沙', NULL, '湖南长沙湖南长沙湖南长沙湖南长沙湖南长沙湖南长沙湖南长沙湖南长沙湖南长沙', 19, '12345678900', '3', '1635585626', 1, 0, '998,999', NULL);
INSERT INTO `cg_resources` VALUES (14, '西瓜刀西瓜刀西瓜刀西瓜刀西瓜刀西瓜刀西瓜刀', '刀先生', '湖南长沙', NULL, '湖南长沙湖南长沙湖南长沙湖南长沙湖南长沙湖南长沙湖南长沙', 21, '13598789789', '3', '1635585626', 1, 0, '998,999', NULL);
INSERT INTO `cg_resources` VALUES (15, '洗衣机洗衣机洗衣机洗衣机洗衣机洗衣机洗衣机洗衣机', '刀先生', '湖南长沙', NULL, '洗衣机洗衣机洗衣机洗衣机洗衣机洗衣机洗衣机洗衣机洗衣机洗衣机洗衣机', 18, '13045555555', '3', '1635585626', 1, 0, '998,999', NULL);
INSERT INTO `cg_resources` VALUES (16, '刮胡刀刮胡刀刮胡刀刮胡刀刮胡刀刮胡刀刮胡刀刮胡刀刮胡刀', '刀先生', '湖南长沙', NULL, '湖南长沙', 99, '13045555555', '3', '1635585626', 1, 0, '998,999', NULL);
INSERT INTO `cg_resources` VALUES (17, '测试商机', '张三', '河南郑州', NULL, '详细信息', 19, '15088881234', '4', '1636449074', 1, 0, '4,6,7,8,9,10', '2');
INSERT INTO `cg_resources` VALUES (38, '河北地区专享商机', '张先生', '河北衡水市', NULL, '收购一台0.5吨的加湿器，浴室内使用，请联系我', 29, '15833033030', '3', '1636786018', 1, 0, '999', '3');
INSERT INTO `cg_resources` VALUES (73, '安徽专属', '张三', '安徽合肥', NULL, '1234551512451', 29, '15033033303', '4', '1636788400', 1, 0, '12', '3');
INSERT INTO `cg_resources` VALUES (74, '安徽专属222', '卢先生', '安徽合肥', NULL, '9999999', 20, '150330333333', '3', '1636788509', 1, 0, '12', '3');
INSERT INTO `cg_resources` VALUES (75, '测试123----', '李四', '河北邢台', NULL, '五吨蒸发器', 19, '15033033333', '3', '1636815499', 1, 0, '999,1,2,7,8', '8');

-- ----------------------------
-- Table structure for cg_resources_order
-- ----------------------------
DROP TABLE IF EXISTS `cg_resources_order`;
CREATE TABLE `cg_resources_order`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resources_id` int(11) NOT NULL COMMENT '商机id',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `buy_num` int(11) NOT NULL COMMENT '购买次数',
  `create_time` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '创建时间',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1已完成2已退款',
  `pay_money` int(11) NULL DEFAULT NULL COMMENT '扣去平台金额',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 38 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '购买商机记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cg_resources_order
-- ----------------------------
INSERT INTO `cg_resources_order` VALUES (1, 6, 2, 1, '1566666666', 1, 4);
INSERT INTO `cg_resources_order` VALUES (2, 6, 1, 1, '1636121814', 1, 20);
INSERT INTO `cg_resources_order` VALUES (3, 6, 1, 2, '1636121858', 1, 40);
INSERT INTO `cg_resources_order` VALUES (4, 22, 3, 1, '1636465401', 1, 19);
INSERT INTO `cg_resources_order` VALUES (5, 23, 3, 3, '1636465416', 1, 57);
INSERT INTO `cg_resources_order` VALUES (6, 21, 3, 1, '1636465548', 2, 19);
INSERT INTO `cg_resources_order` VALUES (7, 21, 3, 1, '1636553002', 2, 19);
INSERT INTO `cg_resources_order` VALUES (8, 22, 4, 2, '1636556670', 1, 38);
INSERT INTO `cg_resources_order` VALUES (9, 21, 4, 1, '1636558379', 1, 19);
INSERT INTO `cg_resources_order` VALUES (10, 20, 4, 3, '1636558475', 1, 57);
INSERT INTO `cg_resources_order` VALUES (11, 19, 4, 1, '1636558556', 1, 19);
INSERT INTO `cg_resources_order` VALUES (12, 19, 3, 1, '1636637214', 2, 19);
INSERT INTO `cg_resources_order` VALUES (13, 12, 3, 1, '1636637254', 2, 20);
INSERT INTO `cg_resources_order` VALUES (14, 19, 3, 1, '1636639559', 2, 19);
INSERT INTO `cg_resources_order` VALUES (15, 17, 3, 1, '1636639566', 2, 19);
INSERT INTO `cg_resources_order` VALUES (16, 18, 3, 1, '1636644122', 1, 19);
INSERT INTO `cg_resources_order` VALUES (17, 18, 3, 1, '1636645814', 1, 19);
INSERT INTO `cg_resources_order` VALUES (18, 12, 3, 1, '1636774862', 1, 20);
INSERT INTO `cg_resources_order` VALUES (19, 66, 3, 1, '1636788062', 1, 30);
INSERT INTO `cg_resources_order` VALUES (20, 74, 3, 3, '1636788647', 1, 60);
INSERT INTO `cg_resources_order` VALUES (21, 73, 4, 1, '1636789057', 2, 29);
INSERT INTO `cg_resources_order` VALUES (22, 73, 4, 2, '1636789109', 1, 58);
INSERT INTO `cg_resources_order` VALUES (23, 38, 3, 1, '1636789292', 1, 29);
INSERT INTO `cg_resources_order` VALUES (24, 12, 3, 1, '1636789957', 1, 20);
INSERT INTO `cg_resources_order` VALUES (25, 11, 3, 3, '1636790125', 1, 60);
INSERT INTO `cg_resources_order` VALUES (26, 17, 3, 2, '1636790226', 1, 38);
INSERT INTO `cg_resources_order` VALUES (27, 8, 4, 3, '1636814186', 1, 60);
INSERT INTO `cg_resources_order` VALUES (28, 38, 4, 1, '1636814226', 1, 29);
INSERT INTO `cg_resources_order` VALUES (29, 13, 4, 1, '1636814241', 1, 19);
INSERT INTO `cg_resources_order` VALUES (30, 13, 4, 2, '1636814277', 1, 38);
INSERT INTO `cg_resources_order` VALUES (31, 38, 4, 1, '1636814346', 1, 29);
INSERT INTO `cg_resources_order` VALUES (32, 14, 4, 3, '1636814354', 1, 63);
INSERT INTO `cg_resources_order` VALUES (33, 15, 4, 3, '1636814359', 1, 54);
INSERT INTO `cg_resources_order` VALUES (34, 16, 4, 3, '1636814456', 1, 297);
INSERT INTO `cg_resources_order` VALUES (35, 7, 3, 3, '1636814479', 1, 57);
INSERT INTO `cg_resources_order` VALUES (36, 75, 3, 1, '1636817336', 1, 19);
INSERT INTO `cg_resources_order` VALUES (37, 75, 3, 2, '1636827463', 1, 38);

-- ----------------------------
-- Table structure for cg_user
-- ----------------------------
DROP TABLE IF EXISTS `cg_user`;
CREATE TABLE `cg_user`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `nickname` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `headimgurl` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `money` float(8, 2) NULL DEFAULT 0.00,
  `vip_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否vip',
  `create_time` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '创建时间',
  `token` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '用户token',
  `expiration_time` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '用户到期时间',
  `province_list` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '接收商机推送',
  `is_jieshou` tinyint(1) NULL DEFAULT 1 COMMENT '1开启 0关闭',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci COMMENT = '用户表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of cg_user
-- ----------------------------
INSERT INTO `cg_user` VALUES (1, NULL, 'buhui', '123', 140.00, 0, NULL, '456789', NULL, NULL, 1);
INSERT INTO `cg_user` VALUES (2, NULL, '哈哈哈', '456', 200.00, 0, NULL, '123456', '', '999,998,1,2,3,4,5,6,7,8,9,10', 1);
INSERT INTO `cg_user` VALUES (3, 'oWNnj5pVYBlfaEpI7YCk8AEcpa-8', '色即是空', 'https://thirdwx.qlogo.cn/mmopen/vi_32/PiajxSqBRaELZX4gM3nUyUsNkDRia0R9IhrQ7QAgLGXVmZvwtmGe4LguHY5VYWqPBDlkel2m0dkre9jQq56ichK5Q/132', 678.00, 1, '1636039026', '3946280c57551f593068a031e9f64ab4', '1668362716', '998,999', 1);
INSERT INTO `cg_user` VALUES (4, 'oWNnj5t55V8gN9CLDMXuNPN0VYkc', '不悔：', 'https://thirdwx.qlogo.cn/mmopen/vi_32/Uqibs8xBdDxIQAEZKMNUOtOMib5dhWuQMl03AcD9AY2y87l9R1uX2K7G4u3WntmicibLxxyhf13rlkPvpHAvGhHJiag/132', 410.00, 0, '1636448971', 'f4a9ff9d50828504280399a76f443344', '', '998,999,4,6,7,8,9,10,12', 1);
INSERT INTO `cg_user` VALUES (5, 'oWNnj5sLI7QLTSpLvizPA6H4HkFA', '狂徒张三', 'https://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTKBrqTKvvrFicp9sNZnGzIic9SMrZ8MF5XyKUNpE9skk47Y67s0ae5oQtR15k4GNVtJRZVlBaeHJjng/132', 0.00, 0, '1636470538', '3c500df1f2ddd54f296160492d92371a', NULL, '998,999', 1);
INSERT INTO `cg_user` VALUES (6, 'oWNnj5jcguKz50_afIBh0cK-jyRg', '.', 'https://thirdwx.qlogo.cn/mmopen/vi_32/1yLQzvkUKibBgTAzOcpeeqTk6qlIpYGvRu8AawVto82XSIibJVjyJ8J04X9VPqEIoyO8Snaico5icicphexub1icVC2Q/132', 100.00, 0, '1636471442', '94cc7768671599d1b993bbfe57913f7c', '', '2,3', 1);
INSERT INTO `cg_user` VALUES (7, 'oWNnj5pclEzdpcw0beV1c-J9iVgQ', '抱着月亮', 'https://thirdwx.qlogo.cn/mmopen/vi_32/9SgmFicKhRHYG3UYIIX9FiaH0B4q1CmicAXw5abbyRtIzIXt5jEwxI7SVWaUn8kO3HDB1K66gIWP6edibe3m11MgHA/132', 0.00, 0, '1636890773', '31297d4ebba28192cec4cc9c7fd3e400', NULL, '998,999', 1);
INSERT INTO `cg_user` VALUES (8, 'oWNnj5gAUlcnedcEGycFr5Ch0M_w', '山东格润特环保科技', 'https://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTI1MqK71ITCXcic3KatiayXr7vz2iaMaktg6cHtlTfDdr4HvdxibMRuVribL0pr7wEn513aCkCXghXrVicQ/132', 0.00, 0, '1636945684', '35885dfe0c1415acfc3a716a5369ec19', NULL, '998,999', 1);
INSERT INTO `cg_user` VALUES (9, 'oWNnj5hxP9_WtHT6IAn9bTiQ-nI0', '不吃香菜', 'https://thirdwx.qlogo.cn/mmopen/vi_32/QTOYsswsT5plV7Kv5qFrdSlDMEzqTJVQXrc6QesRF21jytXgquCPP86fXGQu3rs13ksPxofhn5HhMicDCjffvFQ/132', 0.00, 0, '1637117668', 'da0d705528dc2989bb87e4a6c80c3e9b', NULL, '998,999', 1);

SET FOREIGN_KEY_CHECKS = 1;
