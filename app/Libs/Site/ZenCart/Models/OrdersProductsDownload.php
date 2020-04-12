<?php


namespace App\Libs\Site\ZenCart\Models;


class OrdersProductsDownload extends BaseModel
{
    public $table = 'orders_products_download';

    public function order()
    {
        return $this->belongsTo(Orders::class,'orders_id');
    }

    public function orderProduct()
    {
        return $this->belongsTo(OrdersProducts::class,'orders_products_id')->with('product');
    }

}