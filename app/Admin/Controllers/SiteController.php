<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Site\Banner;
use App\Admin\Actions\Site\Currency;
use App\Admin\Actions\Site\Logo;
use App\Admin\Actions\Site\ProductsDelete;
use App\Admin\Actions\Site\Store;
use App\Admin\Actions\Site\BatchDelete;
use App\Admin\Actions\Site\ProductDownload;
use App\Admin\Actions\Site\ProductImport;
use App\Admin\Actions\Site\ResetPwd;
use App\Admin\Actions\Site\BatchRestoreDB;
use App\Admin\Extensions\Tools\GridModal;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Models\Site;
use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;

class SiteController extends BaseController
{

    protected $header = "站点";
    protected $model = Site::class;

    protected function _model_init()
    {
        parent::_model_init();
        $this->field_config['link'] = ['domain'];
        $this->field_config['after']['id'] = 'columnPreview';
        $this->field_config['after']['server_id'] = 'columnTemplate';
        $this->field_config['replace'] = ['server_id' => 'server.name', 'tpl_id'=>'template.name'];
    }
    public function columnPreview($grid){
        $grid->column(' ')->display(function () {
            return $this->template->preview;
        })->image();
    }

    public  function columnTemplate($grid)
    {
        $grid->cloumn('组别')->display(function () {

             return $this->server->group->name;
        });
    }
    protected function setGridMethods($grid)
    {
        $grid->disableCreateButton();
    }

    protected function batch($batch)
    {
        //parent::batch($batch);
        $batch->add(new BatchRestoreDB());
        $batch->add(new BatchDelete());
    }

    protected function tool($tools)
    {
        $tools->append(new ProductImport());
        $tools->append(new ProductDownload());
        $tools->append(new ResetPwd());
        $tools->append(new Logo());
        //$tools->append(new GridModal('banner管理','site-add-banner-modal',$this->bannerForm()));
        $tools->append(new Banner());
        $tools->append(new Currency());
        $tools->append(new Store());
        $tools->append(new ProductsDelete());
    }

    public function addBanner(Request $request)
    {

    }

    protected function bannerForm()
    {
        $form = new Form();
        $form->action(route('admin.sites.add-banner'));
        $form->hidden('selected','');
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
        $js=<<<JS
        console.log('hhhh');
JS;

Admin::script($js);
        return $form->render();
    }
}
