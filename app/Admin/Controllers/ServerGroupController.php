<?php


namespace App\Admin\Controllers;

use App\Models\ServerGroup;
use Encore\Admin\Facades\Admin;

class ServerGroupController extends BaseController
{
    protected $header = "工作组";
    protected $model = ServerGroup::class;

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
}