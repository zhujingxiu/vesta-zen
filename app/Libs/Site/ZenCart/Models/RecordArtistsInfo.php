<?php


namespace App\Libs\Site\ZenCart\Models;


class RecordArtistsInfo extends BaseModel
{
    public $table = 'record_artists_info';
    protected $_pk = ['artists_id','languages_id'];
    public function artist()
    {
        return $this->belongsTo(RecordArtists::class,'artists_id');
    }

}