<?php

namespace App\Admin\Actions\Site;

use App\Admin\Extensions\Actions\XBatchAction;
use Encore\Admin\Actions\BatchAction;
use Encore\Admin\Widgets\Form;
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


    public function xForm(Form $form)
    {

        $form->radio('status ', 'Banner Status')->options([0=>'禁用',1=>'启用'])
            ->help('NOTE: Banner status will be updated based on Scheduled Date and Impressions')
            ->default(1);
        $form->radio('banners_open_new_windows ', 'Banner New Window')->options([0=>'否',1=>'是'])
            ->help('NOTE: Banner will open in a new window')
            ->default(1);
        $form->radio('banners_on_ssl ', 'Banner on SSL')->options([0=>'禁用',1=>'启用'])
            ->help('NOTE: Banner can be displayed on Secure Pages without errors')
            ->default(1);
        $form->text('banners_title','Banner Title');
        $form->text('banners_url','Banner URL');
        $form->select('banners_group','Banner Group')->options([
            'BannersAll'=>'BannersAll',
            'SideBox-Banners'=>'SideBox-Banners',
            'Wide-Banners'=>'Wide-Banners',
        ]);
        $form->image('banners_image','Image')->help('default save to /home/admin/web/domain/public_html/images/');

        $form->textarea('code','HTML Text');
        $form->text('banners_sort_order','Sort Order - banner_box_all')
            ->help('NOTE: The banners_box_all sidebox will display the banners in their defined sort order')
            ->default(0);
        $form->date('date_scheduled','Scheduled At');
        $form->date('expires_date','Expires On');
        $form->text('expires_impressions','Expires OR At')->help('impressions/views');
        //dd($this);

        return $form;
    }

    public function handle(Collection $collection, Request $request)
    {
        foreach ($collection as $model) {

        }

        return $this->response()->success('Success message.')->refresh();
    }
}