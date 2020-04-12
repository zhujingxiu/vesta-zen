<?php


namespace App\Models;


class SiteConfig extends BaseModel
{

    protected $table = 'hz_site_config';

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}