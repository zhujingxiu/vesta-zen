<?php


namespace App\Libs\Site;

use App\Libs\Site\ZenCart\Configuration;
use App\Libs\Site\ZenCart\ZenCart;
use App\Libs\Vesta;
use Carbon\Carbon;
use Chumper\Zipper\Zipper;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Site extends BaseSite
{

    protected $site_copy = '';

    public function __construct($host, $user, $pass, $hash = null)
    {
        parent::__construct($host, $user, $pass, $hash);
        $this->site_copy = storage_path(self::SiteCopy());
    }

    public static function SiteCopy()
    {
        return cache_setting('dir_copy') ?? 'app/public/sites/copy';
    }

    /**
     * 校验模板地址目录
     * @param $folder
     * @param $admin_dir
     * @param $lang_dir
     * @param $db_file
     * @return mixed
     */
    private function checkSiteFolder($folder, $admin_dir, $lang_dir = 'english', $db_file = 'zencart.sql')
    {
        $folder = ZenCart::SiteFolder($folder, true);
        // 检查站点目录
        if (!ZenCart::checkSiteFolder($folder)) {
            return $this->error($folder . self::ERR_SITE_TPL,);
        }
        // 检查后台目录
        if (!ZenCart::checkSiteAdminFolder($folder, $admin_dir)) {
            return $this->error(self::ERR_SITE_ADMIN);
        }
        // 检查语言目录
        if (!ZenCart::checkSiteLangFolder($folder, $lang_dir)) {
            return $this->error(self::ERR_SITE_LANG);
        }
        // 检查数据库文件
        if (!ZenCart::checkSiteDBFile($folder, $db_file)) {
            return $this->error(self::ERR_SITE_SQL);
        }
        return $this->success([$folder, ZenCart::SiteDBFile($folder, $db_file)]);
    }

    protected function prepareSiteCopy($domain)
    {
        if (!is_dir($this->site_copy)) {
            mkdir($this->site_copy, 0755, true);
        }

        return sprintf("%s/%s", $this->site_copy, $domain . '-' . str_random(4));
    }

    /**
     * 域名校验
     * @param $domain
     * @param $level
     * @return mixed
     */
    protected function checkDomain($domain)
    {
        $domain = strtolower(trim_all($domain));

        if (!$domain) {
            return $this->error(self::ERR_DOMAIN);
        }
        list($level, $top_domain,) = parse_domain($domain);
        if (!$level) {
            return $this->error(self::ERR_DOMAIN);
        }
        return $this->success($domain);
    }

    /**
     * 校验域名和本地站点模板目录
     * @param $domain
     * @param $tpl_dir
     * @param $admin_dir
     * @param $lang_dir
     * @param $db_file
     * @return mixed
     */
    protected function validate($domain, $tpl_dir, $admin_dir, $lang_dir = 'english', $db_file = 'zencart.sql')
    {
        $ret = $this->checkDomain($domain);
        if ($ret['code'] != self::ERR_OK) {
            return $ret;
        }
        $domain = $ret['data'];
        $ret = $this->checkSiteFolder($tpl_dir, $admin_dir, $lang_dir, $db_file);
        if ($ret['code'] != self::ERR_OK) {
            return $ret;
        }
        list($folder, $db_file) = $ret['data'];
        return $this->success([$domain, $folder, $db_file]);
    }

    /**
     * 1.添加数据库，并将sql文件导入数据库
     * 2.添加域名
     * 3.本地副本打包压缩
     * 4.上传到远程服务器
     * 5.远程解压导入sql文件到数据库
     * 6.修改站点语言
     * @param $domain
     * @param $tpl_dir
     * @param $admin_dir
     * @param $lang_dir
     * @param $lang_code
     * @param $db_file
     * @param $db_user
     * @param $db_pass
     * @return array|mixed
     * @throws \Exception
     */
    public function add($domain, $tpl_dir, $admin_dir, $lang_dir, $lang_code, $db_file, $db_user, $db_pass)
    {

        log_trace_millisecond($this->hash . '0-validate-params-time:', Carbon::now()->format('H:i:s.u'));

        // 1.本地检查域名，模板文件目录及语言包校验
        $start_validate = Carbon::now()->format('H:i:s.u');
        $ret = $this->validate($domain, $tpl_dir, $admin_dir, $lang_dir, $db_file);
        if ($ret['code'] != self::ERR_OK) {
            return $ret;
        }
        log_trace_millisecond($this->hash . '1-validate-params-second:', $start_validate, compact('ret'));
        list($domain, $folder, $db_file) = $ret['data'];

        // 2.添加数据库并恢复sql到数据库
        $start_database = Carbon::now()->format('H:i:s.u');
        $ret = $this->addAndRestoreDatabase($domain, $db_user, $db_pass, $db_file);
        if ($ret['code'] != self::ERR_OK) {
            return $ret;
        }
        log_trace_millisecond($this->hash . '2-add-and-restore-database-second:', $start_database, compact('ret'));
        list($db_name, $db_user, $db_pass) = $ret['data'];

        // 3.添加域名
        $start_domain = Carbon::now()->format('H:i:s.u');
        $ret = $this->addDomain($domain);
        if ($ret['code'] != self::ERR_OK) {
            return $ret;
        }
        log_trace_millisecond($this->hash . '3-add-domain-second:', $start_domain, compact('ret'));

        // 4.本地准备副本目录
        $start_local = Carbon::now()->format('H:i:s.u');
        $ret = $this->prepareSite($domain, $folder, $admin_dir, $db_name, $db_user, $db_pass);
        if ($ret['code'] != self::ERR_OK) {
            return $ret;
        }
        log_trace_millisecond($this->hash . '4-prepare-site-second:', $start_local, compact('ret'));
        $local_copy = $ret['data'];

        // 5.zip压缩本地副本
        $start_zip = Carbon::now()->format('H:i:s.u');
        $ret = $this->zipSite($local_copy);
        if ($ret['code'] != self::ERR_OK) {
            return $ret;
        }
        log_trace_millisecond($this->hash . '5-zip-site-second:', $start_zip, compact('ret'));
        $local_zip = $ret['data'];

        // 6.远程解压站点
        $start_restore = Carbon::now()->format('H:i:s.u');
        $ret = $this->unpackSite($domain, $local_zip);
        if ($ret['code'] != self::ERR_OK) {
            return $ret;
        }
        log_trace_millisecond($this->hash . '6-send-unpack-site-second:', $start_restore, compact('ret'));
        $fs_catalog = $ret['data'];

        // 7.配置站点语言
        $start_setup = Carbon::now()->format('H:i:s.u');
        $ret = $this->setupSite($db_name, $db_user, $db_pass, $lang_code);
        if ($ret['code'] != self::ERR_OK) {
            return $ret;
        }
        log_trace_millisecond($this->hash . '7-setup-site-second:', $start_setup, compact('ret'));

        // 8.删除本地副本目录及zip文件
        del_folder($local_copy);
        unlink($local_zip);
        log_trace_millisecond($this->hash . '8-del-local-second:', Carbon::now()->format("H:i:s.u"));
        return $this->success([$domain, $fs_catalog, $db_name, $db_user, $db_pass]);
    }

    /**
     * 删除站点
     * 1.删域名
     * 2.删数据库
     * @param $domain
     * @param $db_name
     * @return array
     */
    public function delete($domain, $db_name)
    {
        $ret = $this->vesta->deleteDomain($this->server_user, $domain);
        if (is_array($ret)) {
            return $this->error($this->wrapperError(self::ERR_VESTA_DOMAIN_DEL, $ret), self::ERR_VESTA_DOMAIN_DEL);
        }
        $ret = $this->vesta->deleteDatabase($this->server_user, $db_name);
        if (is_array($ret)) {
            return $this->error($this->wrapperError(self::ERR_VESTA_DB_DEL, $ret), self::ERR_VESTA_DB_DEL);
        }
        return $this->success();
    }

    /**
     * 2添加域名
     * @param $domain
     * @return array
     */
    public function addDomain($domain)
    {
        //域名
        $ret = $this->vesta->addDomain($this->server_user, $domain);
        if (is_array($ret)) {
            return $this->error($this->wrapperError(self::ERR_VESTA_WEB, $ret), self::ERR_VESTA_WEB);
        }
        return $this->success($domain);
    }

    /**
     * 2添加数据库
     * @param $domain
     * @param $db_user
     * @param $db_pass
     * @param $sql_file
     * @return array
     */
    public function addAndRestoreDatabase($domain, $db_user, $db_pass, $sql_file = null)
    {
        $start_add_database = Carbon::now()->format('H:i:s.u');
        //数据库
        $ret = $this->vesta->addDatabase($this->server_user, $domain, $db_user, $db_pass);
        if (is_array($ret)) {
            //var_dump($ret);
            return $this->error($this->wrapperError(self::ERR_VESTA_DB, $ret), self::ERR_VESTA_DB);
        }
        log_trace_millisecond($this->hash . 'addAndRestoreDatabase-restoreDatabase-cmd-restore-time:', $start_add_database);
        $db_info = Vesta::SiteDBInfo($this->server_user, $domain, $db_user, $db_pass);
        if ($sql_file) {
            list($db_name, $db_user, $db_pass) = $db_info;
            $ret = $this->restoreDatabase($sql_file, $db_name, $db_user, $db_pass);
            if ($ret['code'] != 200) {
                return $ret;
            }
        }
        return $this->success($db_info);
    }

    /**
     * @param $db_file
     * @param $db_name
     * @param $db_user
     * @param $db_pass
     * @param $stream_blocking
     * @return array
     */
    public function restoreDatabase($db_file, $db_name, $db_user, $db_pass, $stream_blocking = false)
    {
        if (!file_exists($db_file)) {
            return $this->error(self::ERR_SITE_SQL);
        }
        $file = Vesta::SiteTempDBFile($this->server_user, basename($db_file));
        // 1.发送DB文件
        $ret = ssh_send_file($this->server_ip, $this->server_user, $this->server_pass, $db_file, $file);
        if ($ret['code']!=200 || empty($ret['data']['connection'])) {
            return $this->error('sql文件发送失败', self::ERR_SITE_RESTORE);
        }
        $connection = $ret['data']['connection'];
        // 2.执行数据库恢复命令
        $cmd = sprintf('mysql -u %s -p%s %s < %s', $db_user, $db_pass, $db_name, $file);
        $ret = ssh_command($connection,$cmd,$stream_blocking,$this->hash);
        if ($ret['code']!=200){
            return $this->error($ret['msg'], self::ERR_SITE_RESTORE);
        }
        //ssh2_exec($connection, "rm -f {$file}");
        return $this->success($file);
    }


    /**
     * 创建本地副本目录
     * @param $domain
     * @param $folder
     * @param $admin_dir
     * @param $db_name
     * @param $db_user
     * @param $db_pass
     * @return array
     */
    protected function prepareSite($domain, $folder, $admin_dir, $db_name, $db_user, $db_pass)
    {
        // 1.本地准备副本目录
        $site_folder = $this->prepareSiteCopy($domain);
        copy_folder($folder, $site_folder);
        if (!is_dir($site_folder)) {
            return $this->error(self::ERR_SITE_SRC);
        }
        // 2.配置站点文件
        $ret = $this->configSite($site_folder, $admin_dir, $domain, $db_name, $db_user, $db_pass);
        if ($ret['code'] != self::ERR_OK) {
            return $ret;
        }
        return $this->success($site_folder);
    }

    /**
     * 修改站点配置信息
     * @param $folder
     * @param $admin_dir
     * @param $domain
     * @param $db_name
     * @param $db_user
     * @param $db_pass
     * @return array
     */
    private function configSite($folder, $admin_dir, $domain, $db_name, $db_user, $db_pass)
    {
        // 1.配置站点前台configuration
        $config_file = ZenCart::SiteConfigFile($folder);
        if (!file_exists($config_file)) {
            return $this->error(self::ERR_SITE_CONF);
        }
        $fs_catalog = Vesta::SiteRoot($this->server_user, $domain);
        $write = Configuration::SiteConfiguration($config_file, $fs_catalog, $domain, $db_name, $db_user, $db_pass);
        if (!$write) {
            return $this->error(self::ERR_SITE_CONF_WR);
        }
        // 2.配置站点后台configuration
        $admin_config = ZenCart::SiteAdminConfigFile($folder, $admin_dir);
        if (!$admin_config || !file_exists($admin_config)) {
            return $this->error(self::ERR_SITE_CONF);
        }
        $write = Configuration::SiteConfiguration($admin_config, $fs_catalog, $domain, $db_name, $db_user, $db_pass,true);
        if ($write) {
            return $this->error(self::ERR_SITE_CONF_WR);
        }
        return $this->success($config_file);
    }

    /**
     * zip压缩本地副本
     * @param $folder
     * @return array
     * @throws \Exception
     */
    protected function zipSite($folder)
    {
        if (!is_dir($folder)) {
            return $this->error(self::ERR_SITE_SRC);
        }
        $zip_name = sprintf('%s/%s.zip', dirname($folder), basename($folder));
        // 副本目录打包成zip
        $zipper = new Zipper();
        $result = $zipper->make($zip_name)->add(glob($folder)); // 添加需要打包路径，配置打包后路径以及文件名
        $zipFile = $result->getFilePath();
        $result->close();
        return $this->success($zipFile);
    }

    /**
     * 用ssh2扩展操作zip传输到远程服务器
     * unpackSite
     * scp-send-zipfile
     * @param $domain
     * @param $local_zip
     * @return array
     */
    public function unpackSite($domain, $local_zip)
    {
        $tmp_zip = Vesta::SiteTempZipPath($this->server_user, basename($local_zip));
        $unzip_path = Vesta::SiteRoot($this->server_user, $domain);

        // 1.发送DB文件
        $ret = ssh_send_file($this->server_ip, $this->server_user, $this->server_pass, $local_zip, $tmp_zip,log_hash($this->hash.__METHOD__));
        if ($ret['code']!=200 || empty($ret['data']['connection'])) {
            return $this->error('sql文件发送失败', self::ERR_SITE_ZIP_FTP);
        }
        $connection = $ret['data']['connection'];
        $cmd = sprintf('unzip -o %s -d %s', $tmp_zip, $unzip_path);
        $ret = ssh_command($connection,$cmd,true);
        if ($ret['code']!=200){
            $this->error($ret['msg'], self::ERR_SITE_RESTORE);
        }
        ssh_command($connection,"rm -f {$tmp_zip}");
        return $this->success($unzip_path);
    }

    /**
     * 配置zencart:数据库等信息
     * @param $db_name
     * @param $db_user
     * @param $db_pass
     * @param $lang_code
     * @return array
     */
    public function setupSite($db_name, $db_user, $db_pass, $lang_code)
    {
        $zen = new Configuration($this->server_ip, $db_user, $db_pass, $db_name);
        $ret = $zen->config($lang_code, 'DEFAULT_LANGUAGE');
        return $ret ? $this->success() : $this->error(self::ERR_SITE_LANG_UP);
    }


    /**
     * @param array $db_list
     * @param array $ignore
     * @return string
     */
    public function deleteDatabases($db_list = [], $ignore = [])
    {
        $databases = $this->vesta->listDatabases($this->server_user);
        echo '<pre>';
        $db_list = is_string($db_list) ? array_filter(explode(",", $db_list)) : $db_list;
        var_dump($db_list);
        var_dump($databases);
        echo '</pre>';
        $ignores = is_string($ignore) ? array_filter(explode(",", $ignore)) : $ignore;
        $n = 0;
        $errors = [];
        $ignores[] = 'admin_default';
        if (is_array($databases)) {
            foreach ($databases as $db) {
                if (!isset($db['DATABASE']) || !isset($db['DBUSER'])) {
                    continue;
                }
                $db_name = $db['DATABASE'];
                if ($ignores && is_array($ignores) && in_array($db_name, $ignores)) {
                    continue;
                }
                if (!$db_list || (is_array($db_list) && in_array($db_name, $db_list))) {
                    $ret = $this->vesta->deleteDatabase($this->server_user, $db_name);
                    if (is_array($ret)) {
                        $errors[] = sprintf("DB[%s] delete error:%s", $db_name, implode(" - ", $ret));
                    } else {
                        $n++;
                    }
                }
            }
        }
        return sprintf(" %s database deleted. error:%s", $n, implode(" | ", $errors));
    }
}