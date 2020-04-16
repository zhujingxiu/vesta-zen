<?php

use Illuminate\Routing\Router;

Admin::routes();
Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {

    $router->resource('/auth/users', 'WorkerController')->names('admin.auth.users')->except('delete');
    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->get('/settings', 'SettingController@index')->name('admin.settings');
    $router->get('/sites', 'SiteController@index')->name('admin.sites');
    $router->post('/sites/add-banner', 'SiteController@addBanner')->name('admin.sites.add-banner');
    $router->resource('/site-languages', SiteLanguageController::class)->except('delete');
    $router->resource('/site-templates', SiteTemplateController::class)->except('delete');
    $router->resource('/servers', ServerController::class)->except('delete');
    $router->post('/servers/add-site', 'ServerController@addSite')->name('admin.addSite');
    $router->resource('/server-groups', ServerGroupController::class)->except('delete');
    $router->resource('/cloud-flare', CloudFlareController::class)->except('delete');
    $router->resource('/domains', DomainController::class)->except('delete');
    $router->resource('/ext', ExtController::class)->except('delete');
    $router->resource('/zc-countries', ZenCartCountryController::class)->except('delete');
    $router->resource('/zc-zones', ZenCartZoneController::class)->except('delete');
    $router->resource('/zc-currencies', ZenCartCurrencyController::class)->except('delete');


    $router->get('/tools/{acts?}', 'GitToolController@index');


    $router->group(['prefix' => 'api'], function ($api) {
        $api->get('/templates', 'SiteTemplateController@apiTemplates');
        $api->get('/parse-domain', 'ToolsController@apiParseDomain');
        $api->get('/zones', 'ZenCartCountryController@apiZones');
    });
});
