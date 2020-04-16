<?php


namespace App\Models;


class SiteLanguage extends BaseModel
{
    protected $table = "hz_site_languages";

    public function admin()
    {
        return $this->belongsTo(AdminUsers::class);
    }
}