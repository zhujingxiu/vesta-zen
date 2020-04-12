<?php


namespace App\Models;


class SiteTemplateLanguage extends BaseModel
{
    protected $table = 'hz_site_tpl_lang';
    protected $admin_user = false;
    public $timestamps = false;

    public function templates()
    {
        return $this->belongsTo(SiteTemplate::class,'tpl_id','id');
    }

    public function languages()
    {
        return $this->belongsTo(SiteLanguage::class,'lang_id','id');
    }
}