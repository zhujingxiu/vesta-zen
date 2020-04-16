<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Site\Banner;
use App\Admin\Actions\Site\Currency;
use App\Admin\Actions\Site\GoBack;
use App\Admin\Actions\Site\Logo;
use App\Admin\Actions\Site\ProductsDelete;
use App\Admin\Actions\Site\Store;
use App\Admin\Actions\Site\BatchDelete;
use App\Admin\Actions\Site\ProductDownload;
use App\Admin\Actions\Site\ProductImport;
use App\Admin\Actions\Site\ResetPass;
use App\Admin\Actions\Site\BatchRestoreDB;
use App\Admin\Extensions\Tools\GridModal;
use App\Libs\Site\ZenCart\Models\Banners;
use App\Libs\Site\ZenCart\Models\Products;
use App\Libs\Site\ZenCart\Models\Specials;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Models\Site;
use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        })->image('',80,80);
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

    protected function disableActions($grid)
    {
        $grid->actions(function ($actions) {
            $actions->add(new GoBack);
        });
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
        $tools->append(new ResetPass);
        $tools->append(new Logo());
        //$tools->append(new GridModal('banner管理','site-add-banner-modal',$this->bannerForm()));
        $tools->append(new Banner());
        $tools->append(new Currency());
        $tools->append(new Store());
        $tools->append(new ProductsDelete());
    }

}
