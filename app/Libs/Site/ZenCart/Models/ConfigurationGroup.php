<?php


namespace App\Libs\Site\ZenCart\Models;


class ConfigurationGroup extends BaseModel
{
    protected $table = 'configuration_group';

    public function configurations()
    {
        return $this->hasMany(Configuration::class,$this->primaryKey);
    }
}