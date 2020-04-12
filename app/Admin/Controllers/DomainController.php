<?php


namespace App\Admin\Controllers;

use App\Models\CloudFlare;
use App\Models\Domain;

class DomainController extends BaseController
{
    protected $header = "域名";
    protected $model = Domain::class;

    protected function _model_init()
    {
        parent::_model_init();
        $this->field_config['select']['cf_id'] = CloudFlare::status(1)->pluck('auth_email', 'id');
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