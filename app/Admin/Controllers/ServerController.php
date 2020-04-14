<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Server\BatchPackage;
use App\Admin\Extensions\Tools\AddSite;
use App\Libs\Site\Site;
use App\Models\Server;
use App\Libs\CloudFlare\DNS;
use App\Models\ServerGroup;
use App\Models\SiteDNSRecord;
use App\Models\SiteLanguage;
use App\Repositories\DomainRepository;
use App\Repositories\ServerRepository;
use App\Repositories\SiteLanguageRepository;
use App\Repositories\SiteRepository;
use App\Repositories\SiteTemplateRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ServerController extends BaseController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $header = '服务器';
    protected $model = Server::class;
    protected $serverRepository;
    protected $siteRepository;
    protected $siteLanguageRepository;
    protected $siteTemplateRepository;

    public function __construct(ServerRepository $serverRepository,
                                SiteRepository $siteRepository,
                                SiteLanguageRepository $siteLanguageRepository,
                                SiteTemplateRepository $siteTemplateRepository)
    {
        parent::__construct();
        $this->serverRepository = $serverRepository;
        $this->siteRepository = $siteRepository;
        $this->siteLanguageRepository = $siteLanguageRepository;
        $this->siteTemplateRepository = $siteTemplateRepository;

    }

    protected function _model_init()
    {
        parent::_model_init();
        $this->field_config['replace'] = ['group_id' => 'group.name'];
        $this->field_config['password'] = ['root', 'pass'];
        $this->field_config['select']['status'] = Server::_gird_status_all();
        $this->field_config['select']['group_id'] = ServerGroup::status(1)->pluck('name', 'id');
        $this->field_config['after']['id'] = 'columnServerImg';
    }

    public static function columnServerImg($grid)
    {
        $grid->column('serverImg', ' ')->display(function () {
            $id = $this->id;
            $name = $this->name;
            $ip = $this->ip;
            $user = $this->user;
            $pass = $this->pass;
            $default_img = asset('img/server_default.png');
            return <<<HTML
        <span id="server-entity-{$id}" class="hidden" data-name="{$name}" data-ip="{$ip}" data-user="{$user}" data-pass="{$pass}"></span>  
        <img src="{$default_img}" width="80px">
HTML;
        });
    }

    protected function tool($tools)
    {
        //$tools->append( new AddSite);

        $languages = SiteLanguage::status(1)->selectRaw('CONCAT(title," ",code) AS t,id')->pluck('t', 'id');
        $tools->append(new AddSite(
            '添加站点',
            'server-add-site-modal',
            '/admin/servers/add-site',
            compact('languages')));
    }

    public function addSite(Request $request)
    {
        $start = Carbon::now()->format('H:i:s.u');
        $server_id = $request->get('server_id');
        $server = $this->serverRepository->getServerById($server_id);
        if (!$server) {
            return msg_error('没有找到服务器');
        }
        $server_ip = $request->get('server_ip');
        $server_user = $request->get('server_user');
        $server_pass = $request->get('server_pass');
        $domain_str = trim_all($request->get('domain'));
        $sub_domain = $request->get('sub_domain');
        $level = $request->get('level');
        $lang_id = $request->get('lang_id');
        $tpl_id = $request->get('tpl_id');
        $parse_cf = boolval(array_filter($request->get('parse_cf', [])));
        $siteTemplate = $this->siteTemplateRepository->getTemplateById($tpl_id);
        if (!$siteTemplate) {
            return msg_error(sprintf('站点模板参数不合法： %s', $tpl_id));
        }
        $tpl_dir = $siteTemplate->path;
        $tpl_admin = $siteTemplate->admin_dir;
        $tpl_db = $siteTemplate->db_file;
        $siteLanguage = $this->siteLanguageRepository->getLanguageById($lang_id);
        if (!$siteLanguage) {
            return msg_error(sprintf('语言模板不合法： %s', $lang_id));
        }
        $lang_dir = $siteLanguage->dir_name;
        $lang_code = $siteLanguage->code;
        try {
            $records = [];
            if (!$level) {
                return msg_error(sprintf('域名[%s]不合法', $domain_str));
            }
            if ($this->siteRepository->getSiteByDomain($domain_str)) {
                return msg_error(sprintf('站点域名[%s]已存在', $domain_str));
            }
            $trace_hash = str_random(16) . '==';
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
                            'content' => $item['content']
                        ];
                    }
                }
            }
            $start_add_site = Carbon::now()->format('H:i:s.u');
            list($db_user, $db_pass) = site_db_info();
            $site = new Site($server_ip, $server_user, $server_pass, $trace_hash);
            $ret = $site->add($domain_str, $tpl_dir, $tpl_admin, $tpl_db, $lang_dir, $lang_code, $db_user, $db_pass);
            log_trace_millisecond($trace_hash . 'add-site-finished-time:', $start_add_site, compact('ret'));

            if ($ret['code'] == 200) {
                list($domain,$fs_catalog, $db_name, $db_user, $db_pass) = $ret['data'];
                $ret = $this->storeSite($domain, $lang_dir, $server_id, $tpl_id,
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
     * @param $records
     * @return array
     */
    protected function storeSite($domain, $lang_dir, $server_id, $tpl_id,
                                 $fs_catalog, $admin_dir, $db_file, $db_name, $db_user, $db_pass, $records)
    {
        $server = $this->serverRepository->getServerById($server_id);
        if (!$server) {
            return msg_error('没有找到服务器');
        }
        return $this->siteRepository->addSite($domain, $lang_dir, $server_id, $server->ip,
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

    protected function batch($batch)
    {
        parent::batch($batch);
        $batch->add(new BatchPackage());
    }

    public function store()
    {
        return $this->field_create()->store();
    }

    public function update($id)
    {
        return $this->field_edit($id)->update($id);
    }
}
