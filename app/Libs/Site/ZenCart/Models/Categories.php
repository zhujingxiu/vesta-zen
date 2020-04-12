<?php


namespace App\Libs\Site\ZenCart\Models;



class Categories extends BaseModel
{
    public function categories()
    {
        return $this->hasMany(self::class,'parent_id')->with(['categories','description']);
    }

    public function descriptions()
    {
        return $this->hasMany(CategoriesDescription::class,$this->primaryKey);
    }

    public function types()
    {
        return $this->belongsToMany(ProductTypes::class,'product_types_to_category','category_id','product_type_id');
    }

    public function products()
    {
        return $this->belongsToMany(Products::class,
            'products_to_categories',
            'categories_id',
            'products_id');
    }
}