<?php


namespace App\Libs\Site;

use App\Libs\Site\ZenCart\ZenCart;
use App\Libs\Vesta;
use Carbon\Carbon;
use Chumper\Zipper\Zipper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Site extends BaseSite
{

    protected $site_copy = '';

    public function __construct($host, $user, $pass, $hash = null)
    {
        parent::__construct($host, $user, $pass, $hash);
        $this->site_copy = storage_path(config('site.site_copy') ?? 'app/public/sites/copy');
    }

    /**
     * 校验模板地址目录
     * @param $folder
     * @param $admin_dir
     * @param $lang
     * @return mixed
     */
    private function checkSiteFolder($folder,$admin_dir, $lang='english')
    {
        $folder = storage_path($folder);
        // 检查站点目录
        if (!is_dir($folder)) {
            //Log::info($this->hash . $_SERVER['SERVER_PORT'] . 'site-folder-err:' . $folder);
            //Log::info($this->hash . $_SERVER['SERVER_PORT'] . 'site-folder-public-path:' . is_dir('sites/zen-cart-2020'));
            return $this->error(self::ERR_SITE_TPL);
        }
        // 检查后台目录
        $folder = rtrim($folder,'/');
        if (!is_dir($folder.'/'.$admin_dir)){
            return $this->error(self::ERR_SITE_ADMIN);
        }
        // 检查语言目录
        if (!is_dir($folder . "/includes/languages/" . $lang)) {
            return $this->error(self::ERR_SITE_LANG);
        }
        return $this->success($folder);
    }

    /**
     * 域名校验
     * @param $domain
     * @param $level
     * @return mixed
     */
    protected function checkFormatDomain($domain)
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
     * @param $db_file
     * @param $lang
     * @return mixed
     */
    protected function validate($domain, $tpl_dir, $admin_dir,$db_file, $lang='english')
    {
        $ret = $this->checkFormatDomain($domain);
        if ($ret['code'] != self::ERR_OK) {
            return $ret;
        }
        $domain = $ret['data'];
        $ret = $this->checkSiteFolder($tpl_dir,$admin_dir,$lang);
        if ($ret['code'] != self::ERR_OK) {
            return $ret;
        }
        $folder = $ret['data'];

        // zencart.sql
        $bakSql = sprintf("%s/%s", $folder,$db_file);
        if (!file_exists($bakSql)) {
            return $this->error(self::ERR_SITE_SQL);
        }
        return $this->success([$domain, $folder,$bakSql]);
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
     * @param $db_file
     * @param $lang_dir
     * @param $lang_code
     * @param $default_dbuser
     * @param $default_dbpass
     * @return array|mixed
     * @throws \Exception
     */
    public function add($domain, $tpl_dir,$admin_dir,$db_file, $lang_dir,$lang_code, $default_dbuser, $default_dbpass)
    {
        if (!is_dir($this->site_copy)) {
            mkdir($this->site_copy, 0755, true);
        }
        log_trace_millisecond($this->hash . '0-validate-params-time:' , Carbon::now()->format('H:i:s.u'));

        // 1.本地检查域名，模板文件目录及语言包校验
        $start_validate = Carbon::now()->format('H:i:s.u');
        $ret = $this->validate($domain, $tpl_dir,$admin_dir,$db_file, $lang_dir);
        if ($ret['code'] != self::ERR_OK) {
            return $ret;
        }
        log_trace_millisecond($this->hash . '1-validate-params-second:',$start_validate,compact('ret'));
        list($domain, $folder,$bak_sql) = $ret['data'];

        // 2.添加数据库并恢复sql到数据库
        $start_database = Carbon::now()->format('H:i:s.u');
        $ret = $this->addAndRestoreDatabase($domain, $default_dbuser, $default_dbpass,$bak_sql);
        if ($ret['code'] != self::ERR_OK) {
            return $ret;
        }
        log_trace_millisecond($this->hash . '2-add-and-restore-database-second:',$start_database,compact('ret'));
        list($db_name, $db_user, $db_pass) = $ret['data'];

        // 3.添加域名
        $start_domain = Carbon::now()->format('H:i:s.u');
        $ret = $this->addDomain($domain);
        if ($ret['code'] != self::ERR_OK) {
            return $ret;
        }
        log_trace_millisecond($this->hash . '3-add-domain-second:',$start_domain,compact('ret'));

        // 4.本地准备副本目录
        $start_local = Carbon::now()->format('H:i:s.u');
        $ret = $this->prepareSite($domain, $folder, $admin_dir,$db_name, $db_user, $db_pass);
        if ($ret['code'] != self::ERR_OK) {
            return $ret;
        }
        log_trace_millisecond($this->hash . '4-prepare-site-second:',$start_local,compact('ret'));
        $local_copy = $ret['data'];

        // 5.zip压缩本地副本
        $start_zip = Carbon::now()->format('H:i:s.u');
        $ret = $this->zipSite($local_copy);
        if ($ret['code'] != self::ERR_OK) {
            return $ret;
        }
        log_trace_millisecond($this->hash . '5-zip-site-second:',$start_zip,compact('ret'));
        $local_zip = $ret['data'];

        // 6.远程解压站点
        $start_restore = Carbon::now()->format('H:i:s.u');
        $ret = $this->unpackSite($domain, $local_zip);
        if ($ret['code'] != self::ERR_OK) {
            return $ret;
        }
        log_trace_millisecond($this->hash . '6-send-unpack-site-second:',$start_restore,compact('ret'));
        $fs_catalog = $ret['data'];

        // 7.配置站点语言
        $start_setup = Carbon::now()->format('H:i:s.u');
        $ret = $this->setupSite($db_name, $db_user, $db_pass, $lang_code);
        if ($ret['code'] != self::ERR_OK) {
            return $ret;
        }
        log_trace_millisecond($this->hash . '7-setup-site-second:',$start_setup,compact('ret'));

        // 8.删除本地副本目录及zip文件
        del_folder($local_copy);
        unlink($local_zip);
        log_trace_millisecond($this->hash . '8-del-local-second:' , Carbon::now()->format("H:i:s.u"));
        return $this->success([$domain,$fs_catalog, $db_name, $db_user, $db_pass]);
    }

    /**
     * 删除站点
     * 1.删域名
     * 2.删数据库
     * @param $domain
     * @param $db_name
     * @return array
     */
    public function delete($domain,$db_name)
    {
        $ret = $this->vesta->deleteDomain($this->server_user,$domain);
        if (is_array($ret)){
            return $this->error($this->wrapperError(self::ERR_VESTA_DOMAIN_DEL, $ret), self::ERR_VESTA_DOMAIN_DEL);
        }
        $ret = $this->vesta->deleteDatabase($this->server_user,$db_name);
        if (is_array($ret)){
            return $this->error($this->wrapperError(self::ERR_VESTA_DB_DEL, $ret), self::ERR_VESTA_DB_DEL);
        }
        return $this->success();
    }

    public function sendFile($local,$remote){
        $start_scp_send = Carbon::now()->format('H:i:s.u');
        try {
            $connection = ssh2_connect($this->server_ip, 22);
            ssh2_auth_password($connection, $this->server_user, $this->server_pass);
            // 传输到远程
            if (ssh2_scp_send($connection, $local, $remote, 0644)) {
                Log::info($this->hash.'sendBanner-ssh2-scp-send-time:' . var_export([
                        'start' => $start_scp_send,
                        'diff' => Carbon::now()->diffInMilliseconds($start_scp_send),
                        'end' => Carbon::now()->format('H:i:s.u'),
                        'local'=>$local,
                        'remote'=>$remote,
                    ], true));
                return msg_success('文件发送成功',['path'=>$remote]);
            }
        } catch (\Exception $e) {
            Log::info($this->hash.'sendBanner-ssh2-scp-send-error:' . var_export([
                    'start' => $start_scp_send,
                    'diff' => Carbon::now()->diffInMilliseconds($start_scp_send),
                    'end' => Carbon::now()->format('H:i:s.u'),
                    'local'=>$local,
                    'remote'=>$remote,
                    'Exception'=>$e->getMessage(),
                ], true));
            return msg_error($e->getMessage());
        }
        return msg_error('文件发送失败');
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
    public function addAndRestoreDatabase($domain, $db_user, $db_pass,$sql_file=null)
    {
        $start_add_database = Carbon::now()->format('H:i:s.u');
        //数据库
        $ret = $this->vesta->addDatabase($this->server_user, $domain, $db_user, $db_pass);
        if (is_array($ret)) {
            //var_dump($ret);
            return $this->error($this->wrapperError(self::ERR_VESTA_DB, $ret), self::ERR_VESTA_DB);
        }
        log_trace_millisecond($this->hash . 'addAndRestoreDatabase-restoreDatabase-cmd-restore-time:' , $start_add_database);
        $user_prefix = $this->server_user . '_';
        $db_info = [
            stripos($domain, $user_prefix) === false ? $user_prefix . $domain : $domain,
            stripos($db_user, $user_prefix) === false ? $user_prefix . $db_user : $db_user,
            $db_pass
        ];
        if ($sql_file){
            list($db_name,$db_user,$db_pass) = $db_info;
            $ret = $this->restoreDatabase($sql_file,$db_name,$db_user,$db_pass);
            if ($ret['code']!=200){
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
    public function restoreDatabase($db_file,$db_name,$db_user, $db_pass,$stream_blocking=false)
    {
        if (!file_exists($db_file)){
            return $this->error(self::ERR_SITE_SQL);
        }
        $file = sprintf("/home/%s/tmp/%s",$this->server_user,date('ymdhis').'-'.basename($db_file));
        try {
            $connection = ssh2_connect($this->server_ip, 22);
            ssh2_auth_password($connection, $this->server_user, $this->server_pass);// 传输到远程

            if (ssh2_scp_send($connection, $db_file, $file, 0644)) {
                $start_cmd_restore = Carbon::now()->format('H:i:s.u');
                $cmd2 = sprintf('mysql -u %s -p%s %s < %s', $db_user, $db_pass, $db_name, $file);
                $stream2 = ssh2_exec($connection, $cmd2);
                $errorStream2 = ssh2_fetch_stream($stream2, SSH2_STREAM_STDERR);
                stream_set_blocking($errorStream2, $stream_blocking);
                stream_set_blocking($stream2, $stream_blocking);
                $errOutput2 = stream_get_contents($errorStream2);
                Log::info($this->hash . 'addAndRestoreDatabase-restoreDatabase-cmd-restore-time:' . var_export([
                        'start' => $start_cmd_restore,
                        'diff' => Carbon::now()->diffInMilliseconds($start_cmd_restore),
                        'end' => Carbon::now()->format('H:i:s.u'),
                        'cmd' => $cmd2,
                        'errOutput' => $errOutput2,
                    ], true));
                if ($errOutput2) {
                    return $this->error($errOutput2, self::ERR_SITE_RESTORE);
                } else {
                    //ssh2_exec($connection, "rm -f {$file}");
                    return $this->success($file);
                }
            } else {
                return $this->error('sql文件发送失败', self::ERR_SITE_RESTORE);
            }
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), self::ERR_SITE_RESTORE);
        }
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
    protected function prepareSite($domain, $folder,$admin_dir, $db_name, $db_user, $db_pass)
    {
        // 本地准备副本目录
        $site_folder = sprintf("%s/%s", $this->site_copy, $domain . '-' . str_random(4));
        copy_folder($folder, $site_folder);
        if (!is_dir($site_folder)) {
            return $this->error(self::ERR_SITE_SRC);
        }
        // 配置站点文件
        $ret = $this->configSite($site_folder,$admin_dir, $domain, $db_name, $db_user, $db_pass);
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
    private function configSite($folder,$admin_dir, $domain, $db_name, $db_user, $db_pass)
    {
        $configFile = $folder . '/includes/configure.php';
        $fs_catalog = sprintf("/home/%s/web/%s/public_html/", $this->server_user, $domain);
        $ret = $this->siteConfigure($configFile,$fs_catalog,$domain, $db_name, $db_user, $db_pass);
        if ($ret['code']!=200){
            return $ret;
        }
        $admin_config = sprintf( '%s/%s/includes/configure.php',$folder,$admin_dir);
        $ret = $this->siteAdminConfigure($admin_config,$fs_catalog,$domain, $db_name, $db_user, $db_pass);
        if ($ret['code']!=200){
            return $ret;
        }
        return $this->success($configFile);
    }

    /**
     * 前台config配置
     * @param $site_config
     * @param $fs_catalog
     * @param $domain
     * @param $db_name
     * @param $db_user
     * @param $db_pass
     * @return array
     */
    private function siteConfigure($site_config,$fs_catalog,$domain,$db_name,$db_user,$db_pass)
    {
        if (!file_exists($site_config)) {
            return $this->error(self::ERR_SITE_CONF);
        }
        $content = file_get_contents($site_config);
        $newContent = preg_replace(
            [
                "/define\('HTTP_SERVER', '.*'\).*;.*/",
                "/define\('HTTPS_SERVER', '.*'\).*;.*/",
                "/define\('DIR_FS_CATALOG', '.*'\).*;.*/",
                "/define\('DB_SERVER_USERNAME', '.*'\).*;.*/",
                "/define\('DB_SERVER_PASSWORD', '.*'\).*;.*/",
                "/define\('DB_DATABASE', '.*'\).*;.*/",
            ],
            [
                sprintf("define('HTTP_SERVER', 'http://%s');", $domain),
                sprintf("define('HTTPS_SERVER', 'https://%s');", $domain),
                sprintf("define('DIR_FS_CATALOG', '%s');", $fs_catalog),
                sprintf("define('DB_SERVER_USERNAME', '%s');", $db_user),
                sprintf("define('DB_SERVER_PASSWORD', '%s');", $db_pass),
                sprintf("define('DB_DATABASE', '%s');", $db_name),
            ],
            $content);
        chmod($site_config, 0755);
        $ret = file_put_contents($site_config, $newContent);
        if ($ret === false) {
            return $this->error(self::ERR_SITE_CONF_WR);
        }
        return $this->success($site_config);
    }

    /**
     * 后台config配置
     * @param $admin_config
     * @param $fs_catalog
     * @param $domain
     * @param $db_name
     * @param $db_user
     * @param $db_pass
     * @return array
     */
    private function siteAdminConfigure($admin_config,$fs_catalog,$domain,$db_name,$db_user,$db_pass)
    {
        if (!$admin_config || !file_exists($admin_config)) {
            return $this->error(self::ERR_SITE_CONF);
        }
        $content = file_get_contents($admin_config);
        $newContent = preg_replace(
            [
                "/define\('HTTP_SERVER', '.*'\).*;.*/",
                "/define\('HTTP_CATALOG_SERVER', '.*'\).*;.*/",
                "/define\('HTTPS_CATALOG_SERVER', '.*'\).*;.*/",
                "/define\('DIR_FS_CATALOG', '.*'\).*;.*/",
                "/define\('DB_SERVER_USERNAME', '.*'\).*;.*/",
                "/define\('DB_SERVER_PASSWORD', '.*'\).*;.*/",
                "/define\('DB_DATABASE', '.*'\).*;.*/",
            ],
            [
                sprintf("define('HTTP_SERVER', 'http://%s');", $domain),
                sprintf("define('HTTP_CATALOG_SERVER', 'http://%s');", $domain),
                sprintf("define('HTTPS_CATALOG_SERVER', 'https://%s');", $domain),
                sprintf("define('DIR_FS_CATALOG', '%s');", $fs_catalog),
                sprintf("define('DB_SERVER_USERNAME', '%s');", $db_user),
                sprintf("define('DB_SERVER_PASSWORD', '%s');", $db_pass),
                sprintf("define('DB_DATABASE', '%s');", $db_name),
            ],
            $content);
        chmod($admin_config, 0755);
        $ret = file_put_contents($admin_config, $newContent);
        if ($ret === false) {
            return $this->error(self::ERR_SITE_CONF_WR);
        }
        return $this->success($admin_config);
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
        $file = date('mdHis') . str_random(4) . '-' . basename($local_zip);
        $site_zip = sprintf("/home/%s/tmp/%s", $this->server_user, $file);
        $site_folder = sprintf("/home/%s/web/%s/public_html", $this->server_user, $domain);

        try {
            $connection = ssh2_connect($this->server_ip, 22);
            ssh2_auth_password($connection, $this->server_user, $this->server_pass);
            // 传输到远程
            $start_scp_send = Carbon::now()->format('H:i:s.u');
            if (ssh2_scp_send($connection, $local_zip, $site_zip, 0644)) {
                Log::info($this->hash.'unpackSite-ssh2-scp-send-time:' . var_export([
                        'start' => $start_scp_send,
                        'diff' => Carbon::now()->diffInMilliseconds($start_scp_send),
                        'end' => Carbon::now()->format('H:i:s.u'),
                    ], true));
                // 1解压zip
                $run_time = 1;
                while ($run_time>0) {
                    $start_cmd_unzip = Carbon::now()->format('H:i:s.u');
                    $cmd = sprintf('unzip -o %s -d %s', $site_zip, $site_folder);
                    $stream = ssh2_exec($connection, $cmd);
                    $errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
                    stream_set_blocking($errorStream, true);
                    stream_set_blocking($stream, true);
                    $errOutput = stream_get_contents($errorStream);
                    Log::info($this->hash . 'unpackSite-cmd-unzip-time:' . var_export([
                            'start' => $start_cmd_unzip,
                            'diff' => Carbon::now()->diffInMilliseconds($start_cmd_unzip),
                            'end' => Carbon::now()->format('H:i:s.u'),
                            'cmd' => $cmd,
                            'errOutput' => $errOutput,
                        ], true));
                    if ($errOutput) {

                        $run_time--;
                        if ($run_time<=0){
                            return $this->error($errOutput, self::ERR_SITE_RESTORE);
                        }else{
                            usleep(50000);//一微秒等于一百万分之一秒
                        }
                    } else {
                        $run_time = 0;
                        ssh2_exec($connection, "rm -f {$site_zip}");
                    }
                }
            } else {
                return $this->error(self::ERR_SITE_ZIP_FTP);
            }
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), self::ERR_SITE_RESTORE);
        }
        return $this->success($site_folder);
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
        $zen = new ZenCart($this->server_ip,$db_user,$db_pass,$db_name);
        $ret = $zen->config($lang_code,'DEFAULT_LANGUAGE');
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