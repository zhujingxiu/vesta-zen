<?php


namespace App\Repositories;


use App\Models\SiteLanguage;
use Illuminate\Support\Facades\Cache;

class SiteLanguageRepository
{
    protected  $site_language;
    public function __construct(SiteLanguage $siteLanguage)
    {
        $this->site_language = $siteLanguage;
    }

    /**
     * @param $id
     * @param bool $cache
     * @return array|mixed
     */
    public function getLanguageById($id,$cache=true)
    {
        if (!$cache){
            return $this->site_language->find($id)->toArray();
        }
        $languages = $this->getLanguages(true);
        if ($languages && is_array($languages)){
            return $languages[$id] ?? [];
        }
        return [];
    }

    /**
     * @param bool $cache
     * @return mixed
     */
    public function getLanguages($cache=true)
    {
        $key = config('site.languages_key');
        $entities = redis_get($key,true);
        if ($cache && is_array($entities)){
            return $entities;
        }
        $all = $this->site_language->select(['id','title','code','image','dir_name'])
            ->status(1)
            ->orderByRaw('sort,id')
            ->get()->keyBy('id')->toArray();

        if ($cache && $all) {
            redis_set($key, $all);
        }
        return $all;
    }
}