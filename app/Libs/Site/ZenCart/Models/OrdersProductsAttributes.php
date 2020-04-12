<?php


namespace App\Libs\Site\ZenCart\Models;


class OrdersProductsAttributes extends BaseModel
{
    public $table = 'orders_products_attributes';

    public function order()
    {
        return $this->belongsTo(Orders::class,'orders_id');
    }

    public function orderProduct()
    {
        return $this->belongsTo(OrdersProducts::class,'orders_products_id')->with('product');
    }

    public function option()
    {
        return $this->belongsTo(ProductsOptions::class,'products_options_id');
    }

    public function optionValue()
    {
        return $this->belongsTo(ProductsOptionsValues::class,'products_options_values_id');
    }
}