<?php


namespace App\Libs\Site\ZenCart\Models;


class RecordArtists extends BaseModel
{
    public $table = 'record_artists';
    protected $_pk = 'artists_id';
    public $timestamps = true;


    public function info()
    {
        return $this->hasMany(RecordArtistsInfo::class,'artists_id');
    }
}