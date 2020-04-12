<?php


namespace App\Libs\Site\ZenCart\Models;


class TaxClass extends BaseModel
{
    public $table = 'tax_class';
    public $timestamps = true;

    public function rates()
    {
        return $this->hasMany(TaxRates::class,'tax_class_id');
    }

    public function products()
    {
        return $this->hasMany(Products::class,'products_tax_class_id');
    }
}