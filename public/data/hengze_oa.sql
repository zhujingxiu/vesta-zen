-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2020-04-16 16:32:06
-- 服务器版本： 5.6.43
-- PHP 版本： 7.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `hengze_oa`
--

-- --------------------------------------------------------

--
-- 表的结构 `admin_menu`
--

CREATE TABLE `admin_menu` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uri` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permission` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `admin_menu`
--

INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES
(1, 0, 1, '仪表盘', 'fa-bar-chart', '/', NULL, NULL, '2020-03-23 18:01:25'),
(2, 0, 10, '系统管理', 'fa-tasks', NULL, NULL, NULL, '2020-04-14 22:38:47'),
(3, 2, 12, '管理用户', 'fa-users', 'auth/users', NULL, NULL, '2020-04-14 22:38:47'),
(4, 2, 13, '管理角色', 'fa-user', 'auth/roles', NULL, NULL, '2020-04-14 22:38:47'),
(5, 2, 14, '权限配置', 'fa-ban', 'auth/permissions', NULL, NULL, '2020-04-14 22:38:47'),
(6, 2, 15, '菜单配置', 'fa-bars', 'auth/menu', NULL, NULL, '2020-04-14 22:38:47'),
(7, 2, 16, '操作日志', 'fa-history', 'auth/logs', NULL, NULL, '2020-04-14 22:38:47'),
(8, 9, 3, '服务器管理', 'fa-database', '/servers', '*', '2020-03-19 17:28:34', '2020-03-23 17:59:12'),
(9, 0, 2, '站点系统管理', 'fa-sitemap', NULL, NULL, '2020-03-19 18:38:59', '2020-03-23 17:59:12'),
(10, 9, 4, '站点管理', 'fa-codepen', '/sites', '*', '2020-03-19 18:40:11', '2020-03-23 17:59:12'),
(11, 2, 11, '参数配置', 'fa-cogs', '/settings', NULL, '2020-03-23 00:04:52', '2020-04-14 22:38:47'),
(12, 9, 5, '站点语言', 'fa-headphones', '/site-languages', NULL, '2020-03-23 17:57:48', '2020-03-23 17:59:12'),
(13, 9, 6, '站点模板', 'fa-envelope', '/site-templates', NULL, '2020-03-23 17:58:57', '2020-03-23 17:59:12'),
(14, 0, 7, '域名解析', 'fa-bars', NULL, NULL, '2020-03-26 00:05:39', '2020-03-26 00:05:54'),
(15, 14, 8, '域名管理', 'fa-bars', '/domains', NULL, '2020-03-26 00:06:24', '2020-04-14 22:38:47'),
(16, 14, 9, 'CloudFlare账户管理', 'fa-bars', '/cloud-flare', NULL, '2020-03-26 00:07:08', '2020-04-14 22:38:47'),
(18, 2, 17, 'Media manager', 'fa-file', 'media', NULL, '2020-04-05 23:52:08', '2020-04-14 22:38:47'),
(19, 2, 18, 'Backup', 'fa-copy', 'backup', NULL, '2020-04-05 23:52:25', '2020-04-14 22:38:47'),
(20, 2, 19, 'Scheduling', 'fa-clock-o', 'scheduling', NULL, '2020-04-05 23:52:35', '2020-04-14 22:38:47');

-- --------------------------------------------------------

--
-- 表的结构 `admin_operation_log`
--

CREATE TABLE `admin_operation_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `input` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `admin_operation_log`
--

INSERT INTO `admin_operation_log` (`id`, `user_id`, `path`, `method`, `ip`, `input`, `created_at`, `updated_at`) VALUES
(1, 8, 'admin', 'GET', '127.0.0.1', '[]', '2020-04-16 00:31:37', '2020-04-16 00:31:37');

-- --------------------------------------------------------

--
-- 表的结构 `admin_permissions`
--

CREATE TABLE `admin_permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `http_method` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `http_path` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `admin_permissions`
--

INSERT INTO `admin_permissions` (`id`, `name`, `slug`, `http_method`, `http_path`, `created_at`, `updated_at`) VALUES
(1, 'All permission', '*', '', '*', NULL, NULL),
(2, 'Dashboard', 'dashboard', 'GET', '/', NULL, NULL),
(3, 'Login', 'auth.login', '', '/auth/login\r\n/auth/logout', NULL, NULL),
(4, 'User setting', 'auth.setting', 'GET,PUT', '/auth/setting', NULL, NULL),
(5, 'Auth management', 'auth.management', '', '/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs', NULL, NULL),
(6, 'Admin helpers', 'ext.helpers', '', '/helpers/*', '2020-03-26 01:14:02', '2020-03-26 01:14:02'),
(7, 'Redis Manager', 'ext.redis-manager', '', '/redis*', '2020-04-05 23:51:54', '2020-04-05 23:51:54'),
(8, 'Media manager', 'ext.media-manager', '', '/media*', '2020-04-05 23:52:08', '2020-04-05 23:52:08'),
(9, 'Backup', 'ext.backup', '', '/backup*', '2020-04-05 23:52:25', '2020-04-05 23:52:25'),
(10, 'Scheduling', 'ext.scheduling', '', '/scheduling*', '2020-04-05 23:52:35', '2020-04-05 23:52:35'),
(11, '服务器列表', 'servers', 'GET,HEAD', '/servers', '2020-04-14 22:44:47', '2020-04-15 18:17:53'),
(12, '站点列表', 'sites', 'GET,HEAD', '/sites', '2020-04-14 22:46:00', '2020-04-15 18:17:35'),
(13, '服务器添加站点', 'add-site', 'GET,POST', '/servers/ad-site', '2020-04-15 17:54:42', '2020-04-15 17:54:42'),
(14, '重置站点后台密码', 'reset-site-passwd', 'POST', '/sites/reset-pass', '2020-04-15 18:04:13', '2020-04-15 18:04:13');

-- --------------------------------------------------------

--
-- 表的结构 `admin_roles`
--

CREATE TABLE `admin_roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `admin_roles`
--

INSERT INTO `admin_roles` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'administrator', '2020-03-19 16:59:57', '2020-03-19 16:59:57'),
(2, '业务组', 'bussiness', '2020-04-14 22:41:23', '2020-04-14 22:41:23'),
(3, '管理组', 'manager', '2020-04-14 22:42:46', '2020-04-14 22:42:46'),
(4, '开发组', 'developer', '2020-04-14 22:43:27', '2020-04-14 22:43:27');

-- --------------------------------------------------------

--
-- 表的结构 `admin_role_menu`
--

CREATE TABLE `admin_role_menu` (
  `role_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `admin_role_menu`
--

INSERT INTO `admin_role_menu` (`role_id`, `menu_id`, `created_at`, `updated_at`) VALUES
(1, 2, NULL, NULL),
(1, 8, NULL, NULL),
(1, 9, NULL, NULL),
(1, 10, NULL, NULL),
(1, 11, NULL, NULL),
(1, 12, NULL, NULL),
(1, 13, NULL, NULL),
(1, 14, NULL, NULL),
(1, 15, NULL, NULL),
(1, 16, NULL, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `admin_role_permissions`
--

CREATE TABLE `admin_role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `admin_role_permissions`
--

INSERT INTO `admin_role_permissions` (`role_id`, `permission_id`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL),
(2, 2, NULL, NULL),
(2, 3, NULL, NULL),
(3, 4, NULL, NULL),
(3, 5, NULL, NULL),
(3, 6, NULL, NULL),
(3, 8, NULL, NULL),
(4, 1, NULL, NULL),
(4, 3, NULL, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `admin_role_users`
--

CREATE TABLE `admin_role_users` (
  `role_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `admin_role_users`
--

INSERT INTO `admin_role_users` (`role_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `name`, `avatar`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$rI9.cGWUNiC3eXj2U/ZfbeoI7HXjP9ZQi8fpPmtYFDv7kwRYEPZxS', 'Administrator', NULL, 's1z3dWp2uv8rEkxUCqnucBV3fazRyoiVLUEOW0o5HVbH0zlQq03gd48UnE8x', '2020-03-19 16:59:57', '2020-03-19 16:59:57'),
(8, 'zhujingxiu', '$2y$10$DcSRZLPHNBW.eauzHhJ6M.s2lX1L1p6TMEtvSsxeucidL0XKqzH6e', '朱景修', 'images/17b5628571a1e454b1a4185051c5ffae.jpeg', 'XAo8DawVbfHWaLsEq2EeZ8JSRtfupYpihXVPE8pLAryS0Pd6KimeCtpWjBl9', '2020-04-15 17:43:46', '2020-04-15 17:43:46');

-- --------------------------------------------------------

--
-- 表的结构 `admin_user_permissions`
--

CREATE TABLE `admin_user_permissions` (
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `admin_user_permissions`
--

INSERT INTO `admin_user_permissions` (`user_id`, `permission_id`, `created_at`, `updated_at`) VALUES
(8, 2, NULL, NULL),
(8, 11, NULL, NULL),
(8, 12, NULL, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `hz_cloud_flare`
--

CREATE TABLE `hz_cloud_flare` (
  `id` int(11) NOT NULL,
  `auth_email` varchar(128) NOT NULL COMMENT 'Auth Email',
  `auth_key` varchar(128) NOT NULL COMMENT 'Auth Key',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态',
  `admin_id` int(11) DEFAULT '0' COMMENT '操作人',
  `create_at` datetime DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime DEFAULT NULL COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='CloudFlare账户';

--
-- 转存表中的数据 `hz_cloud_flare`
--

INSERT INTO `hz_cloud_flare` (`id`, `auth_email`, `auth_key`, `status`, `admin_id`, `create_at`, `update_at`) VALUES
(1, '760609240@qq.com', '3e8d78952fbd09ceef065b9114ad868d01373', 1, 1, '2020-03-26 08:21:56', '2020-03-26 08:21:56'),
(2, 'test-test@qq.com', '3e8d78952fbd09ceef065b9114ad868d12345', 1, 1, '2020-03-26 08:30:48', '2020-03-26 08:30:48'),
(3, 'zhujingxiu@qq.com', 'cb1040a688c30bce8a0538153f49456d111ba', 1, 1, '2020-03-30 02:56:27', '2020-03-30 02:56:27');

-- --------------------------------------------------------

--
-- 表的结构 `hz_domains`
--

CREATE TABLE `hz_domains` (
  `id` int(11) NOT NULL,
  `domain` varchar(128) NOT NULL COMMENT '域名',
  `user` varchar(32) DEFAULT NULL COMMENT '用户',
  `cf_id` int(11) DEFAULT NULL COMMENT 'CloudFlare',
  `zone_id` varchar(64) DEFAULT NULL COMMENT 'CF-Zone',
  `status` tinyint(11) NOT NULL DEFAULT '1' COMMENT '状态',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '操作人',
  `create_at` datetime DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime DEFAULT NULL COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='域名';

--
-- 转存表中的数据 `hz_domains`
--

INSERT INTO `hz_domains` (`id`, `domain`, `user`, `cf_id`, `zone_id`, `status`, `admin_id`, `create_at`, `update_at`) VALUES
(1, 'homeuom.com', 'admin', 1, 'adb82dfbc53ee080ce583cc5815b6df5', 1, 1, '2020-03-26 08:29:29', '2020-03-26 08:29:29'),
(2, 'example.app', 'admin', 1, NULL, 1, 1, '2020-03-26 08:30:00', '2020-03-26 08:30:00'),
(3, 'abc.cn', 'admin', 2, NULL, 1, 1, '2020-03-26 08:31:14', '2020-03-26 08:31:14'),
(4, 'mausen.cn', 'admin', 3, 'd92396bf7913a58ed49dd6393e34cce5', 1, 1, '2020-03-30 02:58:43', '2020-03-30 02:58:43');

-- --------------------------------------------------------

--
-- 表的结构 `hz_ext`
--

CREATE TABLE `hz_ext` (
  `id` int(11) NOT NULL,
  `preview` varchar(128) NOT NULL,
  `coder` text NOT NULL,
  `content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `hz_servers`
--

CREATE TABLE `hz_servers` (
  `id` int(11) NOT NULL,
  `name` varchar(126) NOT NULL COMMENT '名称',
  `ip` char(15) NOT NULL COMMENT 'IP',
  `group_id` int(11) DEFAULT NULL COMMENT '服务器组',
  `sites` smallint(6) NOT NULL DEFAULT '0' COMMENT '站点数',
  `root` varchar(128) DEFAULT NULL COMMENT 'root密码',
  `user` varchar(64) DEFAULT NULL COMMENT '用户名',
  `pass` varchar(128) DEFAULT NULL COMMENT '密码',
  `status` tinyint(4) NOT NULL COMMENT '状态',
  `admin_id` int(11) DEFAULT '0' COMMENT '操作人',
  `create_at` datetime DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime DEFAULT NULL COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='服务器';

--
-- 转存表中的数据 `hz_servers`
--

INSERT INTO `hz_servers` (`id`, `name`, `ip`, `group_id`, `sites`, `root`, `user`, `pass`, `status`, `admin_id`, `create_at`, `update_at`) VALUES
(1, 'newserver235.111', '192.168.235.111', 1, 122, '1q2w3e', 'admin', 'DGO5tEYPyH', 1, 1, '2020-03-20 00:00:00', '2020-04-01 03:59:45'),
(2, 'newserver235.222', '192.168.235.222', 1, 106, '1q2w3e', 'admin', 'SovywHBh5r', 1, 1, '2020-03-20 00:00:00', '2020-03-31 07:40:55'),
(9, 'newserver1099', '46.4.85.58', 1, 125, '4PWE8c8Df8xkPP', 'admin', 'tgJoyl5pru', 1, 1, '2020-03-20 00:00:00', '2020-04-15 04:11:45'),
(10, 'German', '192.168.235.235', 1, 1, '1q2w3e', 'admin', '123ewqewq', 1, 1, '2020-04-14 06:52:22', '2020-04-14 06:52:22');

-- --------------------------------------------------------

--
-- 表的结构 `hz_server_groups`
--

CREATE TABLE `hz_server_groups` (
  `id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL COMMENT '组名称',
  `status` tinyint(4) NOT NULL COMMENT '状态',
  `admin_id` int(11) NOT NULL COMMENT '操作人',
  `create_at` datetime NOT NULL COMMENT '创建时间',
  `update_at` datetime NOT NULL COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='服务器组';

--
-- 转存表中的数据 `hz_server_groups`
--

INSERT INTO `hz_server_groups` (`id`, `name`, `status`, `admin_id`, `create_at`, `update_at`) VALUES
(1, '站群组', 1, 1, '2020-03-21 00:00:00', '2020-03-21 00:00:00');

-- --------------------------------------------------------

--
-- 表的结构 `hz_settings`
--

CREATE TABLE `hz_settings` (
  `id` int(11) NOT NULL,
  `conf_key` varchar(64) NOT NULL,
  `conf_value` mediumtext NOT NULL,
  `tab` varchar(32) NOT NULL COMMENT '配置组',
  `json` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否JSON',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统参数';

--
-- 转存表中的数据 `hz_settings`
--

INSERT INTO `hz_settings` (`id`, `conf_key`, `conf_value`, `tab`, `json`, `status`) VALUES
(1, 'site_db_user', 'hz_zen_cart_site', 'site', 0, 1),
(2, 'site_db_user', 'r1yorHaKnM23+z', 'site', 0, 1);

-- --------------------------------------------------------

--
-- 表的结构 `hz_sites`
--

CREATE TABLE `hz_sites` (
  `id` int(11) NOT NULL,
  `domain` varchar(128) NOT NULL COMMENT '域名',
  `lang` varchar(32) NOT NULL COMMENT '语言',
  `server_ip` char(15) DEFAULT NULL COMMENT 'IP',
  `server_id` int(11) NOT NULL DEFAULT '0' COMMENT '服务器来源',
  `tpl_id` int(11) NOT NULL DEFAULT '0' COMMENT '模板',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态',
  `admin_id` int(11) DEFAULT '0' COMMENT '操作人',
  `create_at` datetime DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime DEFAULT NULL COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='站点系统';

--
-- 转存表中的数据 `hz_sites`
--

INSERT INTO `hz_sites` (`id`, `domain`, `lang`, `server_ip`, `server_id`, `tpl_id`, `status`, `admin_id`, `create_at`, `update_at`) VALUES
(98, 'VP0QvM.homeuom.com', 'german', '46.4.85.58', 9, 2, 1, NULL, '2020-04-11 04:01:54', '2020-04-11 04:01:54'),
(99, 'vInNqn.homeuom.com', 'german', '46.4.85.58', 9, 2, 1, NULL, '2020-04-11 04:02:01', '2020-04-11 04:02:01'),
(100, '46tQuy.homeuom.com', 'german', '46.4.85.58', 9, 2, 1, NULL, '2020-04-11 04:02:08', '2020-04-11 04:02:08'),
(101, 'mxrIQx.homeuom.com', 'german', '46.4.85.58', 9, 2, 1, NULL, '2020-04-11 04:02:16', '2020-04-11 04:02:16'),
(102, '55cpA3.homeuom.com', 'german', '46.4.85.58', 9, 2, 1, NULL, '2020-04-11 04:02:23', '2020-04-11 04:02:23'),
(103, 'RhmDfr.homeuom.com', 'german', '46.4.85.58', 9, 2, 1, NULL, '2020-04-11 04:02:30', '2020-04-11 04:02:30'),
(104, 'o3tOLz.homeuom.com', 'german', '46.4.85.58', 9, 2, 1, NULL, '2020-04-11 04:02:37', '2020-04-11 04:02:37'),
(105, '1Nn9UG.homeuom.com', 'german', '46.4.85.58', 9, 2, 1, NULL, '2020-04-11 04:02:44', '2020-04-11 04:02:44'),
(106, '1Z6coF.homeuom.com', 'german', '46.4.85.58', 9, 2, 1, NULL, '2020-04-11 04:02:52', '2020-04-11 04:02:52'),
(107, 'eya3Az.homeuom.com', 'german', '46.4.85.58', 9, 2, 1, NULL, '2020-04-11 04:02:59', '2020-04-11 04:02:59'),
(108, 'C6y8Ur.homeuom.com', 'german', '46.4.85.58', 9, 2, 1, NULL, '2020-04-11 04:03:06', '2020-04-11 04:03:06'),
(109, 'XznBxj.homeuom.com', 'german', '46.4.85.58', 9, 2, 1, NULL, '2020-04-11 04:03:14', '2020-04-11 04:03:14'),
(110, 'weGSUX.homeuom.com', 'german', '46.4.85.58', 9, 2, 1, NULL, '2020-04-11 04:03:22', '2020-04-11 04:03:22'),
(111, 'QMabTk.homeuom.com', 'german', '46.4.85.58', 9, 2, 1, NULL, '2020-04-11 04:03:30', '2020-04-11 04:03:30'),
(112, 'lnTSbV.homeuom.com', 'german', '46.4.85.58', 9, 2, 1, NULL, '2020-04-11 04:03:42', '2020-04-11 04:03:42'),
(113, 'homeuom.com', 'english', '46.4.85.58', 9, 1, 1, 1, '2020-04-11 05:10:04', '2020-04-11 05:10:04');

-- --------------------------------------------------------

--
-- 表的结构 `hz_site_config`
--

CREATE TABLE `hz_site_config` (
  `id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL COMMENT '站点',
  `fs_catalog` varchar(128) DEFAULT NULL COMMENT '站点目录',
  `admin_dir` varchar(64) DEFAULT NULL COMMENT '后台目录',
  `db_file` varchar(64) DEFAULT NULL COMMENT '数据库文件',
  `db_name` varchar(32) DEFAULT NULL COMMENT '数据库名称',
  `db_user` varchar(32) DEFAULT NULL COMMENT '数据库用户',
  `db_pass` varchar(64) DEFAULT NULL COMMENT '数据库密码',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态',
  `create_at` datetime DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime DEFAULT NULL COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='站点配置信息';

--
-- 转存表中的数据 `hz_site_config`
--

INSERT INTO `hz_site_config` (`id`, `site_id`, `fs_catalog`, `admin_dir`, `db_file`, `db_name`, `db_user`, `db_pass`, `status`, `create_at`, `update_at`) VALUES
(98, 98, '/home/admin/web/vp0qvm.homeuom.com/public_html', 'xadmin', 'zencart.sql', 'admin_vp0qvm.homeuom.com', 'admin_myZenCart', 'tgJoyl5C9UgvZ', 1, '2020-04-11 04:01:54', '2020-04-11 04:01:54'),
(99, 99, '/home/admin/web/vinnqn.homeuom.com/public_html', 'xadmin', 'zencart.sql', 'admin_vinnqn.homeuom.com', 'admin_myZenCart', 'tgJoyl5C9UgvZ', 1, '2020-04-11 04:02:01', '2020-04-11 04:02:01'),
(100, 100, '/home/admin/web/46tquy.homeuom.com/public_html', 'xadmin', 'zencart.sql', 'admin_46tquy.homeuom.com', 'admin_myZenCart', 'tgJoyl5C9UgvZ', 1, '2020-04-11 04:02:08', '2020-04-11 04:02:08'),
(101, 101, '/home/admin/web/mxriqx.homeuom.com/public_html', 'xadmin', 'zencart.sql', 'admin_mxriqx.homeuom.com', 'admin_myZenCart', 'tgJoyl5C9UgvZ', 1, '2020-04-11 04:02:16', '2020-04-11 04:02:16'),
(102, 102, '/home/admin/web/55cpa3.homeuom.com/public_html', 'xadmin', 'zencart.sql', 'admin_55cpa3.homeuom.com', 'admin_myZenCart', 'tgJoyl5C9UgvZ', 1, '2020-04-11 04:02:23', '2020-04-11 04:02:23'),
(103, 103, '/home/admin/web/rhmdfr.homeuom.com/public_html', 'xadmin', 'zencart.sql', 'admin_rhmdfr.homeuom.com', 'admin_myZenCart', 'tgJoyl5C9UgvZ', 1, '2020-04-11 04:02:30', '2020-04-11 04:02:30'),
(104, 104, '/home/admin/web/o3tolz.homeuom.com/public_html', 'xadmin', 'zencart.sql', 'admin_o3tolz.homeuom.com', 'admin_myZenCart', 'tgJoyl5C9UgvZ', 1, '2020-04-11 04:02:37', '2020-04-11 04:02:37'),
(105, 105, '/home/admin/web/1nn9ug.homeuom.com/public_html', 'xadmin', 'zencart.sql', 'admin_1nn9ug.homeuom.com', 'admin_myZenCart', 'tgJoyl5C9UgvZ', 1, '2020-04-11 04:02:44', '2020-04-11 04:02:44'),
(106, 106, '/home/admin/web/1z6cof.homeuom.com/public_html', 'xadmin', 'zencart.sql', 'admin_1z6cof.homeuom.com', 'admin_myZenCart', 'tgJoyl5C9UgvZ', 1, '2020-04-11 04:02:52', '2020-04-11 04:02:52'),
(107, 107, '/home/admin/web/eya3az.homeuom.com/public_html', 'xadmin', 'zencart.sql', 'admin_eya3az.homeuom.com', 'admin_myZenCart', 'tgJoyl5C9UgvZ', 1, '2020-04-11 04:02:59', '2020-04-11 04:02:59'),
(108, 108, '/home/admin/web/c6y8ur.homeuom.com/public_html', 'xadmin', 'zencart.sql', 'admin_c6y8ur.homeuom.com', 'admin_myZenCart', 'tgJoyl5C9UgvZ', 1, '2020-04-11 04:03:06', '2020-04-11 04:03:06'),
(109, 109, '/home/admin/web/xznbxj.homeuom.com/public_html', 'xadmin', 'zencart.sql', 'admin_xznbxj.homeuom.com', 'admin_myZenCart', 'tgJoyl5C9UgvZ', 1, '2020-04-11 04:03:14', '2020-04-11 04:03:14'),
(110, 110, '/home/admin/web/wegsux.homeuom.com/public_html', 'xadmin', 'zencart.sql', 'admin_wegsux.homeuom.com', 'admin_myZenCart', 'tgJoyl5C9UgvZ', 1, '2020-04-11 04:03:22', '2020-04-11 04:03:22'),
(111, 111, '/home/admin/web/qmabtk.homeuom.com/public_html', 'xadmin', 'zencart.sql', 'admin_qmabtk.homeuom.com', 'admin_myZenCart', 'tgJoyl5C9UgvZ', 1, '2020-04-11 04:03:30', '2020-04-11 04:03:30'),
(112, 112, '/home/admin/web/lntsbv.homeuom.com/public_html', 'xadmin', 'zencart.sql', 'admin_lntsbv.homeuom.com', 'admin_myZenCart', 'tgJoyl5C9UgvZ', 1, '2020-04-11 04:03:42', '2020-04-11 04:03:42'),
(113, 113, '/home/admin/web/homeuom.com/public_html', 'xadmin', 'zencart.sql', 'admin_homeuom.com', 'admin_myZenCart', 'tgJoyl5C9UgvZ', 1, '2020-04-11 05:10:04', '2020-04-11 05:10:04');

-- --------------------------------------------------------

--
-- 表的结构 `hz_site_dns_records`
--

CREATE TABLE `hz_site_dns_records` (
  `id` int(11) NOT NULL,
  `site_id` int(11) DEFAULT NULL,
  `parse_mode` varchar(32) NOT NULL COMMENT '解析模式',
  `record` varchar(128) DEFAULT NULL COMMENT '记录ID',
  `type` char(8) NOT NULL COMMENT '类型',
  `name` varchar(128) NOT NULL COMMENT '名称',
  `content` char(15) NOT NULL COMMENT 'IP地址',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态',
  `create_at` datetime DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime DEFAULT NULL COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='站点DNS解析记录';

--
-- 转存表中的数据 `hz_site_dns_records`
--

INSERT INTO `hz_site_dns_records` (`id`, `site_id`, `parse_mode`, `record`, `type`, `name`, `content`, `status`, `create_at`, `update_at`) VALUES
(64, 98, 'CloudFlare', '994a599ded6f58554b2ab518c946c888', 'A', 'vp0qvm.homeuom.com', '46.4.85.58', 1, '2020-04-11 04:01:54', '2020-04-11 04:01:54'),
(65, 99, 'CloudFlare', '26ee1bc85c4d21d2ab92677e9f5f4239', 'A', 'vinnqn.homeuom.com', '46.4.85.58', 1, '2020-04-11 04:02:01', '2020-04-11 04:02:01'),
(66, 100, 'CloudFlare', '3520c97c40e242334ffc42a52823be39', 'A', '46tquy.homeuom.com', '46.4.85.58', 1, '2020-04-11 04:02:08', '2020-04-11 04:02:08'),
(67, 101, 'CloudFlare', 'f1e9b719efddde94ac539533dda2575e', 'A', 'mxriqx.homeuom.com', '46.4.85.58', 1, '2020-04-11 04:02:16', '2020-04-11 04:02:16'),
(68, 102, 'CloudFlare', '0b37fd5c5eeee106b09a1680c9902576', 'A', '55cpa3.homeuom.com', '46.4.85.58', 1, '2020-04-11 04:02:23', '2020-04-11 04:02:23'),
(69, 103, 'CloudFlare', '0267573f99052133d862b59d14b7b201', 'A', 'rhmdfr.homeuom.com', '46.4.85.58', 1, '2020-04-11 04:02:30', '2020-04-11 04:02:30'),
(70, 104, 'CloudFlare', '75fde2fea1ca020e5068a2509347fa93', 'A', 'o3tolz.homeuom.com', '46.4.85.58', 1, '2020-04-11 04:02:37', '2020-04-11 04:02:37'),
(71, 105, 'CloudFlare', '59fe4f580b6904fda23c981bfe7cac9a', 'A', '1nn9ug.homeuom.com', '46.4.85.58', 1, '2020-04-11 04:02:44', '2020-04-11 04:02:44'),
(72, 106, 'CloudFlare', '4b017e4837568b237dc9de4edfea2073', 'A', '1z6cof.homeuom.com', '46.4.85.58', 1, '2020-04-11 04:02:52', '2020-04-11 04:02:52'),
(73, 107, 'CloudFlare', '2441b0b46c7491f72524787020e4191d', 'A', 'eya3az.homeuom.com', '46.4.85.58', 1, '2020-04-11 04:02:59', '2020-04-11 04:02:59'),
(74, 108, 'CloudFlare', '897b054381967f8f031c4883b7badaec', 'A', 'c6y8ur.homeuom.com', '46.4.85.58', 1, '2020-04-11 04:03:06', '2020-04-11 04:03:06'),
(75, 109, 'CloudFlare', '297ffc92f6fb2ba39c87e63149fb20e9', 'A', 'xznbxj.homeuom.com', '46.4.85.58', 1, '2020-04-11 04:03:14', '2020-04-11 04:03:14'),
(76, 110, 'CloudFlare', '7c82dc5c745af002c4999fc33bef7d14', 'A', 'wegsux.homeuom.com', '46.4.85.58', 1, '2020-04-11 04:03:22', '2020-04-11 04:03:22'),
(77, 111, 'CloudFlare', '91e4e87ace6b4c795fb5a19f36b29145', 'A', 'qmabtk.homeuom.com', '46.4.85.58', 1, '2020-04-11 04:03:30', '2020-04-11 04:03:30'),
(78, 112, 'CloudFlare', 'a5f427119999db104b84b13b67c5d646', 'A', 'lntsbv.homeuom.com', '46.4.85.58', 1, '2020-04-11 04:03:42', '2020-04-11 04:03:42'),
(79, 113, 'CloudFlare', 'c42088c25d230f6a7f080da33e46ccd6', 'A', 'homeuom.com', '46.4.85.58', 1, '2020-04-11 05:10:04', '2020-04-11 05:10:04'),
(80, 113, 'CloudFlare', 'e52461506ac7a4f7beb109ef741294f8', 'A', 'www.homeuom.com', '46.4.85.58', 1, '2020-04-11 05:10:04', '2020-04-11 05:10:04');

-- --------------------------------------------------------

--
-- 表的结构 `hz_site_languages`
--

CREATE TABLE `hz_site_languages` (
  `id` int(11) NOT NULL,
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '标题',
  `code` char(2) NOT NULL DEFAULT '' COMMENT 'Code',
  `image` varchar(64) DEFAULT NULL COMMENT '图标',
  `dir_name` varchar(32) DEFAULT NULL COMMENT '目录',
  `sort` smallint(6) DEFAULT NULL COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态',
  `admin_id` int(11) NOT NULL COMMENT '操作人',
  `create_at` datetime DEFAULT NULL COMMENT '添加时间',
  `update_at` datetime DEFAULT NULL COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `hz_site_languages`
--

INSERT INTO `hz_site_languages` (`id`, `title`, `code`, `image`, `dir_name`, `sort`, `status`, `admin_id`, `create_at`, `update_at`) VALUES
(1, 'English-英语', 'en', 'lang/en.gif', 'english', 1, 1, 1, '2020-04-04 04:04:04', '2020-04-04 04:04:04'),
(2, 'German-德语', 'de', 'lang/de.gif', 'german', 2, 1, 1, '2020-04-04 04:04:04', '2020-04-04 04:04:04'),
(3, 'Spanish-西班牙语', 'es', 'lang/es.gif', 'spanish', 3, 1, 1, '2020-04-04 04:04:04', '2020-04-04 04:04:04'),
(4, 'French-法语', 'fr', 'lang/fr.gif', 'french', 4, 1, 1, '2020-04-04 04:04:04', '2020-04-04 04:04:04'),
(5, 'Italian-意大利语', 'it', 'lang/it.gif', 'italian', 5, 1, 1, '2020-04-04 04:04:04', '2020-04-04 04:04:04'),
(6, 'Swedish-瑞典语', 'sv', 'lang/sv.gif', 'swedish', 6, 1, 1, '2020-04-04 04:04:04', '2020-04-04 04:04:04'),
(7, 'Dutch-荷兰语', 'nl', 'lang/nl.gif', 'dutch', 7, 1, 1, '2020-04-04 04:04:04', '2020-04-04 04:04:04'),
(8, 'Norwegian-挪威语', 'no', 'lang/no.gif', 'norwegian', 8, 1, 1, '2020-04-04 04:04:04', '2020-04-04 04:04:04'),
(9, 'Danish-丹麦语', 'da', 'lang/da.gif', 'danish', 9, 1, 1, '2020-04-04 04:04:04', '2020-04-04 04:04:04'),
(10, 'Portuguese-葡萄牙语', 'pt', 'lang/pt.gif', 'portugues', 10, 1, 1, '2020-04-04 04:04:04', '2020-04-04 04:04:04'),
(11, 'Czech-捷克语', 'cs', 'lang/cs.gif', 'czech', 11, 1, 1, '2020-04-04 04:04:04', '2020-04-04 04:04:04'),
(12, 'Finnish-芬兰语', 'fi', 'lang/fi.gif', 'finnish', 12, 1, 1, '2020-04-04 04:04:04', '2020-04-04 04:04:04'),
(13, 'Hungarian-匈牙利语', 'hu', 'lang/hu.gif', 'hungarian', 13, 1, 1, '2020-04-04 04:04:04', '2020-04-04 04:04:04'),
(14, 'Polish-波兰语', 'pl', 'lang/pl.gif', 'polish', 14, 1, 1, '2020-04-04 04:04:04', '2020-04-04 04:04:04');

-- --------------------------------------------------------

--
-- 表的结构 `hz_site_templates`
--

CREATE TABLE `hz_site_templates` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL COMMENT '标题',
  `author` varchar(32) DEFAULT NULL COMMENT '作者',
  `preview` varchar(128) DEFAULT NULL COMMENT '预览图',
  `path` varchar(128) DEFAULT NULL COMMENT '本地路径',
  `admin_dir` varchar(64) DEFAULT NULL COMMENT '后台目录',
  `db_file` varchar(64) DEFAULT NULL COMMENT '数据库文件',
  `sites` int(11) NOT NULL DEFAULT '0' COMMENT '站点数',
  `remark` mediumtext COMMENT '备注',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '操作人',
  `create_at` datetime DEFAULT NULL COMMENT '创建时间',
  `update_at` datetime DEFAULT NULL COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='站点模板';

--
-- 转存表中的数据 `hz_site_templates`
--

INSERT INTO `hz_site_templates` (`id`, `name`, `author`, `preview`, `path`, `admin_dir`, `db_file`, `sites`, `remark`, `status`, `admin_id`, `create_at`, `update_at`) VALUES
(1, 'EnglishLanguage', 'sadsa', '/200410/084442-UiGhGSiYY.jpg', 'app/public/sites/200410/0844422EvY_EnglishLanguage', 'xadmin', 'zencart.sql', 5, NULL, 1, 1, '2020-03-24 03:35:44', '2020-04-11 05:10:04'),
(2, '14Languages', 'sadsa', '/200410/084116-ulFAGsWQH.jpg', 'app/public/sites/200410/084116wPyM_14Languages', 'xadmin', 'zencart.sql', 26, NULL, 1, 1, '2020-03-24 04:04:43', '2020-04-15 04:11:45'),
(3, 'asuyua98787879', 'wewq', 'images/17.jpg', './sites/zen-cart-2020', 'xadmin', 'zencart.sql', 0, NULL, 0, 1, '2020-03-28 06:38:09', '2020-04-08 15:33:11');

-- --------------------------------------------------------

--
-- 表的结构 `hz_site_tpl_lang`
--

CREATE TABLE `hz_site_tpl_lang` (
  `id` int(11) NOT NULL,
  `tpl_id` int(11) NOT NULL COMMENT '模板',
  `lang_id` int(11) NOT NULL COMMENT '语言'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='站点模板语言关联表';

--
-- 转存表中的数据 `hz_site_tpl_lang`
--

INSERT INTO `hz_site_tpl_lang` (`id`, `tpl_id`, `lang_id`) VALUES
(57, 3, 1),
(58, 3, 2),
(59, 3, 3),
(60, 3, 4),
(61, 3, 5),
(62, 3, 6),
(63, 2, 1),
(64, 2, 2),
(65, 2, 3),
(66, 2, 4),
(67, 2, 5),
(68, 2, 6),
(69, 2, 7),
(70, 2, 8),
(71, 2, 9),
(72, 2, 10),
(73, 2, 11),
(74, 2, 12),
(75, 2, 13),
(76, 2, 14),
(77, 1, 1);

-- --------------------------------------------------------

--
-- 表的结构 `hz_workers`
--

CREATE TABLE `hz_workers` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `no` char(8) NOT NULL COMMENT '工号',
  `gender` tinyint(4) NOT NULL DEFAULT '1' COMMENT '性别',
  `phone` varchar(32) DEFAULT NULL COMMENT '电话',
  `birthday` date DEFAULT NULL COMMENT '生日',
  `join_day` date NOT NULL COMMENT '入职日期',
  `left_day` date DEFAULT NULL COMMENT '离职日期',
  `login_token` varchar(128) DEFAULT NULL COMMENT 'sso-token'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='员工';

--
-- 转存表中的数据 `hz_workers`
--

INSERT INTO `hz_workers` (`id`, `admin_id`, `no`, `gender`, `phone`, `birthday`, `join_day`, `left_day`, `login_token`) VALUES
(1, 8, '000000', 0, '44454456', '2020-04-16', '2020-04-16', NULL, 'cdc5a0aa8758e95258c5218aeed5a79c');

-- --------------------------------------------------------

--
-- 表的结构 `hz_zencart_countries`
--

CREATE TABLE `hz_zencart_countries` (
  `countries_id` int(11) NOT NULL,
  `countries_name` varchar(64) NOT NULL DEFAULT '',
  `countries_iso_code_2` char(2) NOT NULL DEFAULT '',
  `countries_iso_code_3` char(3) NOT NULL DEFAULT '',
  `address_format_id` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `hz_zencart_countries`
--

INSERT INTO `hz_zencart_countries` (`countries_id`, `countries_name`, `countries_iso_code_2`, `countries_iso_code_3`, `address_format_id`, `status`) VALUES
(240, 'Åland Islands', 'AX', 'ALA', 1, 1),
(1, 'Afghanistan', 'AF', 'AFG', 1, 1),
(2, 'Albania', 'AL', 'ALB', 1, 1),
(3, 'Algeria', 'DZ', 'DZA', 1, 1),
(4, 'American Samoa', 'AS', 'ASM', 1, 1),
(5, 'Andorra', 'AD', 'AND', 1, 1),
(6, 'Angola', 'AO', 'AGO', 1, 1),
(7, 'Anguilla', 'AI', 'AIA', 1, 1),
(8, 'Antarctica', 'AQ', 'ATA', 1, 1),
(9, 'Antigua and Barbuda', 'AG', 'ATG', 1, 1),
(10, 'Argentina', 'AR', 'ARG', 1, 1),
(11, 'Armenia', 'AM', 'ARM', 1, 1),
(12, 'Aruba', 'AW', 'ABW', 1, 1),
(13, 'Australia', 'AU', 'AUS', 7, 1),
(14, 'Austria', 'AT', 'AUT', 5, 1),
(15, 'Azerbaijan', 'AZ', 'AZE', 1, 1),
(16, 'Bahamas', 'BS', 'BHS', 1, 1),
(17, 'Bahrain', 'BH', 'BHR', 1, 1),
(18, 'Bangladesh', 'BD', 'BGD', 1, 1),
(19, 'Barbados', 'BB', 'BRB', 1, 1),
(20, 'Belarus', 'BY', 'BLR', 1, 1),
(21, 'Belgium', 'BE', 'BEL', 5, 1),
(22, 'Belize', 'BZ', 'BLZ', 1, 1),
(23, 'Benin', 'BJ', 'BEN', 1, 1),
(24, 'Bermuda', 'BM', 'BMU', 1, 1),
(25, 'Bhutan', 'BT', 'BTN', 1, 1),
(26, 'Bolivia', 'BO', 'BOL', 1, 1),
(27, 'Bosnia and Herzegowina', 'BA', 'BIH', 1, 1),
(28, 'Botswana', 'BW', 'BWA', 1, 1),
(29, 'Bouvet Island', 'BV', 'BVT', 1, 1),
(30, 'Brazil', 'BR', 'BRA', 1, 1),
(31, 'British Indian Ocean Territory', 'IO', 'IOT', 1, 1),
(32, 'Brunei Darussalam', 'BN', 'BRN', 1, 1),
(33, 'Bulgaria', 'BG', 'BGR', 1, 1),
(34, 'Burkina Faso', 'BF', 'BFA', 1, 1),
(35, 'Burundi', 'BI', 'BDI', 1, 1),
(36, 'Cambodia', 'KH', 'KHM', 1, 1),
(37, 'Cameroon', 'CM', 'CMR', 1, 1),
(38, 'Canada', 'CA', 'CAN', 2, 1),
(39, 'Cape Verde', 'CV', 'CPV', 1, 1),
(40, 'Cayman Islands', 'KY', 'CYM', 1, 1),
(41, 'Central African Republic', 'CF', 'CAF', 1, 1),
(42, 'Chad', 'TD', 'TCD', 1, 1),
(43, 'Chile', 'CL', 'CHL', 1, 1),
(44, 'China', 'CN', 'CHN', 1, 1),
(45, 'Christmas Island', 'CX', 'CXR', 1, 1),
(46, 'Cocos (Keeling) Islands', 'CC', 'CCK', 1, 1),
(47, 'Colombia', 'CO', 'COL', 1, 1),
(48, 'Comoros', 'KM', 'COM', 1, 1),
(49, 'Congo', 'CG', 'COG', 1, 1),
(50, 'Cook Islands', 'CK', 'COK', 1, 1),
(51, 'Costa Rica', 'CR', 'CRI', 1, 1),
(52, 'Côte d\'Ivoire', 'CI', 'CIV', 1, 1),
(53, 'Croatia', 'HR', 'HRV', 1, 1),
(54, 'Cuba', 'CU', 'CUB', 1, 1),
(55, 'Cyprus', 'CY', 'CYP', 1, 1),
(56, 'Czech Republic', 'CZ', 'CZE', 1, 1),
(57, 'Denmark', 'DK', 'DNK', 1, 1),
(58, 'Djibouti', 'DJ', 'DJI', 1, 1),
(59, 'Dominica', 'DM', 'DMA', 1, 1),
(60, 'Dominican Republic', 'DO', 'DOM', 1, 1),
(61, 'Timor-Leste', 'TL', 'TLS', 1, 1),
(62, 'Ecuador', 'EC', 'ECU', 1, 1),
(63, 'Egypt', 'EG', 'EGY', 1, 1),
(64, 'El Salvador', 'SV', 'SLV', 1, 1),
(65, 'Equatorial Guinea', 'GQ', 'GNQ', 1, 1),
(66, 'Eritrea', 'ER', 'ERI', 1, 1),
(67, 'Estonia', 'EE', 'EST', 1, 1),
(68, 'Ethiopia', 'ET', 'ETH', 1, 1),
(69, 'Falkland Islands (Malvinas)', 'FK', 'FLK', 1, 1),
(70, 'Faroe Islands', 'FO', 'FRO', 1, 1),
(71, 'Fiji', 'FJ', 'FJI', 1, 1),
(72, 'Finland', 'FI', 'FIN', 1, 1),
(73, 'France', 'FR', 'FRA', 1, 1),
(75, 'French Guiana', 'GF', 'GUF', 1, 1),
(76, 'French Polynesia', 'PF', 'PYF', 1, 1),
(77, 'French Southern Territories', 'TF', 'ATF', 1, 1),
(78, 'Gabon', 'GA', 'GAB', 1, 1),
(79, 'Gambia', 'GM', 'GMB', 1, 1),
(80, 'Georgia', 'GE', 'GEO', 1, 1),
(81, 'Germany', 'DE', 'DEU', 5, 1),
(82, 'Ghana', 'GH', 'GHA', 1, 1),
(83, 'Gibraltar', 'GI', 'GIB', 1, 1),
(84, 'Greece', 'GR', 'GRC', 1, 1),
(85, 'Greenland', 'GL', 'GRL', 1, 1),
(86, 'Grenada', 'GD', 'GRD', 1, 1),
(87, 'Guadeloupe', 'GP', 'GLP', 1, 1),
(88, 'Guam', 'GU', 'GUM', 1, 1),
(89, 'Guatemala', 'GT', 'GTM', 1, 1),
(90, 'Guinea', 'GN', 'GIN', 1, 1),
(91, 'Guinea-bissau', 'GW', 'GNB', 1, 1),
(92, 'Guyana', 'GY', 'GUY', 1, 1),
(93, 'Haiti', 'HT', 'HTI', 1, 1),
(94, 'Heard and Mc Donald Islands', 'HM', 'HMD', 1, 1),
(95, 'Honduras', 'HN', 'HND', 1, 1),
(96, 'Hong Kong', 'HK', 'HKG', 1, 1),
(97, 'Hungary', 'HU', 'HUN', 1, 1),
(98, 'Iceland', 'IS', 'ISL', 1, 1),
(99, 'India', 'IN', 'IND', 1, 1),
(100, 'Indonesia', 'ID', 'IDN', 1, 1),
(101, 'Iran (Islamic Republic of)', 'IR', 'IRN', 1, 1),
(102, 'Iraq', 'IQ', 'IRQ', 1, 1),
(103, 'Ireland', 'IE', 'IRL', 1, 1),
(104, 'Israel', 'IL', 'ISR', 1, 1),
(105, 'Italy', 'IT', 'ITA', 5, 1),
(106, 'Jamaica', 'JM', 'JAM', 1, 1),
(107, 'Japan', 'JP', 'JPN', 1, 1),
(108, 'Jordan', 'JO', 'JOR', 1, 1),
(109, 'Kazakhstan', 'KZ', 'KAZ', 1, 1),
(110, 'Kenya', 'KE', 'KEN', 1, 1),
(111, 'Kiribati', 'KI', 'KIR', 1, 1),
(112, 'Korea, Democratic People\'s Republic of', 'KP', 'PRK', 1, 1),
(113, 'Korea, Republic of', 'KR', 'KOR', 1, 1),
(114, 'Kuwait', 'KW', 'KWT', 1, 1),
(115, 'Kyrgyzstan', 'KG', 'KGZ', 1, 1),
(116, 'Lao People\'s Democratic Republic', 'LA', 'LAO', 1, 1),
(117, 'Latvia', 'LV', 'LVA', 1, 1),
(118, 'Lebanon', 'LB', 'LBN', 1, 1),
(119, 'Lesotho', 'LS', 'LSO', 1, 1),
(120, 'Liberia', 'LR', 'LBR', 1, 1),
(121, 'Libya', 'LY', 'LBY', 1, 1),
(122, 'Liechtenstein', 'LI', 'LIE', 1, 1),
(123, 'Lithuania', 'LT', 'LTU', 1, 1),
(124, 'Luxembourg', 'LU', 'LUX', 1, 1),
(125, 'Macao', 'MO', 'MAC', 1, 1),
(126, 'Macedonia, The Former Yugoslav Republic of', 'MK', 'MKD', 1, 1),
(127, 'Madagascar', 'MG', 'MDG', 1, 1),
(128, 'Malawi', 'MW', 'MWI', 1, 1),
(129, 'Malaysia', 'MY', 'MYS', 1, 1),
(130, 'Maldives', 'MV', 'MDV', 1, 1),
(131, 'Mali', 'ML', 'MLI', 1, 1),
(132, 'Malta', 'MT', 'MLT', 1, 1),
(133, 'Marshall Islands', 'MH', 'MHL', 1, 1),
(134, 'Martinique', 'MQ', 'MTQ', 1, 1),
(135, 'Mauritania', 'MR', 'MRT', 1, 1),
(136, 'Mauritius', 'MU', 'MUS', 1, 1),
(137, 'Mayotte', 'YT', 'MYT', 1, 1),
(138, 'Mexico', 'MX', 'MEX', 1, 1),
(139, 'Micronesia, Federated States of', 'FM', 'FSM', 1, 1),
(140, 'Moldova', 'MD', 'MDA', 1, 1),
(141, 'Monaco', 'MC', 'MCO', 1, 1),
(142, 'Mongolia', 'MN', 'MNG', 1, 1),
(143, 'Montserrat', 'MS', 'MSR', 1, 1),
(144, 'Morocco', 'MA', 'MAR', 1, 1),
(145, 'Mozambique', 'MZ', 'MOZ', 1, 1),
(146, 'Myanmar', 'MM', 'MMR', 1, 1),
(147, 'Namibia', 'NA', 'NAM', 1, 1),
(148, 'Nauru', 'NR', 'NRU', 1, 1),
(149, 'Nepal', 'NP', 'NPL', 1, 1),
(150, 'Netherlands', 'NL', 'NLD', 5, 1),
(151, 'Bonaire, Sint Eustatius and Saba', 'BQ', 'BES', 1, 1),
(152, 'New Caledonia', 'NC', 'NCL', 1, 1),
(153, 'New Zealand', 'NZ', 'NZL', 1, 1),
(154, 'Nicaragua', 'NI', 'NIC', 1, 1),
(155, 'Niger', 'NE', 'NER', 1, 1),
(156, 'Nigeria', 'NG', 'NGA', 1, 1),
(157, 'Niue', 'NU', 'NIU', 1, 1),
(158, 'Norfolk Island', 'NF', 'NFK', 1, 1),
(159, 'Northern Mariana Islands', 'MP', 'MNP', 1, 1),
(160, 'Norway', 'NO', 'NOR', 1, 1),
(161, 'Oman', 'OM', 'OMN', 1, 1),
(162, 'Pakistan', 'PK', 'PAK', 1, 1),
(163, 'Palau', 'PW', 'PLW', 1, 1),
(164, 'Panama', 'PA', 'PAN', 1, 1),
(165, 'Papua New Guinea', 'PG', 'PNG', 1, 1),
(166, 'Paraguay', 'PY', 'PRY', 1, 1),
(167, 'Peru', 'PE', 'PER', 1, 1),
(168, 'Philippines', 'PH', 'PHL', 1, 1),
(169, 'Pitcairn', 'PN', 'PCN', 1, 1),
(170, 'Poland', 'PL', 'POL', 1, 1),
(171, 'Portugal', 'PT', 'PRT', 1, 1),
(172, 'Puerto Rico', 'PR', 'PRI', 1, 1),
(173, 'Qatar', 'QA', 'QAT', 1, 1),
(174, 'Réunion', 'RE', 'REU', 1, 1),
(175, 'Romania', 'RO', 'ROU', 1, 1),
(176, 'Russian Federation', 'RU', 'RUS', 1, 1),
(177, 'Rwanda', 'RW', 'RWA', 1, 1),
(178, 'Saint Kitts and Nevis', 'KN', 'KNA', 1, 1),
(179, 'Saint Lucia', 'LC', 'LCA', 1, 1),
(180, 'Saint Vincent and the Grenadines', 'VC', 'VCT', 1, 1),
(181, 'Samoa', 'WS', 'WSM', 1, 1),
(182, 'San Marino', 'SM', 'SMR', 1, 1),
(183, 'Sao Tome and Principe', 'ST', 'STP', 1, 1),
(184, 'Saudi Arabia', 'SA', 'SAU', 1, 1),
(185, 'Senegal', 'SN', 'SEN', 1, 1),
(186, 'Seychelles', 'SC', 'SYC', 1, 1),
(187, 'Sierra Leone', 'SL', 'SLE', 1, 1),
(188, 'Singapore', 'SG', 'SGP', 4, 1),
(189, 'Slovakia (Slovak Republic)', 'SK', 'SVK', 1, 1),
(190, 'Slovenia', 'SI', 'SVN', 1, 1),
(191, 'Solomon Islands', 'SB', 'SLB', 1, 1),
(192, 'Somalia', 'SO', 'SOM', 1, 1),
(193, 'South Africa', 'ZA', 'ZAF', 1, 1),
(194, 'South Georgia and the South Sandwich Islands', 'GS', 'SGS', 1, 1),
(195, 'Spain', 'ES', 'ESP', 3, 1),
(196, 'Sri Lanka', 'LK', 'LKA', 1, 1),
(197, 'St. Helena', 'SH', 'SHN', 1, 1),
(198, 'St. Pierre and Miquelon', 'PM', 'SPM', 1, 1),
(199, 'Sudan', 'SD', 'SDN', 1, 1),
(200, 'Suriname', 'SR', 'SUR', 1, 1),
(201, 'Svalbard and Jan Mayen Islands', 'SJ', 'SJM', 1, 1),
(202, 'Swaziland', 'SZ', 'SWZ', 1, 1),
(203, 'Sweden', 'SE', 'SWE', 5, 1),
(204, 'Switzerland', 'CH', 'CHE', 1, 1),
(205, 'Syrian Arab Republic', 'SY', 'SYR', 1, 1),
(206, 'Taiwan', 'TW', 'TWN', 1, 1),
(207, 'Tajikistan', 'TJ', 'TJK', 1, 1),
(208, 'Tanzania, United Republic of', 'TZ', 'TZA', 1, 1),
(209, 'Thailand', 'TH', 'THA', 1, 1),
(210, 'Togo', 'TG', 'TGO', 1, 1),
(211, 'Tokelau', 'TK', 'TKL', 1, 1),
(212, 'Tonga', 'TO', 'TON', 1, 1),
(213, 'Trinidad and Tobago', 'TT', 'TTO', 1, 1),
(214, 'Tunisia', 'TN', 'TUN', 1, 1),
(215, 'Turkey', 'TR', 'TUR', 1, 1),
(216, 'Turkmenistan', 'TM', 'TKM', 1, 1),
(217, 'Turks and Caicos Islands', 'TC', 'TCA', 1, 1),
(218, 'Tuvalu', 'TV', 'TUV', 1, 1),
(219, 'Uganda', 'UG', 'UGA', 1, 1),
(220, 'Ukraine', 'UA', 'UKR', 1, 1),
(221, 'United Arab Emirates', 'AE', 'ARE', 1, 1),
(222, 'United Kingdom', 'GB', 'GBR', 6, 1),
(223, 'United States', 'US', 'USA', 2, 1),
(224, 'United States Minor Outlying Islands', 'UM', 'UMI', 1, 1),
(225, 'Uruguay', 'UY', 'URY', 1, 1),
(226, 'Uzbekistan', 'UZ', 'UZB', 1, 1),
(227, 'Vanuatu', 'VU', 'VUT', 1, 1),
(228, 'Vatican City State (Holy See)', 'VA', 'VAT', 1, 1),
(229, 'Venezuela', 'VE', 'VEN', 1, 1),
(230, 'Viet Nam', 'VN', 'VNM', 1, 1),
(231, 'Virgin Islands (British)', 'VG', 'VGB', 1, 1),
(232, 'Virgin Islands (U.S.)', 'VI', 'VIR', 1, 1),
(233, 'Wallis and Futuna Islands', 'WF', 'WLF', 1, 1),
(234, 'Western Sahara', 'EH', 'ESH', 1, 1),
(235, 'Yemen', 'YE', 'YEM', 1, 1),
(236, 'Serbia', 'RS', 'SRB', 1, 1),
(238, 'Zambia', 'ZM', 'ZMB', 1, 1),
(239, 'Zimbabwe', 'ZW', 'ZWE', 1, 1),
(241, 'Palestine, State of', 'PS', 'PSE', 1, 1),
(242, 'Montenegro', 'ME', 'MNE', 1, 1),
(243, 'Guernsey', 'GG', 'GGY', 1, 1),
(244, 'Isle of Man', 'IM', 'IMN', 1, 1),
(245, 'Jersey', 'JE', 'JEY', 1, 1),
(246, 'South Sudan', 'SS', 'SSD', 1, 1),
(247, 'Curaçao', 'CW', 'CUW', 1, 1),
(248, 'Sint Maarten (Dutch part)', 'SX', 'SXM', 1, 1);

-- --------------------------------------------------------

--
-- 表的结构 `hz_zencart_currencies`
--

CREATE TABLE `hz_zencart_currencies` (
  `currencies_id` int(11) NOT NULL,
  `title` varchar(32) NOT NULL DEFAULT '',
  `code` char(3) NOT NULL DEFAULT '',
  `symbol_left` varchar(32) DEFAULT NULL,
  `symbol_right` varchar(32) DEFAULT NULL,
  `decimal_point` char(1) DEFAULT NULL,
  `thousands_point` char(1) DEFAULT NULL,
  `decimal_places` char(1) DEFAULT NULL,
  `value` decimal(14,6) DEFAULT NULL,
  `last_updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `hz_zencart_currencies`
--

INSERT INTO `hz_zencart_currencies` (`currencies_id`, `title`, `code`, `symbol_left`, `symbol_right`, `decimal_point`, `thousands_point`, `decimal_places`, `value`, `last_updated`) VALUES
(1, 'US Dollar', 'USD', '$', '', '.', ',', '2', '1.000000', '2020-03-19 16:29:48'),
(2, 'Euro', 'EUR', '&euro;', '', '.', ',', '2', '0.773000', '2020-03-19 16:29:48'),
(3, 'GB Pound', 'GBP', '&pound;', '', '.', ',', '2', '0.672600', '2020-03-19 16:29:48'),
(4, 'Canadian Dollar', 'CAD', '$', '', '.', ',', '2', '1.104200', '2020-03-19 16:29:48'),
(5, 'Australian Dollar', 'AUD', '$', '', '.', ',', '2', '1.178900', '2020-03-19 16:29:48');

-- --------------------------------------------------------

--
-- 表的结构 `hz_zencart_zones`
--

CREATE TABLE `hz_zencart_zones` (
  `zone_id` int(11) NOT NULL,
  `zone_country_id` int(11) NOT NULL DEFAULT '0',
  `zone_code` varchar(32) NOT NULL DEFAULT '',
  `zone_name` varchar(32) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `hz_zencart_zones`
--

INSERT INTO `hz_zencart_zones` (`zone_id`, `zone_country_id`, `zone_code`, `zone_name`) VALUES
(1, 223, 'AL', 'Alabama'),
(2, 223, 'AK', 'Alaska'),
(3, 223, 'AS', 'American Samoa'),
(4, 223, 'AZ', 'Arizona'),
(5, 223, 'AR', 'Arkansas'),
(7, 223, 'AA', 'Armed Forces Americas'),
(9, 223, 'AE', 'Armed Forces Europe'),
(11, 223, 'AP', 'Armed Forces Pacific'),
(12, 223, 'CA', 'California'),
(13, 223, 'CO', 'Colorado'),
(14, 223, 'CT', 'Connecticut'),
(15, 223, 'DE', 'Delaware'),
(16, 223, 'DC', 'District of Columbia'),
(17, 223, 'FM', 'Federated States Of Micronesia'),
(18, 223, 'FL', 'Florida'),
(19, 223, 'GA', 'Georgia'),
(20, 223, 'GU', 'Guam'),
(21, 223, 'HI', 'Hawaii'),
(22, 223, 'ID', 'Idaho'),
(23, 223, 'IL', 'Illinois'),
(24, 223, 'IN', 'Indiana'),
(25, 223, 'IA', 'Iowa'),
(26, 223, 'KS', 'Kansas'),
(27, 223, 'KY', 'Kentucky'),
(28, 223, 'LA', 'Louisiana'),
(29, 223, 'ME', 'Maine'),
(30, 223, 'MH', 'Marshall Islands'),
(31, 223, 'MD', 'Maryland'),
(32, 223, 'MA', 'Massachusetts'),
(33, 223, 'MI', 'Michigan'),
(34, 223, 'MN', 'Minnesota'),
(35, 223, 'MS', 'Mississippi'),
(36, 223, 'MO', 'Missouri'),
(37, 223, 'MT', 'Montana'),
(38, 223, 'NE', 'Nebraska'),
(39, 223, 'NV', 'Nevada'),
(40, 223, 'NH', 'New Hampshire'),
(41, 223, 'NJ', 'New Jersey'),
(42, 223, 'NM', 'New Mexico'),
(43, 223, 'NY', 'New York'),
(44, 223, 'NC', 'North Carolina'),
(45, 223, 'ND', 'North Dakota'),
(46, 223, 'MP', 'Northern Mariana Islands'),
(47, 223, 'OH', 'Ohio'),
(48, 223, 'OK', 'Oklahoma'),
(49, 223, 'OR', 'Oregon'),
(50, 163, 'PW', 'Palau'),
(51, 223, 'PA', 'Pennsylvania'),
(52, 223, 'PR', 'Puerto Rico'),
(53, 223, 'RI', 'Rhode Island'),
(54, 223, 'SC', 'South Carolina'),
(55, 223, 'SD', 'South Dakota'),
(56, 223, 'TN', 'Tennessee'),
(57, 223, 'TX', 'Texas'),
(58, 223, 'UT', 'Utah'),
(59, 223, 'VT', 'Vermont'),
(60, 223, 'VI', 'Virgin Islands'),
(61, 223, 'VA', 'Virginia'),
(62, 223, 'WA', 'Washington'),
(63, 223, 'WV', 'West Virginia'),
(64, 223, 'WI', 'Wisconsin'),
(65, 223, 'WY', 'Wyoming'),
(66, 38, 'AB', 'Alberta'),
(67, 38, 'BC', 'British Columbia'),
(68, 38, 'MB', 'Manitoba'),
(69, 38, 'NL', 'Newfoundland'),
(70, 38, 'NB', 'New Brunswick'),
(71, 38, 'NS', 'Nova Scotia'),
(72, 38, 'NT', 'Northwest Territories'),
(73, 38, 'NU', 'Nunavut'),
(74, 38, 'ON', 'Ontario'),
(75, 38, 'PE', 'Prince Edward Island'),
(76, 38, 'QC', 'Quebec'),
(77, 38, 'SK', 'Saskatchewan'),
(78, 38, 'YT', 'Yukon Territory'),
(79, 81, 'NDS', 'Niedersachsen'),
(80, 81, 'BAW', 'Baden-Württemberg'),
(81, 81, 'BAY', 'Bayern'),
(82, 81, 'BER', 'Berlin'),
(83, 81, 'BRG', 'Brandenburg'),
(84, 81, 'BRE', 'Bremen'),
(85, 81, 'HAM', 'Hamburg'),
(86, 81, 'HES', 'Hessen'),
(87, 81, 'MEC', 'Mecklenburg-Vorpommern'),
(88, 81, 'NRW', 'Nordrhein-Westfalen'),
(89, 81, 'RHE', 'Rheinland-Pfalz'),
(90, 81, 'SAR', 'Saarland'),
(91, 81, 'SAS', 'Sachsen'),
(92, 81, 'SAC', 'Sachsen-Anhalt'),
(93, 81, 'SCN', 'Schleswig-Holstein'),
(94, 81, 'THE', 'Thüringen'),
(95, 14, 'WI', 'Wien'),
(96, 14, 'NO', 'Niederösterreich'),
(97, 14, 'OO', 'Oberösterreich'),
(98, 14, 'SB', 'Salzburg'),
(99, 14, 'KN', 'Kärnten'),
(100, 14, 'ST', 'Steiermark'),
(101, 14, 'TI', 'Tirol'),
(102, 14, 'BL', 'Burgenland'),
(103, 14, 'VB', 'Voralberg'),
(104, 204, 'AG', 'Aargau'),
(105, 204, 'AI', 'Appenzell Innerrhoden'),
(106, 204, 'AR', 'Appenzell Ausserrhoden'),
(107, 204, 'BE', 'Bern'),
(108, 204, 'BL', 'Basel-Landschaft'),
(109, 204, 'BS', 'Basel-Stadt'),
(110, 204, 'FR', 'Freiburg'),
(111, 204, 'GE', 'Genf'),
(112, 204, 'GL', 'Glarus'),
(113, 204, 'JU', 'Graubnden'),
(114, 204, 'JU', 'Jura'),
(115, 204, 'LU', 'Luzern'),
(116, 204, 'NE', 'Neuenburg'),
(117, 204, 'NW', 'Nidwalden'),
(118, 204, 'OW', 'Obwalden'),
(119, 204, 'SG', 'St. Gallen'),
(120, 204, 'SH', 'Schaffhausen'),
(121, 204, 'SO', 'Solothurn'),
(122, 204, 'SZ', 'Schwyz'),
(123, 204, 'TG', 'Thurgau'),
(124, 204, 'TI', 'Tessin'),
(125, 204, 'UR', 'Uri'),
(126, 204, 'VD', 'Waadt'),
(127, 204, 'VS', 'Wallis'),
(128, 204, 'ZG', 'Zug'),
(129, 204, 'ZH', 'Zürich'),
(130, 195, 'A Coruña', 'A Coruña'),
(131, 195, 'Álava', 'Álava'),
(132, 195, 'Albacete', 'Albacete'),
(133, 195, 'Alicante', 'Alicante'),
(134, 195, 'Almería', 'Almería'),
(135, 195, 'Asturias', 'Asturias'),
(136, 195, 'Ávila', 'Ávila'),
(137, 195, 'Badajoz', 'Badajoz'),
(138, 195, 'Baleares', 'Baleares'),
(139, 195, 'Barcelona', 'Barcelona'),
(140, 195, 'Burgos', 'Burgos'),
(141, 195, 'Cáceres', 'Cáceres'),
(142, 195, 'Cádiz', 'Cádiz'),
(143, 195, 'Cantabria', 'Cantabria'),
(144, 195, 'Castellón', 'Castellón'),
(145, 195, 'Ceuta', 'Ceuta'),
(146, 195, 'Ciudad Real', 'Ciudad Real'),
(147, 195, 'Córdoba', 'Córdoba'),
(148, 195, 'Cuenca', 'Cuenca'),
(149, 195, 'Girona', 'Girona'),
(150, 195, 'Granada', 'Granada'),
(151, 195, 'Guadalajara', 'Guadalajara'),
(152, 195, 'Guipúzcoa', 'Guipúzcoa'),
(153, 195, 'Huelva', 'Huelva'),
(154, 195, 'Huesca', 'Huesca'),
(155, 195, 'Jaén', 'Jaén'),
(156, 195, 'La Rioja', 'La Rioja'),
(157, 195, 'Las Palmas', 'Las Palmas'),
(158, 195, 'León', 'León'),
(159, 195, 'Lérida', 'Lérida'),
(160, 195, 'Lugo', 'Lugo'),
(161, 195, 'Madrid', 'Madrid'),
(162, 195, 'Málaga', 'Málaga'),
(163, 195, 'Melilla', 'Melilla'),
(164, 195, 'Murcia', 'Murcia'),
(165, 195, 'Navarra', 'Navarra'),
(166, 195, 'Ourense', 'Ourense'),
(167, 195, 'Palencia', 'Palencia'),
(168, 195, 'Pontevedra', 'Pontevedra'),
(169, 195, 'Salamanca', 'Salamanca'),
(170, 195, 'Santa Cruz de Tenerife', 'Santa Cruz de Tenerife'),
(171, 195, 'Segovia', 'Segovia'),
(172, 195, 'Sevilla', 'Sevilla'),
(173, 195, 'Soria', 'Soria'),
(174, 195, 'Tarragona', 'Tarragona'),
(175, 195, 'Teruel', 'Teruel'),
(176, 195, 'Toledo', 'Toledo'),
(177, 195, 'Valencia', 'Valencia'),
(178, 195, 'Valladolid', 'Valladolid'),
(179, 195, 'Vizcaya', 'Vizcaya'),
(180, 195, 'Zamora', 'Zamora'),
(181, 195, 'Zaragoza', 'Zaragoza'),
(182, 13, 'ACT', 'Australian Capital Territory'),
(183, 13, 'NSW', 'New South Wales'),
(184, 13, 'NT', 'Northern Territory'),
(185, 13, 'QLD', 'Queensland'),
(186, 13, 'SA', 'South Australia'),
(187, 13, 'TAS', 'Tasmania'),
(188, 13, 'VIC', 'Victoria'),
(189, 13, 'WA', 'Western Australia'),
(190, 105, 'AG', 'Agrigento'),
(191, 105, 'AL', 'Alessandria'),
(192, 105, 'AN', 'Ancona'),
(193, 105, 'AO', 'Aosta'),
(194, 105, 'AR', 'Arezzo'),
(195, 105, 'AP', 'Ascoli Piceno'),
(196, 105, 'AT', 'Asti'),
(197, 105, 'AV', 'Avellino'),
(198, 105, 'BA', 'Bari'),
(199, 105, 'BT', 'Barletta Andria Trani'),
(200, 105, 'BL', 'Belluno'),
(201, 105, 'BN', 'Benevento'),
(202, 105, 'BG', 'Bergamo'),
(203, 105, 'BI', 'Biella'),
(204, 105, 'BO', 'Bologna'),
(205, 105, 'BZ', 'Bolzano'),
(206, 105, 'BS', 'Brescia'),
(207, 105, 'BR', 'Brindisi'),
(208, 105, 'CA', 'Cagliari'),
(209, 105, 'CL', 'Caltanissetta'),
(210, 105, 'CB', 'Campobasso'),
(211, 105, 'CI', 'Carbonia-Iglesias'),
(212, 105, 'CE', 'Caserta'),
(213, 105, 'CT', 'Catania'),
(214, 105, 'CZ', 'Catanzaro'),
(215, 105, 'CH', 'Chieti'),
(216, 105, 'CO', 'Como'),
(217, 105, 'CS', 'Cosenza'),
(218, 105, 'CR', 'Cremona'),
(219, 105, 'KR', 'Crotone'),
(220, 105, 'CN', 'Cuneo'),
(221, 105, 'EN', 'Enna'),
(222, 105, 'FM', 'Fermo'),
(223, 105, 'FE', 'Ferrara'),
(224, 105, 'FI', 'Firenze'),
(225, 105, 'FG', 'Foggia'),
(226, 105, 'FC', 'Forlì Cesena'),
(227, 105, 'FR', 'Frosinone'),
(228, 105, 'GE', 'Genova'),
(229, 105, 'GO', 'Gorizia'),
(230, 105, 'GR', 'Grosseto'),
(231, 105, 'IM', 'Imperia'),
(232, 105, 'IS', 'Isernia'),
(233, 105, 'AQ', 'Aquila'),
(234, 105, 'SP', 'La Spezia'),
(235, 105, 'LT', 'Latina'),
(236, 105, 'LE', 'Lecce'),
(237, 105, 'LC', 'Lecco'),
(238, 105, 'LI', 'Livorno'),
(239, 105, 'LO', 'Lodi'),
(240, 105, 'LU', 'Lucca'),
(241, 105, 'MC', 'Macerata'),
(242, 105, 'MN', 'Mantova'),
(243, 105, 'MS', 'Massa Carrara'),
(244, 105, 'MT', 'Matera'),
(245, 105, 'VS', 'Medio Campidano'),
(246, 105, 'ME', 'Messina'),
(247, 105, 'MI', 'Milano'),
(248, 105, 'MO', 'Modena'),
(249, 105, 'MB', 'Monza e Brianza'),
(250, 105, 'NA', 'Napoli'),
(251, 105, 'NO', 'Novara'),
(252, 105, 'NU', 'Nuoro'),
(253, 105, 'OG', 'Ogliastra'),
(254, 105, 'OT', 'Olbia-Tempio'),
(255, 105, 'OR', 'Oristano'),
(256, 105, 'PD', 'Padova'),
(257, 105, 'PA', 'Palermo'),
(258, 105, 'PR', 'Parma'),
(259, 105, 'PG', 'Perugia'),
(260, 105, 'PV', 'Pavia'),
(261, 105, 'PU', 'Pesaro Urbino'),
(262, 105, 'PE', 'Pescara'),
(263, 105, 'PC', 'Piacenza'),
(264, 105, 'PI', 'Pisa'),
(265, 105, 'PT', 'Pistoia'),
(266, 105, 'PN', 'Pordenone'),
(267, 105, 'PZ', 'Potenza'),
(268, 105, 'PO', 'Prato'),
(269, 105, 'RG', 'Ragusa'),
(270, 105, 'RA', 'Ravenna'),
(271, 105, 'RC', 'Reggio Calabria'),
(272, 105, 'RE', 'Reggio Emilia'),
(273, 105, 'RI', 'Rieti'),
(274, 105, 'RN', 'Rimini'),
(275, 105, 'RM', 'Roma'),
(276, 105, 'RO', 'Rovigo'),
(277, 105, 'SA', 'Salerno'),
(278, 105, 'SS', 'Sassari'),
(279, 105, 'SV', 'Savona'),
(280, 105, 'SI', 'Siena'),
(281, 105, 'SR', 'Siracusa'),
(282, 105, 'SO', 'Sondrio'),
(283, 105, 'TA', 'Taranto'),
(284, 105, 'TE', 'Teramo'),
(285, 105, 'TR', 'Terni'),
(286, 105, 'TO', 'Torino'),
(287, 105, 'TP', 'Trapani'),
(288, 105, 'TN', 'Trento'),
(289, 105, 'TV', 'Treviso'),
(290, 105, 'TS', 'Trieste'),
(291, 105, 'UD', 'Udine'),
(292, 105, 'VA', 'Varese'),
(293, 105, 'VE', 'Venezia'),
(294, 105, 'VB', 'Verbania'),
(295, 105, 'VC', 'Vercelli'),
(296, 105, 'VR', 'Verona'),
(297, 105, 'VV', 'Vibo Valentia'),
(298, 105, 'VI', 'Vicenza'),
(299, 105, 'VT', 'Viterbo');

-- --------------------------------------------------------

--
-- 表的结构 `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转存表中的数据 `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2016_01_04_173148_create_admin_tables', 1),
(4, '2020_03_26_091907_create_China_table', 2);

-- --------------------------------------------------------

--
-- 表的结构 `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 转储表的索引
--

--
-- 表的索引 `admin_menu`
--
ALTER TABLE `admin_menu`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `admin_operation_log`
--
ALTER TABLE `admin_operation_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_operation_log_user_id_index` (`user_id`);

--
-- 表的索引 `admin_permissions`
--
ALTER TABLE `admin_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_permissions_name_unique` (`name`),
  ADD UNIQUE KEY `admin_permissions_slug_unique` (`slug`);

--
-- 表的索引 `admin_roles`
--
ALTER TABLE `admin_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_roles_name_unique` (`name`),
  ADD UNIQUE KEY `admin_roles_slug_unique` (`slug`);

--
-- 表的索引 `admin_role_menu`
--
ALTER TABLE `admin_role_menu`
  ADD KEY `admin_role_menu_role_id_menu_id_index` (`role_id`,`menu_id`);

--
-- 表的索引 `admin_role_permissions`
--
ALTER TABLE `admin_role_permissions`
  ADD KEY `admin_role_permissions_role_id_permission_id_index` (`role_id`,`permission_id`);

--
-- 表的索引 `admin_role_users`
--
ALTER TABLE `admin_role_users`
  ADD KEY `admin_role_users_role_id_user_id_index` (`role_id`,`user_id`);

--
-- 表的索引 `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_users_username_unique` (`username`);

--
-- 表的索引 `admin_user_permissions`
--
ALTER TABLE `admin_user_permissions`
  ADD KEY `admin_user_permissions_user_id_permission_id_index` (`user_id`,`permission_id`);

--
-- 表的索引 `hz_cloud_flare`
--
ALTER TABLE `hz_cloud_flare`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `hz_domains`
--
ALTER TABLE `hz_domains`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `domain` (`domain`),
  ADD KEY `zone_id` (`zone_id`),
  ADD KEY `cf_id` (`cf_id`);

--
-- 表的索引 `hz_servers`
--
ALTER TABLE `hz_servers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ip` (`ip`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `group_id` (`group_id`);

--
-- 表的索引 `hz_server_groups`
--
ALTER TABLE `hz_server_groups`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `hz_settings`
--
ALTER TABLE `hz_settings`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `hz_sites`
--
ALTER TABLE `hz_sites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `domain` (`domain`),
  ADD KEY `server_ip` (`server_ip`),
  ADD KEY `server_id` (`server_id`),
  ADD KEY `tpl_id` (`tpl_id`);

--
-- 表的索引 `hz_site_config`
--
ALTER TABLE `hz_site_config`
  ADD PRIMARY KEY (`id`),
  ADD KEY `site_id` (`site_id`);

--
-- 表的索引 `hz_site_dns_records`
--
ALTER TABLE `hz_site_dns_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `site_id` (`site_id`);

--
-- 表的索引 `hz_site_languages`
--
ALTER TABLE `hz_site_languages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_languages_title` (`title`) USING BTREE;

--
-- 表的索引 `hz_site_templates`
--
ALTER TABLE `hz_site_templates`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `hz_site_tpl_lang`
--
ALTER TABLE `hz_site_tpl_lang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tpl_id` (`tpl_id`),
  ADD KEY `lang_id` (`lang_id`);

--
-- 表的索引 `hz_workers`
--
ALTER TABLE `hz_workers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no` (`no`);

--
-- 表的索引 `hz_zencart_currencies`
--
ALTER TABLE `hz_zencart_currencies`
  ADD PRIMARY KEY (`currencies_id`);

--
-- 表的索引 `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- 表的索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `admin_menu`
--
ALTER TABLE `admin_menu`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- 使用表AUTO_INCREMENT `admin_operation_log`
--
ALTER TABLE `admin_operation_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `admin_permissions`
--
ALTER TABLE `admin_permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- 使用表AUTO_INCREMENT `admin_roles`
--
ALTER TABLE `admin_roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 使用表AUTO_INCREMENT `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- 使用表AUTO_INCREMENT `hz_cloud_flare`
--
ALTER TABLE `hz_cloud_flare`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用表AUTO_INCREMENT `hz_domains`
--
ALTER TABLE `hz_domains`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 使用表AUTO_INCREMENT `hz_servers`
--
ALTER TABLE `hz_servers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- 使用表AUTO_INCREMENT `hz_server_groups`
--
ALTER TABLE `hz_server_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `hz_settings`
--
ALTER TABLE `hz_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `hz_sites`
--
ALTER TABLE `hz_sites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- 使用表AUTO_INCREMENT `hz_site_config`
--
ALTER TABLE `hz_site_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- 使用表AUTO_INCREMENT `hz_site_dns_records`
--
ALTER TABLE `hz_site_dns_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- 使用表AUTO_INCREMENT `hz_site_languages`
--
ALTER TABLE `hz_site_languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- 使用表AUTO_INCREMENT `hz_site_templates`
--
ALTER TABLE `hz_site_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用表AUTO_INCREMENT `hz_site_tpl_lang`
--
ALTER TABLE `hz_site_tpl_lang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- 使用表AUTO_INCREMENT `hz_workers`
--
ALTER TABLE `hz_workers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用表AUTO_INCREMENT `hz_zencart_currencies`
--
ALTER TABLE `hz_zencart_currencies`
  MODIFY `currencies_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 使用表AUTO_INCREMENT `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 使用表AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
