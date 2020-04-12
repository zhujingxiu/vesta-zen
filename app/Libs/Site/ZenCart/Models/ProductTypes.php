<?php


namespace App\Libs\Site\ZenCart\Models;


class ProductTypes extends BaseModel
{
    public $table = 'product_types';
    protected $_pk = 'type_id';
    public $timestamps = true;

    public function layouts()
    {
        return $this->hasMany(ProductTypeLayout::class,'product_type_id');
    }

    public function products()
    {
        return $this->hasMany(Products::class,'products_type');
    }

    public function categories()
    {
        return $this->belongsToMany(Categories::class,
            'product_types_to_category',
            'product_type_id',
            'category_id');
    }
}