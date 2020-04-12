<?php


namespace App\Admin\Controllers;


use App\Models\ZenCartCountries;
use App\Models\ZenCartZones;
use Illuminate\Http\Request;

class ZenCartCountryController extends BaseController
{
    protected $header = "ZenCartCountry国家表";
    protected $model = ZenCartCountries::class;

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

    public function apiZones(Request $request)
    {
        $id = $request->get('q');

        $zones = ZenCartZones::where('zone_country_id',$id)->get();

        $options = [['id' => ' ', 'text' => '请选择']];
        foreach ($zones as $v) {
            $options[] = [
                'id' => $v->zone_id,
                'text' => $v->zone_id.' '.$v->zone_name
            ];
        }
        return msg_success('', $options);
    }
}