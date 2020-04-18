<?php

namespace App\Listeners;

use App\Events\ServiceCacheEvent;
use App\Models\SiteLanguage;
use App\Models\SiteTemplate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ResolveServiceCache
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ServiceCacheEvent  $event
     * @return void
     */
    public function handle(ServiceCacheEvent $event)
    {
        $keys = $this->cacheKeys($event->mode);
        if ($keys){
            foreach ($keys as $key){
                Cache::store('redis')->forget($key);
            }

        }
    }

    protected function cacheKeys($mode)
    {
        if (is_object($mode)){
            $mode = get_class($mode);
        }else{
            $mode = strtolower($mode);
        }

        $keys = [];
        switch ($mode){
            case 'language':
            case SiteLanguage::class:
                $keys[] = config('site.languages_key');
                break;
            case 'template':
            case SiteTemplate::class:
                $keys[] = config('site.templates_key');
                break;
        }

        return $keys;

    }
}
