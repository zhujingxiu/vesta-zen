<?php


namespace App\Libs\Site\ZenCart\Models;


class RecordCompany extends BaseModel
{
    public $table = 'record_company';
    public $timestamps = true;


    public function info()
    {
        return $this->hasMany(RecordArtistsInfo::class,'artists_id');
    }
}