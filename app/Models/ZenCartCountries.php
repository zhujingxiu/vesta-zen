<?php


namespace App\Models;


class ZenCartCountries extends BaseModel
{
    protected $table = 'hz_zencart_countries';
    protected $primaryKey = 'countries_id';

    public $timestamps = false;

    public function zones()
    {
        return $this->hasMany(ZenCartZones::class,'zone_country_id');
    }
}