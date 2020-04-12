<?php


namespace App\Libs\Site\ZenCart\Models;


class ProductsAttributes extends BaseModel
{
    public $table = 'products_attributes';

    public function product()
    {
        return $this->belongsTo(Products::class,'products_id');
    }

    public function option()
    {
        return $this->belongsTo(ProductsOptions::class,'options_id');
    }

    public function value()
    {
        return $this->belongsTo(ProductsOptionsValues::class,'options_values_id');
    }
}