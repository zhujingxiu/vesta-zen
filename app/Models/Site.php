<?php


namespace App\Models;


class Site extends BaseModel
{
    protected $table = "hz_sites";

    public function config()
    {
        return $this->hasOne(SiteConfig::class);
    }

    public function template()
    {
        return $this->belongsTo(SiteTemplate::class,'tpl_id');
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function dns()
    {
        return $this->hasMany(SiteDNSRecord::class);
    }
}