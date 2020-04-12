<?php


namespace App\Admin\Controllers;

use App\Models\ZenCartZones;

class ZenCartZoneController extends BaseController
{
    protected $header = "ZenCartZone区域表";
    protected $model = ZenCartZones::class;

    protected function _model_init()
    {
        parent::_model_init();
        $this->field_config['replace'] = ['zone_country_id'=>'country.countries_name'];
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
        $grid->disableExport();

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