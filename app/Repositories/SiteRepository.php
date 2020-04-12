<?php


namespace App\Repositories;


use App\Models\Site;
use Encore\Admin\Facades\Admin;
use Illuminate\Support\Facades\DB;

class SiteRepository
{
    protected $site;
    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    public function deleteSite($id)
    {
        try {
            DB::connection('mysql')->beginTransaction();
            $entity = $this->site->find($id);
            $entity->delete();
            $entity->config()->delete();
            $entity->dns()->delete();
            $entity->server()->decrement('sites');
            $entity->template()->decrement('sites');
            DB::connection('mysql')->commit();
            return msg_success('站点删除成功', $entity->id);
        } catch (\Exception $e) {
            return msg_success('站点删除失败', $e->getMessage());
        }
    }

    /**
     * @param $domain
     * @param $lang
     * @param $server_id
     * @param $server_ip
     * @param $tpl_id
     * @param $fs_catalog
     * @param $admin_dir
     * @param $db_file
     * @param $db_name
     * @param $db_user
     * @param $db_pwd
     * @param $records
     * @return array
     */
    public function addSite($domain,$lang,$server_id,$server_ip,$tpl_id,
                            $fs_catalog,$admin_dir,$db_file,$db_name,$db_user,$db_pwd,$records=[])
    {
        DB::connection('mysql')->beginTransaction();
        try {
            $entity = $this->site;
            $entity->domain = $domain;
            $entity->lang = $lang;
            $entity->server_id = $server_id;
            $entity->server_ip = $server_ip;
            $entity->tpl_id = $tpl_id;
            $entity->status = 1;
            $entity->admin_id = Admin::user()->id;
            $entity->save();
            $entity->config()->create([
                'fs_catalog' => $fs_catalog,
                'admin_dir' => $admin_dir,
                'db_file' => $db_file,
                'db_name' => $db_name,
                'db_user' => $db_user,
                'db_pwd' => $db_pwd,
                'status' => 1
            ]);
            $entity->server()->increment('sites');
            $entity->template()->increment('sites');
            if ($records){
                foreach ($records as $record){
                    if (!isset($record['parse_mode'])){
                        continue;
                    }
                    $entity->dns()->create($record);
                }
            }
            DB::connection('mysql')->commit();
            return msg_success('站点添加成功', $entity->id);

        }catch (\Exception $e){
            DB::connection('mysql')->rollback();

            return msg_error('站点模板添加失败:'.$e->getMessage());
        }
    }

    public function getSiteByDomain($domain)
    {
        return $this->site->where('domain',trim_all($domain))->first();
    }
}