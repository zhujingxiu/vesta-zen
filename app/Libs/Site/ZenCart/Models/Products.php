<?php


namespace App\Libs\Site\ZenCart\Models;


class Products extends BaseModel
{
    public $table = 'products';

    public $timestamps = true;

    public const CREATED_AT = 'products_date_added';
    public const UPDATED_AT = 'products_last_modified';

    public function type()
    {
        return $this->belongsTo(ProductTypes::class,'products_type');
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturers::class,'manufacturers_id');
    }

    public function master()
    {
        return $this->belongsTo(Categories::class,'master_categories_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Categories::class,
            'products_to_categories',
            'products_id',
            'categories_id');
    }

    public function description()
    {
        return $this->hasOne(ProductsDescription::class,'products_id');
    }

    public function attributes()
    {
        return $this->hasMany(ProductsAttributes::class,'products_id');
    }

    public function reviews()
    {
        return $this->hasMany(Reviews::class,'products_id');
    }

    public function specials()
    {
        return $this->hasMany(Specials::class,'products_id');
    }

    public function tax()
    {
        return $this->belongsTo(TaxClass::class,'products_tax_class_id');
    }
}