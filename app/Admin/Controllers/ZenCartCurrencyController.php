<?php


namespace App\Admin\Controllers;


use App\Models\ZenCartCountries;
use App\Models\ZenCartCurrencies;
use App\Models\ZenCartZones;
use Illuminate\Http\Request;

class ZenCartCurrencyController extends BaseController
{
    protected $header = "ZenCart货币配置";
    protected $model = ZenCartCurrencies::class;

    protected function _model_init()
    {
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
        $grid->disableExport();
    }

}