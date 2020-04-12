<?php


namespace App\Libs\Site\ZenCart\Models;


class MetaTagsProductsDescription extends BaseModel
{
    public $table = 'meta_tags_products_description';
    protected $_pk = ['products_id','language_id'];

    public function product()
    {
        return $this->belongsTo(Products::class,'products_id');
    }
}