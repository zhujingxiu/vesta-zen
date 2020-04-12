<?php


namespace App\Libs\Site\ZenCart\Models;


class Countries extends BaseModel
{
    public $table = 'countries';

    public function address()
    {
        return $this->belongsTo(AddressFormat::class,'address_format_id');
    }

    public function zones()
    {
        return $this->hasMany(Zones::class,'zone_country_id');
    }
}