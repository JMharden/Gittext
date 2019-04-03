/*
 Navicat Premium Data Transfer

 Source Server         : x
 Source Server Type    : MySQL
 Source Server Version : 50536
 Source Host           : 47.102.145.210:3306
 Source Schema         : test

 Target Server Type    : MySQL
 Target Server Version : 50536
 File Encoding         : 65001

 Date: 03/04/2019 19:16:27
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for dd_account_info
-- ----------------------------
DROP TABLE IF EXISTS `dd_account_info`;
CREATE TABLE `dd_account_info`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NULL DEFAULT NULL COMMENT '用户ID',
  `total_amount` int(11) NULL DEFAULT NULL COMMENT '总充值金额',
  `received_amount` int(11) NULL DEFAULT NULL COMMENT '佣金已领取金额',
  `pending_amount` int(11) NULL DEFAULT NULL COMMENT '佣金未领取金额',
  `last_receive_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最近佣金领取时间',
  `available_amount` int(11) NULL DEFAULT NULL COMMENT '可用金额',
  `last_recharge_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最近充值时间',
  `recharge_count` int(11) NULL DEFAULT NULL COMMENT '充值次数',
  `withdraw_count` int(11) NULL DEFAULT NULL COMMENT '提现次数',
  `withdraw_amount` int(11) NULL DEFAULT NULL COMMENT '提现总金额',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_activity_info
-- ----------------------------
DROP TABLE IF EXISTS `dd_activity_info`;
CREATE TABLE `dd_activity_info`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT '活动标题',
  `content` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT '活动内容',
  `url` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT '跳转url',
  `send_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '发送时间',
  `expire_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '过期时间',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `create_user` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT '创建人',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 5 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_admin_account
-- ----------------------------
DROP TABLE IF EXISTS `dd_admin_account`;
CREATE TABLE `dd_admin_account`  (
  `id` bigint(20) NULL DEFAULT NULL,
  `account_name` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `password` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_login_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_login_ip` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `modify_time` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `status` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `roles` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL
) ENGINE = MyISAM CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_admin_login_log
-- ----------------------------
DROP TABLE IF EXISTS `dd_admin_login_log`;
CREATE TABLE `dd_admin_login_log`  (
  `id` bigint(20) NULL DEFAULT NULL,
  `account_name` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `login_time` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `login_ip` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL
) ENGINE = MyISAM CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_agent_info
-- ----------------------------
DROP TABLE IF EXISTS `dd_agent_info`;
CREATE TABLE `dd_agent_info`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NULL DEFAULT NULL,
  `discount` float(11, 1) NOT NULL COMMENT '折扣',
  `tel` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '联系方式',
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `modify_time` datetime NULL DEFAULT NULL,
  `create_time` datetime NULL DEFAULT NULL,
  `active` tinyint(1) NULL DEFAULT NULL COMMENT '是否有效',
  `order_no` varchar(0) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '交易订单号',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for dd_alipay_log
-- ----------------------------
DROP TABLE IF EXISTS `dd_alipay_log`;
CREATE TABLE `dd_alipay_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `out_trade_no` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `trade_no` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `total_fee` float(10, 2) NULL DEFAULT NULL,
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '日志记录时间',
  `seller_email` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '卖家支付宝',
  `buyer_email` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '买家支付宝',
  `seller_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '卖家支付宝id',
  `buyer_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '买家支付宝id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '支付宝支付记录' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_all_num
-- ----------------------------
DROP TABLE IF EXISTS `dd_all_num`;
CREATE TABLE `dd_all_num`  (
  `id` int(11) NOT NULL,
  `all_num` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Table structure for dd_apply
-- ----------------------------
DROP TABLE IF EXISTS `dd_apply`;
CREATE TABLE `dd_apply`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '被申请用户id',
  `sid` int(11) NOT NULL COMMENT '好友申请人id',
  `text` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '申请说明',
  `status` tinyint(2) NOT NULL COMMENT '1为待通过，2为通过',
  `stime` int(11) NOT NULL COMMENT '申请时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for dd_brokerage_log
-- ----------------------------
DROP TABLE IF EXISTS `dd_brokerage_log`;
CREATE TABLE `dd_brokerage_log`  (
  `user_id` bigint(20) NULL DEFAULT NULL,
  `type` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `amount` int(11) NULL DEFAULT NULL
) ENGINE = MyISAM CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_buylog
-- ----------------------------
DROP TABLE IF EXISTS `dd_buylog`;
CREATE TABLE `dd_buylog`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `money` float(10, 2) NOT NULL,
  `kid` int(11) NOT NULL COMMENT '结果',
  `kid1` int(11) NOT NULL,
  `kid2` int(11) NOT NULL,
  `kid3` int(11) NOT NULL,
  `kid4` int(11) NOT NULL,
  `kid5` int(11) NOT NULL,
  `kid6` int(11) NOT NULL,
  `kid7` int(11) NOT NULL,
  `kid8` int(11) NOT NULL,
  `kid9` int(11) NOT NULL,
  `kid10` int(11) NOT NULL,
  `starttime` int(11) NOT NULL,
  `endtime` int(11) NOT NULL,
  `yingmoney` float NOT NULL,
  `status` tinyint(4) NOT NULL COMMENT '1进行中，2结束',
  `isid` int(11) NOT NULL,
  `isname` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `true` tinyint(2) NOT NULL,
  `handfee` decimal(10, 2) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_challenge_info
-- ----------------------------
DROP TABLE IF EXISTS `dd_challenge_info`;
CREATE TABLE `dd_challenge_info`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `declaration` char(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `create_user` int(11) NULL DEFAULT NULL COMMENT '发布者ID',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `gold_amount` int(11) NULL DEFAULT NULL COMMENT '获得金币',
  `deposit` int(11) NULL DEFAULT NULL COMMENT '押金',
  `status` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT '是否有效',
  `challenge_count` int(11) NULL DEFAULT NULL,
  `win_count` int(11) NULL DEFAULT NULL COMMENT '胜利次数',
  `fail_count` int(11) NULL DEFAULT NULL COMMENT '失败次数',
  `each_cost_amount` int(11) NULL DEFAULT NULL COMMENT '每次挑战金额',
  `Available_amount` int(11) NULL DEFAULT NULL COMMENT '可用余额',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_charge
-- ----------------------------
DROP TABLE IF EXISTS `dd_charge`;
CREATE TABLE `dd_charge`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NULL DEFAULT 0 COMMENT '用户ID',
  `money` float(10, 2) NULL DEFAULT 0.00 COMMENT '金额',
  `payway` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '支付方式',
  `create_time` int(11) NULL DEFAULT 0 COMMENT '创建时间',
  `status` tinyint(3) NULL DEFAULT 0 COMMENT '状态，0待支付，1已完成',
  `pay_time` int(11) NULL DEFAULT 0 COMMENT '支付完成时间',
  `paid` float(10, 2) NULL DEFAULT 0.00 COMMENT '已支付',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_charge_log
-- ----------------------------
DROP TABLE IF EXISTS `dd_charge_log`;
CREATE TABLE `dd_charge_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NULL DEFAULT NULL,
  `money` float(10, 2) NULL DEFAULT 0.00,
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `create_time` int(11) NULL DEFAULT NULL,
  `chou` tinyint(4) NOT NULL,
  `is_pay` tinyint(4) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for dd_chou
-- ----------------------------
DROP TABLE IF EXISTS `dd_chou`;
CREATE TABLE `dd_chou`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NULL DEFAULT NULL,
  `money` float(10, 2) NULL DEFAULT 0.00 COMMENT '扣除数量',
  `status` tinyint(3) NULL DEFAULT 0 COMMENT '0未支付,1未开奖，2未中奖，3已中奖',
  `reward` float(10, 2) NULL DEFAULT NULL COMMENT '奖金',
  `reward_time` int(11) NULL DEFAULT NULL COMMENT '开奖时间',
  `create_time` int(11) NULL DEFAULT NULL,
  `payway` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '支付方式',
  `paid` float(10, 2) NULL DEFAULT 0.00 COMMENT '已支付金额',
  `expense` float(10, 2) NULL DEFAULT 0.00 COMMENT '推荐奖',
  `nickname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '昵称',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_club_info
-- ----------------------------
DROP TABLE IF EXISTS `dd_club_info`;
CREATE TABLE `dd_club_info`  (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `tel` int(11) NULL DEFAULT NULL COMMENT '手机号',
  `openid` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `club_head` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT '俱乐部头像',
  `club_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '俱乐部名称',
  `ower_id` int(11) NULL DEFAULT NULL COMMENT '创建人ID',
  `ower_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '创建人名称',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  `declaration` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '社团宣言',
  `club_notice` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '俱乐部公告',
  `area` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '创建地区',
  `create_fee` int(11) NULL DEFAULT NULL COMMENT '创建费用',
  `create_number` int(11) NOT NULL COMMENT '俱乐部人数上限 ',
  `level` tinyint(2) NULL DEFAULT NULL COMMENT '俱乐部等级',
  `ercode` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT '二维码',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 68 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_club_infomation
-- ----------------------------
DROP TABLE IF EXISTS `dd_club_infomation`;
CREATE TABLE `dd_club_infomation`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `club_id` int(11) NOT NULL COMMENT '俱乐部ID',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `type` tinyint(2) NOT NULL COMMENT '1为申请加入记录，2为俱乐部成员任职',
  `status` int(11) NOT NULL COMMENT '1为通过2为不通过3为申请中',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 21 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for dd_config
-- ----------------------------
DROP TABLE IF EXISTS `dd_config`;
CREATE TABLE `dd_config`  (
  `name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `value` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_domain
-- ----------------------------
DROP TABLE IF EXISTS `dd_domain`;
CREATE TABLE `dd_domain`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_lock` tinyint(4) NOT NULL,
  `is_qr_code` tinyint(4) NOT NULL,
  `is_home` tinyint(4) NOT NULL,
  `is_type` tinyint(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '域名类型：0游戏域名 1推广域名',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 171 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_email_read
-- ----------------------------
DROP TABLE IF EXISTS `dd_email_read`;
CREATE TABLE `dd_email_read`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `email_id` int(11) NOT NULL,
  `status` tinyint(4) NULL DEFAULT NULL COMMENT '1为已删，2为已读，3为未读',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for dd_expense
-- ----------------------------
DROP TABLE IF EXISTS `dd_expense`;
CREATE TABLE `dd_expense`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `buyer_id` int(11) NULL DEFAULT NULL,
  `divided_money` float(10, 2) NULL DEFAULT 0.00 COMMENT '分成金额',
  `money` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '总金额',
  `create_time` int(11) NULL DEFAULT NULL,
  `type` tinyint(3) NULL DEFAULT NULL COMMENT '1.上下级提成 ，2俱乐部提成',
  `level` tinyint(3) NULL DEFAULT NULL COMMENT '层级(-1 系统，0 俱乐部，1：上级1,2 上级2,3 上级3)',
  `match_id` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '状态（1：已领取，0：未领取）',
  `modify_time` datetime NULL DEFAULT NULL COMMENT '领取时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 759605 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '佣金记录' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_expense_withdraw
-- ----------------------------
DROP TABLE IF EXISTS `dd_expense_withdraw`;
CREATE TABLE `dd_expense_withdraw`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NULL DEFAULT NULL,
  `money` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `create_time` datetime NULL DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '佣金提现记录' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for dd_fantime
-- ----------------------------
DROP TABLE IF EXISTS `dd_fantime`;
CREATE TABLE `dd_fantime`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lasttime` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `shifei` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_finance_log
-- ----------------------------
DROP TABLE IF EXISTS `dd_finance_log`;
CREATE TABLE `dd_finance_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `money` float(10, 2) NOT NULL,
  `action` tinyint(4) NOT NULL,
  `create_time` bigint(20) NOT NULL,
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 153 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_game_detail_log
-- ----------------------------
DROP TABLE IF EXISTS `dd_game_detail_log`;
CREATE TABLE `dd_game_detail_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auser_id` int(11) NOT NULL COMMENT 'a用户id',
  `uname` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `user_step` int(11) NOT NULL COMMENT 'a用户步数',
  `user_result` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `user_score` int(11) NOT NULL COMMENT 'a用户评分',
  `game_id` int(11) NOT NULL COMMENT '游戏ID',
  `start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '游戏开始时间',
  `end_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '游戏结束时间',
  `result` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '1:赢  0 ：输',
  `winner_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '赢+',
  `winner` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `data` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '该对局内所有玩家的数据，可以透传前端的数据',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for dd_game_info
-- ----------------------------
DROP TABLE IF EXISTS `dd_game_info`;
CREATE TABLE `dd_game_info`  (
  `user_id` bigint(20) NULL DEFAULT NULL,
  `game_id` bigint(20) NULL DEFAULT NULL,
  `game_name` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `game_icon` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `game_desc` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = MyISAM CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_gift_code
-- ----------------------------
DROP TABLE IF EXISTS `dd_gift_code`;
CREATE TABLE `dd_gift_code`  (
  `CODE` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_pick` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `picker` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pick_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `type` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `expire_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `remark` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `create_user` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL
) ENGINE = MyISAM CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_gift_log
-- ----------------------------
DROP TABLE IF EXISTS `dd_gift_log`;
CREATE TABLE `dd_gift_log`  (
  `id` bigint(20) NULL DEFAULT NULL,
  `tel` int(11) NULL DEFAULT NULL,
  `openid` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `user_id` bigint(20) NULL DEFAULT NULL,
  `type` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `amount` int(11) NULL DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `code` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL
) ENGINE = MyISAM CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_hongbao
-- ----------------------------
DROP TABLE IF EXISTS `dd_hongbao`;
CREATE TABLE `dd_hongbao`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `type` tinyint(2) NOT NULL,
  `money` float(10, 2) NOT NULL,
  `ying` float(10, 2) NOT NULL,
  `addtime` int(11) NOT NULL,
  `rand` int(11) NOT NULL,
  `chai` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Table structure for dd_login_log
-- ----------------------------
DROP TABLE IF EXISTS `dd_login_log`;
CREATE TABLE `dd_login_log`  (
  `id` char(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `user_id` bigint(20) NULL DEFAULT NULL,
  `login_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ip` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL
) ENGINE = MyISAM CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_order_log
-- ----------------------------
DROP TABLE IF EXISTS `dd_order_log`;
CREATE TABLE `dd_order_log`  (
  `order_id` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `user_id` bigint(20) NULL DEFAULT NULL,
  `recharge_amount` bigint(20) NULL DEFAULT NULL,
  `real_amount` bigint(20) NULL DEFAULT NULL,
  `status` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `pay_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `channel` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL
) ENGINE = MyISAM CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_pay_king_log
-- ----------------------------
DROP TABLE IF EXISTS `dd_pay_king_log`;
CREATE TABLE `dd_pay_king_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单号',
  `userid` int(11) UNSIGNED NOT NULL COMMENT '用户id',
  `money` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '下单金额',
  `total_fee` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '额外盈利',
  `golds` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '订单获取金币数',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态（1.待处理，2.已处理）',
  `ctime` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间',
  `uptime` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 275 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '充值订单' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_pay_order
-- ----------------------------
DROP TABLE IF EXISTS `dd_pay_order`;
CREATE TABLE `dd_pay_order`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单号',
  `trade_sn` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单交易号',
  `prepay_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '预支付订单号',
  `userid` int(11) UNSIGNED NOT NULL COMMENT '用户id',
  `total_fee` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '金额',
  `golds` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '订单获取金币数',
  `detail` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '描述',
  `from` tinyint(1) NOT NULL DEFAULT 0 COMMENT '来源（1：微信，2.支付宝，3生活圈付呗，4掌上云支付）',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态（1.未支付，2.已支付已处理）',
  `ctime` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间',
  `uptime` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 275 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '充值订单' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_pay_record
-- ----------------------------
DROP TABLE IF EXISTS `dd_pay_record`;
CREATE TABLE `dd_pay_record`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `order_sn` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单号',
  `trade_sn` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '交易号',
  `total_fee` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '充值金额',
  `golds` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '获取金币数',
  `amount` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '金币总额',
  `from` smallint(3) UNSIGNED NOT NULL COMMENT '来源（1：微信，2支付宝，3生活圈付呗,，4掌上云支付）',
  `ctime` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 6 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '充值记录' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_pay_return_data
-- ----------------------------
DROP TABLE IF EXISTS `dd_pay_return_data`;
CREATE TABLE `dd_pay_return_data`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `data` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '返回数据data',
  `ctime` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '记录时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 96 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '支付成功返回数据' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_pickup
-- ----------------------------
DROP TABLE IF EXISTS `dd_pickup`;
CREATE TABLE `dd_pickup`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NULL DEFAULT NULL,
  `user_plant_id` int(11) NULL DEFAULT NULL,
  `money` float(10, 2) NULL DEFAULT 0.00,
  `pickup_times` tinyint(3) NULL DEFAULT 0,
  `create_time` int(11) NULL DEFAULT NULL,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `plant_time` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_plant
-- ----------------------------
DROP TABLE IF EXISTS `dd_plant`;
CREATE TABLE `dd_plant`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `num` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '名称',
  `pic` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '图片',
  `price` float(10, 2) NULL DEFAULT 0.00 COMMENT '价格',
  `life_cycle` int(11) NULL DEFAULT 0 COMMENT '生命周期(天)',
  `harvest_value` float(10, 2) NULL DEFAULT 0.00 COMMENT '收货价值',
  `body` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '介绍',
  `can_tou` int(11) NULL DEFAULT 0 COMMENT '可以被偷次数',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '植物' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_play_log
-- ----------------------------
DROP TABLE IF EXISTS `dd_play_log`;
CREATE TABLE `dd_play_log`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NULL DEFAULT NULL,
  `game_id` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `result` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `score` int(11) NULL DEFAULT NULL,
  `start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `end_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `challenge_id` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `type` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT '1为单机，2为1VS1,3为多人',
  `status` tinyint(2) NULL DEFAULT 1 COMMENT '1为游戏中，二为游戏结束',
  `winner` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `winner_id` int(11) NULL DEFAULT NULL,
  `match_id` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT '对局id关联 dd_play_match_info',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 53 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_play_match_info
-- ----------------------------
DROP TABLE IF EXISTS `dd_play_match_info`;
CREATE TABLE `dd_play_match_info`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game_id` int(11) NULL DEFAULT NULL,
  `match_id` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '对战id',
  `ticket_fee` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '总门票金额',
  `player_num` int(11) NULL DEFAULT NULL COMMENT '玩家数量',
  `players` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '玩家',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间后',
  `battle_amount` int(255) NULL DEFAULT NULL COMMENT '单个玩家支出的对战金额',
  `status` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '游戏状态(0进行中，1已结束) ',
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '1 初级场，2 中级场，3 高级场',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 24 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for dd_qqnum
-- ----------------------------
DROP TABLE IF EXISTS `dd_qqnum`;
CREATE TABLE `dd_qqnum`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `num1` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `num2` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `time` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `uptime` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_relation
-- ----------------------------
DROP TABLE IF EXISTS `dd_relation`;
CREATE TABLE `dd_relation`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `parent_id` int(11) NULL DEFAULT NULL,
  `create_time` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for dd_singleplay_log
-- ----------------------------
DROP TABLE IF EXISTS `dd_singleplay_log`;
CREATE TABLE `dd_singleplay_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `mark` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '每局游戏标识',
  `rate` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '点击速率',
  `map` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '地图包',
  `result` int(11) NOT NULL COMMENT '游戏步数',
  `is_challage` tinyint(2) NOT NULL DEFAULT 1 COMMENT '是否发起挑战书1为否2为是',
  `start_time` int(11) NOT NULL COMMENT '游戏开始时间',
  `end_time` int(11) NOT NULL COMMENT '游戏结束时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 79 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for dd_slime
-- ----------------------------
DROP TABLE IF EXISTS `dd_slime`;
CREATE TABLE `dd_slime`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `img` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for dd_sms_log
-- ----------------------------
DROP TABLE IF EXISTS `dd_sms_log`;
CREATE TABLE `dd_sms_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mobile` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '号码,可能是多个号码,所以使用text类型',
  `msg` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '内容，一般不超过60字',
  `result` tinyint(3) NULL DEFAULT 0 COMMENT '返回码，整形',
  `create_time` int(11) NULL DEFAULT 0 COMMENT '发送时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 3517 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_suggest
-- ----------------------------
DROP TABLE IF EXISTS `dd_suggest`;
CREATE TABLE `dd_suggest`  (
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NULL DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `content` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `contact` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `create_time` int(11) NULL DEFAULT NULL,
  `is_del` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`sid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1512 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_user
-- ----------------------------
DROP TABLE IF EXISTS `dd_user`;
CREATE TABLE `dd_user`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login_name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '登录名',
  `login_pass` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '密码',
  `openid` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `bopenid` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `popenid` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `sub_openid` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `nickname` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `headimg` varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `mobile` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `sub_time` int(11) NOT NULL,
  `subscribe` tinyint(4) NOT NULL,
  `parent1` int(11) NOT NULL,
  `parent2` int(11) NOT NULL,
  `parent3` int(11) NOT NULL,
  `agent1` int(11) NOT NULL,
  `agent2` int(11) NOT NULL,
  `agent3` int(11) NOT NULL,
  `money` decimal(10, 2) NOT NULL DEFAULT 100.00 COMMENT '余额',
  `num` int(11) NOT NULL,
  `yingkui` float(10, 2) NOT NULL,
  `expense_total` float(255, 0) NULL DEFAULT NULL,
  `expense_withdraw` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `expense_avail` float(10, 2) NOT NULL DEFAULT 0.00,
  `withdraw` float(10, 2) NOT NULL DEFAULT 0.00,
  `is_tong` tinyint(3) NOT NULL DEFAULT 0,
  `type` tinyint(2) NOT NULL,
  `count_money` decimal(10, 2) NOT NULL DEFAULT 0.00,
  `text` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `xiazhu` float(10, 2) NOT NULL,
  `yingmoney` float(10, 2) NOT NULL,
  `renshu` int(11) NOT NULL,
  `wintegration` int(13) NULL DEFAULT 0 COMMENT '待领取积分',
  `empiric` int(11) NULL DEFAULT 100 COMMENT '经验值',
  `active_point` int(11) NOT NULL DEFAULT 100 COMMENT '活跃度',
  `integration` int(13) NULL DEFAULT 100 COMMENT '积分',
  `club_id` int(11) NOT NULL DEFAULT 0 COMMENT '俱乐部ID',
  `club_role` tinyint(1) NOT NULL COMMENT '俱乐部角色1为部长0成员',
  `join_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_login_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后登录时间',
  `all_login_time` int(13) NULL DEFAULT NULL COMMENT '登录总次数',
  `introducer_id` int(11) NULL DEFAULT NULL COMMENT '推荐人ID',
  `introducer2_id` int(11) NULL DEFAULT NULL COMMENT '第二推荐人ID',
  `introducer3_id` int(11) NULL DEFAULT NULL COMMENT '第三推荐人ID',
  `match_amount` int(255) NULL DEFAULT NULL COMMENT '游戏总对局数',
  `win_amount` int(255) NULL DEFAULT NULL COMMENT '胜场数',
  `is_agents` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '是否代理商',
  `is_club_owner` tinyint(1) NOT NULL COMMENT '是否俱乐部部长  0为否 1为是',
  `rank` int(255) NULL DEFAULT NULL COMMENT '初始值为1200分',
  `stamina` int(255) UNSIGNED NULL DEFAULT 0 COMMENT '体力值 上限为50',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 185 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for dd_user_charge
-- ----------------------------
DROP TABLE IF EXISTS `dd_user_charge`;
CREATE TABLE `dd_user_charge`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `price` decimal(10, 2) NOT NULL,
  `is_pay` tinyint(4) NOT NULL DEFAULT 0,
  `create_time` int(11) NOT NULL,
  `pay_time` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 73 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_user_info
-- ----------------------------
DROP TABLE IF EXISTS `dd_user_info`;
CREATE TABLE `dd_user_info`  (
  `id` bigint(20) NULL DEFAULT NULL,
  `nickname` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `tel` int(13) NULL DEFAULT NULL,
  `openid` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `head_ico` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `active_point` int(13) NULL DEFAULT NULL,
  `integration` int(13) NULL DEFAULT NULL,
  `club_id` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `club_name` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `join_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `create_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `last_login_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最近登录时间',
  `all_login_time` int(13) NULL DEFAULT NULL,
  `introducer_id` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `introducer2_id` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `introducer3_id` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL
) ENGINE = MyISAM CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dd_user_sign
-- ----------------------------
DROP TABLE IF EXISTS `dd_user_sign`;
CREATE TABLE `dd_user_sign`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `username` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户名称',
  `is_sign` tinyint(1) NOT NULL COMMENT '是否签到',
  `sign_time` int(11) NOT NULL COMMENT '签到时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 21 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for dd_withdraw_log
-- ----------------------------
DROP TABLE IF EXISTS `dd_withdraw_log`;
CREATE TABLE `dd_withdraw_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NULL DEFAULT NULL,
  `money` float(10, 2) NULL DEFAULT 0.00,
  `hand_fee` float(10, 2) NULL DEFAULT 0.00 COMMENT '手续费',
  `pay_result` tinyint(3) NULL DEFAULT NULL COMMENT '支付结果',
  `create_time` int(11) NULL DEFAULT 0,
  `status` tinyint(3) NULL DEFAULT 0 COMMENT '0待发放,1已发放',
  `pay_time` int(11) NULL DEFAULT 0 COMMENT '发放时间',
  `server_addr` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `remote_addr` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `return_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `result_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `return_msg` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `err_code_des` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `err_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `payment_no` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 41 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for dd_wuguili
-- ----------------------------
DROP TABLE IF EXISTS `dd_wuguili`;
CREATE TABLE `dd_wuguili`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `money` float(10, 2) NOT NULL,
  `num` int(11) NOT NULL,
  `addtime` int(11) NOT NULL,
  `type` tinyint(2) NOT NULL,
  `ying` float(10, 2) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 11718 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Fixed;

-- ----------------------------
-- Table structure for dd_wxpay_log
-- ----------------------------
DROP TABLE IF EXISTS `dd_wxpay_log`;
CREATE TABLE `dd_wxpay_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(2) NOT NULL,
  `return_code` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `result_code` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `openid` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `bank_type` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `total_fee` float(10, 2) NULL DEFAULT NULL,
  `cash_fee` float(10, 2) NULL DEFAULT NULL,
  `transaction_id` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `out_trade_no` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `attach` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `time_end` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `log_time` int(11) NULL DEFAULT 0,
  `is_gong` tinyint(2) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 3461 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for t_withdraw_log
-- ----------------------------
DROP TABLE IF EXISTS `t_withdraw_log`;
CREATE TABLE `t_withdraw_log`  (
  `id` bigint(20) NULL DEFAULT NULL,
  `user_id` bigint(20) NULL DEFAULT NULL,
  `amount` bigint(20) NULL DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `type` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `account` char(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `ip` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE = MyISAM CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
