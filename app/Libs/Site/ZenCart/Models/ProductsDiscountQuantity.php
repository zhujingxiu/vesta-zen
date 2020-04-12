<?php


namespace App\Libs\Site\ZenCart\Models;


class ProductsDiscountQuantity extends BaseModel
{
    public $table = 'products_discount_quantity';
    protected $_pk = ['discount_id','products_id'];

    public function product()
    {
        return $this->belongsTo(Products::class,'products_id');
    }


}