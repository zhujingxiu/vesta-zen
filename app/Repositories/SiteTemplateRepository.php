<?php

namespace App\Repositories;


use App\Models\SiteTemplate;
use App\Models\SiteTemplateLanguage;
use Encore\Admin\Facades\Admin;
use Illuminate\Support\Facades\Cache;

class SiteTemplateRepository
{

    protected $site_template;

    public function __construct(SiteTemplate $siteTemplate)
    {
        $this->site_template = $siteTemplate;
    }

    /**
     * 添加站点模板
     * @param $name
     * @param $author
     * @param $preview
     * @param $path
     * @param $admin_dir
     * @param $db_file
     * @param $remark
     * @param array $languages
     * @return array
     */
    public function addTemplate($name,$author,$preview,$path,$admin_dir,$db_file,$remark,$languages=[])
    {
        $entity = $this->site_template;
        $entity->name = $name;
        $entity->author = $author;
        $entity->preview = $preview;
        $entity->path = $path;
        $entity->admin_dir = $admin_dir;
        $entity->db_file = $db_file;
        $entity->remark = $remark;
        $entity->status = 1;
        $entity->admin_id = Admin::user()->id;

        if ($entity->save()) {
            $entity->languages()->detach();
            $languages = array_filter($languages);
            if ($languages){
                $entity->languages()->attach($languages);
            }

            return msg_success('站点模板添加成功', $entity->id);
        } else {
            return msg_error('站点模板添加失败');
        }
    }

    /**
     * 编辑站点模板
     * @param $id
     * @param $name
     * @param $author
     * @param $preview
     * @param $path
     * @param $admin_dir
     * @param $db_file
     * @param $remark
     * @param $status
     * @param $languages
     * @return array
     */
    public function editTemplate($id, $name,$author,$preview,$path,$admin_dir,$db_file,$remark,$status,$languages)
    {
        $entity = $this->site_template->find($id);
        $entity->name = $name;
        $entity->author = $author;
        $entity->admin_dir = $admin_dir;
        $entity->db_file = $db_file;
        $entity->remark = $remark;
        $entity->status = $status;
        $entity->admin_id = Admin::user()->id;
        if ($preview){
            $entity->preview = $preview;
        }
        if ($path){
            $entity->path = $path;
        }
        if ($entity->save()) {
            $entity->languages()->detach();
            $languages = array_filter($languages);
            if ($languages){
                $entity->languages()->attach($languages);
            }
            return msg_success('站点模板修改成功', $id);
        } else {
            return msg_error('站点模板修改失败');
        }
    }

    /**
     * @param bool $cache
     * @return mixed
     */
    public function getTemplates($cache=true)
    {
        $key = config('site.templates_key');
        $templates = redis_get($key,true);
        if ($cache && $templates){
            return $templates;
        }
        $all = $this->site_template->status(1)
            ->select(['id','name','author','preview','path','admin_dir','db_file','sites','remark'])
            ->orderBy('id')
            ->get()->keyBy('id')->toArray();

        if ($cache && $all) {
            redis_set($key, $all);
        }
        return $all;
    }
    /**
     * 通过id获取站点模板
     * @param $id
     * @param $cache
     * @return mixed
     */
    public function getTemplateById($id,$cache=true)
    {
        if (!$cache){
            return $this->site_template->find($id)->toArray();
        }
        $templates = $this->getTemplates(true);
        if ($templates && is_array($templates)){
            return $templates[$id] ?? [];
        }
        return [];
    }

    public function getTemplateByName($name)
    {
        return $this->site_template->where('name',$name)->first();
    }

    public function getTemplatesByLangId($langId){
        return SiteTemplateLanguage::where('lang_id',$langId)->with(['templates'=>function($q){
            return $q->status(1);
        },'templates.languages'=>function($q){
            return $q->status(1);
        }])->get();
    }
}