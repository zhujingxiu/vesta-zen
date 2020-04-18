<?php


namespace App\Models;


use App\Events\ServiceCacheEvent;

class SiteLanguage extends BaseModel
{
    protected $table = "hz_site_languages";

    public function admin()
    {
        return $this->belongsTo(AdminUsers::class);
    }
    // 有更新则清除缓存
    protected $dispatchesEvents = [
        'saved' => ServiceCacheEvent::class
    ];
}