<?php


namespace App\Admin\Controllers;

use App\Repositories\SiteTemplateRepository;
use Illuminate\Http\Request;
use App\Models\SiteLanguage;
use Encore\Admin\Facades\Admin;

class SiteLanguageController extends BaseController
{
    protected $header = "语言模板";
    protected $model = SiteLanguage::class;

    protected function _model_init()
    {
        $this->field_config['image'] = ['image' => asset('upload')];
        parent::_model_init();
    }

    public function store()
    {
        return $this->field_create()->store();
    }

    public function update($id)
    {
        return $this->field_edit($id)->update($id);
    }

    protected function setGridMethods($grid)
    {
        $grid->disableCreateButton();

    }

    protected function batch($batch)
    {
        //parent::batch($batch);
    }

//    protected function disableActions($grid)
//    {
//        $grid->actions(function ($actions){
//            // 去掉删除
//            $actions->disableDelete();
//
//            // 去掉编辑
//            $actions->disableEdit();
//
//            // 去掉查看
//            //$actions->disableView();
//        });
//    }
}