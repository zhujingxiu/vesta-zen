<?php

namespace App\Admin\Actions\Site;

use App\Admin\Extensions\Actions\XBatchAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class Banner extends XBatchAction
{
    public $name = '设置首页广告';
    protected $selector = '.site-banner';

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-default site-banner">设置首页广告</a>
HTML;
    }

    public function form()
    {
//        $this->radio('status ', 'Banner Status')->options([0=>'禁用',1=>'启用'])
//            ->help('NOTE: Banner status will be updated based on Scheduled Date and Impressions')
//            ->default(1);
//        $this->radio('banners_open_new_windows ', 'Banner New Window')->options([0=>'否',1=>'是'])
//            ->help('NOTE: Banner will open in a new window')
//            ->default(1);
        $this->select('banners_on_ssl ', 'Banner on SSL')->options([0=>'禁用',1=>'启用'])
            ->help('NOTE: Banner can be displayed on Secure Pages without errors')
            ->default(1);
        $this->text('banners_title','Banners Title');
        $this->text('banners_url','Banner URL');
        $this->select('banners_url','Banner Group')->options([
            'BannersAll'=>'BannersAll',
            'SideBox-Banners'=>'SideBox-Banners',
            'Wide-Banners'=>'Wide-Banners',
        ]);
        $this->image('banners_image','Image')->help('default save to /home/admin/web/domain/public_html/images/');

        //$this->coderPHP('text','HTML Text');
    }

    public function handle(Collection $collection, Request $request)
    {
        foreach ($collection as $model) {

        }

        return $this->response()->success('Success message.')->refresh();
    }
}