<?php


namespace App\Libs\Site\ZenCart\Models;


class ProductTypeLayout extends BaseModel
{
    public $table = 'product_type_layout';
    protected $_pk = 'configuration_id';
    public $timestamps = true;

    public function type()
    {
        return $this->belongsTo(ProductTypes::class,'product_type_id');
    }
}