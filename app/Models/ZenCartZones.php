<?php


namespace App\Models;


class ZenCartZones extends BaseModel
{
    protected $table = 'hz_zencart_zones';
    protected $primaryKey = 'zone_id';
    public $timestamps = false;

    public function country()
    {
        return $this->belongsTo(ZenCartCountries::class,'zone_country_id');
    }
}