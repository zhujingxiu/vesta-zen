<?php


namespace App\Libs\Site\ZenCart\Models;


class Manufacturers extends BaseModel
{
    protected $table = 'manufacturers';
    protected $_pk = 'manufacturers_id';

    public function info()
    {
        return $this->hasOne(Manufacturers::class,$this->primaryKey);
    }

    public function products()
    {
        return $this->hasMany(Products::class,$this->primaryKey);
    }
}