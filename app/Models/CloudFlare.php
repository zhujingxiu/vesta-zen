<?php


namespace App\Models;


class CloudFlare extends BaseModel
{
    protected $table='hz_cloud_flare';

    public function domains()
    {
        return $this->hasMany(Domain::class,'cf_id');
    }
}