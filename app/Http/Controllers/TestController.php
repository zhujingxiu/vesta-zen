<?php


namespace App\Http\Controllers;


use App\Libs\CloudFlare\User;
use App\Libs\CloudFlare\DNS;
use App\Libs\CloudFlare\Zone;
use App\Libs\Site\Site;
use App\Libs\Site\ZenCart\ImportProduct;
use App\Libs\Site\ZenCart\Models\CategoriesDescription;
use App\Libs\Site\ZenCart\Models\TaxClass;
use App\Libs\Vesta;

use App\Repositories\DomainRepository;
use App\Repositories\ServerRepository;
use App\Repositories\SiteLanguageRepository;
use App\Repositories\SiteRepository;
use App\Repositories\SiteTemplateRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController
{
    private $server_ip = "46.4.85.58";
    private $server_user = "admin";
    private $server_pass = "tgJoyl5pru";
    private $server_ip2 = "95.216.117.155";
    private $server_user2 = "admin";
    private $server_pass2 = "EqP5C9UgvZ";
    private $site;
    private $site2;
    public function __construct()
    {
        try {
            $this->site = new Site($this->server_ip, $this->server_user, $this->server_pass);
            //$this->site2 = new Site($this->server_ip2, $this->server_user2, $this->server_pass2);
        }catch (\Exception $e){
            dd($e->getMessage());
        }
    }

    public function database()
    {
        $ret = $this->site->deleteDatabases("",["admin_hengze_oa"]);
        dd($ret , time());
        list($default_dbuser,$default_dbpass) = $this->genDatabaseInfo();
        for($i = 0;$i<100;$i++) {
            $domain = str_random(3).($i<10 ? '0'.$i : $i);

            $ret = ($this->site->addDatabase($domain,$default_dbuser,$default_dbpass));
            echo '<pre>';
            var_dump($ret);
            echo '</pre>';
        }
        dd('执行验证账户操作:<br>',$this->site->checkAccount($this->server_user,$this->server_pass));
    }

    public function sites()
    {
        $domain_str = str_random(6).".homeuom.com";
        $lang_id = 5;
        $tpl_id = 1;
        $parse_cf = 1;
    }

    public function site(Request $request)
    {
        $start = Carbon::now()->format('H:i:s.u');
        $domain_str = $request->get('domain',str_random(6).".homeuom.com");
        $lang_id = $request->get('lang',2);
        $tpl_id = $request->get('tpl',2);
        $parse_cf = $request->get('cf',1);
        $level = 2;
        $server_id = 9;
        $server_ip = $this->server_ip;
        $server_user = $this->server_user;
        $server_pass = $this->server_pass;
        try {
            $siteTemplate = app(SiteTemplateRepository::class)->getTemplateById($tpl_id);
            if (!$siteTemplate) {
                return msg_error(sprintf('站点模板参数不合法： %s', $tpl_id));
            }
            $tpl_dir = $siteTemplate->path;
            $tpl_admin = $siteTemplate->admin_dir;
            $tpl_db = $siteTemplate->db_file;
            $siteLanguage = app(SiteLanguageRepository::class)->getLanguageById($lang_id);
            if (!$siteLanguage) {
                return msg_error(sprintf('语言模板不合法： %s', $lang_id));
            }
            $lang_dir = $siteLanguage->dir_name;
            $lang_code = $siteLanguage->code;
            $records = [];
            if (!$level) {
                return msg_error(sprintf('域名[%s]不合法', $domain_str));
            }
            if (app(SiteRepository::class)->getSiteByDomain($domain_str)) {
                return msg_error(sprintf('站点域名[%s]已存在', $domain_str));
            }
            $trace_hash = str_random(16) . '-test-site==';
            $start_parse = Carbon::now()->format('H:i:s.u');
            if ($parse_cf) {
                $ret = $this->parseDNSByCloudFlare($server_ip, $domain_str);
                log_trace_millisecond($trace_hash . 'cf-parse-dns-finished-time', $start_parse, compact('ret'));

                if (is_string($ret)) {
                    return msg_error(sprintf('CF解析错误： %s', $ret));
                } else {

                    foreach ($ret as $item) {
                        $records[] = [
                            'parse_mode' => 'CloudFlare',
                            'record' => $item['id'],
                            'type' => $item['type'],
                            'name' => $item['name'],
                            'content' => $item['content'],
                            'status' => 1
                        ];
                    }
                }
            }
            list($db_user, $db_pass) = site_db_info();
            $start_add_site = Carbon::now()->format('H:i:s.u');
            $site = new Site($server_ip, $server_user, $server_pass, $trace_hash);
            $ret = $site->add($domain_str, $tpl_dir, $tpl_admin, $tpl_db, $lang_dir, $lang_code, $db_user, $db_pass);
            log_trace_millisecond($trace_hash . 'add-site-finished-time:', $start_add_site, compact('ret'));

            if ($ret['code'] == 200) {
                list($domain_str,$fs_catalog, $db_name, $db_user, $db_pass) = $ret['data'];
                $ret = $this->storeSite($domain_str, $lang_dir, $server_id, $tpl_id,
                    $fs_catalog, $tpl_admin, $tpl_db, $db_name, $db_user, $db_pass, $records);
                if ($ret['code'] != 200) {
                    return msg_error(sprintf('添加站点成功，本地数据更新失败： %s', $ret['msg']));
                }
                return msg_success('添加成功耗时秒数：' . Carbon::now()->diffInMilliseconds($start), ['redirect' => url('/admin/sites')]);
            }
            return msg_error(sprintf('添加站点失败： %s', $ret['msg']));
        } catch (\Exception $e) {
            return msg_error(sprintf('添加站点异常失败： %s', $e->getMessage()));
        }
    }


    /**
     * 本地信息写入表
     * @param $domain
     * @param $lang_dir
     * @param $server_id
     * @param $tpl_id
     * @param $fs_catalog
     * @param $admin_dir
     * @param $db_file
     * @param $db_name
     * @param $db_user
     * @param $db_pass
     * @return array
     */
    protected function storeSite($domain, $lang_dir, $server_id, $tpl_id,
                                 $fs_catalog, $admin_dir, $db_file, $db_name, $db_user, $db_pass, $records)
    {
        $server = app(ServerRepository::class)->getServerById($server_id);
        if (!$server) {
            return msg_error('没有找到服务器');
        }
        return app(SiteRepository::class)->addSite($domain, $lang_dir, $server_id, $server->ip,
            $tpl_id, $fs_catalog, $admin_dir, $db_file, $db_name, $db_user, $db_pass, $records);

    }

    /**
     * @param $server_ip
     * @param $domain_str
     * @return string|array
     */
    protected function parseDNSByCloudFlare($server_ip, $domain_str)
    {
        list($domain_level, $top_domain,) = parse_domain($domain_str);
        if (!$domain_level) {
            return sprintf('域名[%s]不合法', $domain_str);
        }
        $domain = app(DomainRepository::class)->getDomain($top_domain);
        $zone_id = $domain->zone_id;
        if (!$zone_id) {
            return sprintf('域名[%s]尚未关联CloudFlare账户', $domain_str);
        }
        if ($domain_level == 1) {
            $names = ["@", "www"];
        } else {
            $names = [substr($domain_str, 0, strpos($domain_str, "."))];
        }
        $cloudflare = $domain->cloudflare;
        $auth_key = $cloudflare->auth_key;
        $auth_email = $cloudflare->auth_email;
        $cf_dns = new DNS($auth_key, $auth_email);
        //dd($names,$zone_id,$cf_dns);
        $errors = [];
        $result = [];
        foreach ($names as $name) {
            $ret = $cf_dns->addRecord($zone_id, $name, $server_ip);
            if ($ret['code'] != 200) {
                $errors[] = $ret['msg'];
                continue;
            }
            $result[] = $ret['data'];
        }
        return $errors ? implode("", $errors) : $result;
    }

    public function cloudflare()
    {
        $auth_email = '760609240@qq.com';
        $auth_key = '3e8d78952fbd09ceef065b9114ad868d01373';
        $cf_user = new DNS($auth_key,$auth_email);
        $records = $cf_user->records('adb82dfbc53ee080ce583cc5815b6df5','homeuom.com');
        dd($records);
        $result = $cf_user->getFirstAccount();
        if ($result['code']!=200){
            dd('get-account-info-error:',$result);
        }
        $account = $result['data'];
        $cf_zone = new Zone($auth_key,$auth_email);
        $result = $cf_zone->addZone("mausen.cn",$account['id']);
        dd($cf_user,$user,$account,$cf_zone->zones(),$result);
    }

    /**
     *
    "WEB_TEMPLATE" => "default",
    "PROXY_TEMPLATE" => "default",
    "DNS_TEMPLATE" => "default",
    "WEB_DOMAINS" => "1000",
    "WEB_ALIASES" => "1000",
    "DNS_DOMAINS" => "100",
    "DNS_RECORDS" => "100",
    "MAIL_DOMAINS" => "100",
    "MAIL_ACCOUNTS" => "100",
    "DATABASES" => "1000",
    "CRON_JOBS" => "100",
    "DISK_QUOTA" => "unlimited",
    "BANDWIDTH" => "100000",
    "NS" => "ns1.domain.tld,ns2.domain.tld",
    "SHELL" => "nologin",
    "BACKUPS" => "3",
    "TIME" => "18:00:00",
    "DATE" => "2017-12-28",
     */
    public function package()
    {
        $cp = new Vesta($this->server_ip,$this->server_user,$this->server_pass);

        $ret1 = $cp->changeAdminPackageConfig('4PWE8c8Df8xkPP','admin');
        $pkgs = $cp->listUserPackages();
        dd($ret1,$pkgs);
    }

    public function restore()
    {
        $ret = DB::select("SHOW TABLES like 'userss'");

        dd($ret);
        $site = new Site("192.168.235.222","admin","SovywHBh5r");
        $ret = $site->restoreSite(
            "zzz.mausen.cn",
            "/home/admin/tmp/0331053415I13Y-zzz.mausen.cn-I13Y.zip",
            "admin_zzz.mausen.cn",
            "admin_myZenCart",
            "tgJoyl5C9UgvZ");
        dd($ret);
    }

    public function index()
    {
//        $dir_path1 = './sites/zen-cart-2020';
//        $dir_path2 = 'sites/zen-cart-2020';
//        echo '<pre>';
//        var_dump([$dir_path1,is_dir($dir_path1),public_path($dir_path1),$dir_path2, is_dir($dir_path2),public_path($dir_path2)]);
//        echo '</pre>';
        $server1 = '192.168.235.222';
        $server2 = '192.168.235.111';
        $db_name = 'admin_lll346.homeuom.com';
        $db_user = 'admin_myZenCart';
        $db_pass = 'tgJoyl5C9UgvZ';
        $ret = (new Vesta($server1,'admin','SovywHBh5r'))->deleteDomain('admin','2342.homeuom.com');
        dd($ret);
//        $conn = new_db_connection($server1,$db_user,$db_pass,$db_name);
//        $import = new Import($server1,$db_user,$db_pass,$db_name);
//        $res = $import->getTaxClassByTitle('Taxable Goods');
//        dd($res->tax_class_id);
//        $model = (new CategoriesDescription())->setConnection($conn);
//
//        $res = $model->where(['categories_id'=>1,'language_id'=>1])->with(['categories'])->get();
//        dd($model,$res,(new TaxClass())->setConnection($conn)->first());
    }
}