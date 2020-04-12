<?php


namespace App\Models;


class SiteDNSRecord extends BaseModel
{
    protected $table = 'hz_site_dns_records';
    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}