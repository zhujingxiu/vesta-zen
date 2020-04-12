<?php


namespace App\Libs\Site\ZenCart\Models;


class Configuration extends BaseModel
{
    protected $table = 'configuration';
    public $timestamps = true;


    public function group()
    {
        return $this->belongsTo(ConfigurationGroup::class,'configuration_group_id');
    }
}