<?php


namespace App\Models;


use Encore\Admin\Facades\Admin;;

class SiteTemplate extends BaseModel
{
    protected $table = "hz_site_templates";

    public function languages()
    {
        return $this->belongsToMany(SiteLanguage::class,'hz_site_tpl_lang','tpl_id','lang_id');
    }

    public function admin()
    {
        return $this->belongsTo(AdminUsers::class);
    }


}