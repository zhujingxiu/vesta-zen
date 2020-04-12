<?php


namespace App\Libs\Site\ZenCart\Models;


class Languages extends BaseModel
{
    protected $table = 'languages';

    public function categories()
    {
        return $this->hasMany(CategoriesDescription::class,'language_id');
    }

    public function manufacturers()
    {
        return $this->hasMany(ManufacturersInfo::class,$this->primaryKey);
    }

    public function options()
    {
        return $this->hasMany(ProductsOptions::class,'language_id');
    }

    public function optionsValues()
    {
        return $this->hasMany(ProductsOptionsValues::class,'language_id');
    }

    public function reviews()
    {
        return $this->hasMany(ReviewsDescription::class, $this->primaryKey);
    }
}