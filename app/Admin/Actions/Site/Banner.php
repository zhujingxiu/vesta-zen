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
        $form->radio('status ', '状态')->options([0=>'禁用',1=>'启用'])
            ->help('广告状态将按生效日期和显示更新')
            ->default(1);
        $form->radio('banners_open_new_windows ', '新窗口打开')->options([0=>'否',1=>'是'])
            ->help('广告将在新窗口打开')
            ->default(1);
        $form->radio('banners_on_ssl ', '带SSL')->options([0=>'禁用',1=>'启用'])
            ->help('广告可以无误地显示在安全页面')
            ->default(1);
        $form->text('banners_title','标题');
        $form->text('banners_url','URL');
        $form->select('banners_group','组别')->options([
            'BannersAll'=>'BannersAll',
            'SideBox-Banners'=>'SideBox-Banners',
            'Wide-Banners'=>'Wide-Banners',
        ]);
        $form->image('banners_image','图片');
        $form->text('banners_image_local','图片保存路径')->help('默认保存在网站目录的/images下');

        $form->textarea('code','HTML文本');
        $form->number('banners_sort_order','排序值')
            ->help('banners_box_all边框按照设定的顺序显示广告')
            ->default(0);
        $form->date('date_scheduled','启用日');
        $form->date('expires_date','有效期');
        $form->number('expires_impressions','或在x 查看');
        return $form;
    }

    public function handle(Collection $collection, Request $request)
    {
        foreach ($collection as $model) {

        }

        return $this->response()->success('Success message.')->refresh();
    }
}