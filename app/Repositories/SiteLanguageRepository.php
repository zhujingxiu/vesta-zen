<?php


namespace App\Repositories;


use App\Models\SiteLanguage;

class SiteLanguageRepository
{
    protected  $site_language;
    public function __construct(SiteLanguage $siteLanguage)
    {
        $this->site_language = $siteLanguage;
    }

    public function getLanguageById($id)
    {
        return $this->site_language->find($id);
    }
}