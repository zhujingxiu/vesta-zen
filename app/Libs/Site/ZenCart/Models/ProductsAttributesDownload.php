<?php


namespace App\Libs\Site\ZenCart\Models;


class ProductsAttributesDownload extends BaseModel
{
    public $table = 'products_attributes_download';

    public function attribute()
    {
        return $this->belongsTo(ProductsAttributes::class,'products_attributes_id');
    }


}