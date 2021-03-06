<?php

namespace App\Admin\Actions\Site;

use App\Libs\CloudFlare\DNS;
use App\Libs\Site\Site;
use App\Repositories\DomainRepository;
use App\Repositories\SiteRepository;
use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;
class BatchDelete extends BatchAction
{
    public $name = '删除产品站';

    public function dialog()
    {
        $this->confirm('确定删除站点？');
    }

    public function handle(Collection $collection)
    {
        $errors = [];
        $affected = [];
        $n = 0;
        foreach ($collection as $model) {
            $server = $model->server;
            $ret = (new Site($server->ip, $server->user, $server->pass))->delete($model->domain, $model->config->db_name);
            if ($ret['code'] != 200) {
                $errors[] = sprintf('[#%s]%s:%s', $model->id, $model->domain, $ret['msg']);
                continue;
            }
            $ret = $this->deleteDNSRecord($model->domain, $model->id);
            if ($ret['code'] != 200) {
                $errors[] = sprintf('[#%s]%s:DNS记录异常-%s', $model->id, $model->domain, $ret['msg']);
                //continue;
            }
            $ret = app(SiteRepository::class)->deleteSite($model->id);
            if ($ret['code'] != 200) {
                $errors[] = sprintf('[#%s]%s:删除站点异常-%s', $model->id, $model->domain, $ret['msg']);
                continue;
            }
            $n++;
            $affected[] = sprintf('[#%s:%s]%s',$model->id,$model->domain,$ret['msg']);
        }
        if ($n) {
            return $this->response()->toastr()
                ->success(action_msg($this->name, $n, $errors).'<br>'.implode('<br>',$affected))
                ->refresh();
        }
        return $this->response()->error(action_msg($this->name,$n,$errors));
    }


    protected function deleteDNSRecord($domain_str, $site_id = null)
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
        $cloudflare = $domain->cloudflare;
        $auth_key = $cloudflare->auth_key;
        $auth_email = $cloudflare->auth_email;
        $cf_dns = new DNS($auth_key, $auth_email);
        $n = 0;
        $errors = [];
        // 从本地数据表查找record_id
        if ($site_id) {
            $records = app(DomainRepository::class)->getRecordsBySiteId($site_id);
            if ($records){
                foreach ($records as $item){
                    if (empty($item['record'])){
                        continue;
                    }
                    $ret = $cf_dns->deleteRecord($zone_id, $item['record']);
                    if ($ret['code'] != 200) {
                        $errors[] = sprintf('删除DNS记录[%s]失败:%s！', $item['name'], $ret['msg']);
                        continue;
                    }
                    $n++;
                }
            }
        } else {// 从CloudFlare查找record_id
            $names = [$domain_str];
            if ($domain_level == 1) {
                $names[] = $top_domain;
            }
            foreach ($names as $name) {
                $ret = $cf_dns->records($zone_id, $name);
                if ($ret['code'] != 200) {
                    continue;
                }
                foreach ($ret['data'] as $item) {
                    if ($item['name'] == $name) {
                        $ret = $cf_dns->deleteRecord($zone_id, $item['id']);
                        if ($ret['code'] != 200) {
                            $errors[] = sprintf('删除DNS记录[%s]失败:%s！', $name, $ret['msg']);
                            continue;
                        }
                        $n++;
                    }
                }
            }
        }
        if ($n){
            $msg = sprintf('执行成功记录:%s条',$n);
            if ($errors){
                $msg .= sprintf('， 失败信息：%s',implode(",",$errors));
            }
            return msg_success($msg,['success'=>$n,'errors'=>$errors]);
        }
        return  msg_error(implode("<br>", $errors));
    }
}