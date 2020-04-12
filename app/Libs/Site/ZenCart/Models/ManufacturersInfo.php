<?php


namespace App\Libs\Site\ZenCart\Models;


class ManufacturersInfo extends BaseModel
{
    protected $table = 'manufacturers_info';

    protected $_pk = ['manufacturers_id','language_id'];

    public function manufacture()
    {
        return $this->belongsTo(Manufacturers::class,'manufacturers_id');
    }
}