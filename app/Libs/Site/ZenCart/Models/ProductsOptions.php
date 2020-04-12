<?php


namespace App\Libs\Site\ZenCart\Models;


class ProductsOptions extends BaseModel
{
    public $table = 'products_options';

    public function type()
    {
        return $this->belongsTo(ProductsOptionsTypes::class,'products_options_type');
    }

    public function values()
    {
        return $this->belongsToMany(ProductsOptionsValues::class,
            'products_options_values_to_products_options',
            $this->primaryKey,
            'products_options_values_id');
    }
}