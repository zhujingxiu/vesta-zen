<?php


namespace App\Libs\Site\ZenCart\Models;


class OrdersProducts extends BaseModel
{
    public $table = 'orders_products';

    public function order()
    {
        return $this->belongsTo(Orders::class,'orders_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class,'products_id');
    }


}