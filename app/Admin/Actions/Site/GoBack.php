<?php

namespace App\Admin\Actions\Site;

use App\Admin\Extensions\Actions\XRowAction;
use Illuminate\Database\Eloquent\Model;

class GoBack extends XRowAction
{
    public $name = '去后台';

    public function handle(Model $model)
    {


    }

    public function href()
    {
        return [
            'link' => 'http://'.rtrim($this->row->domain).'/'.$this->row->config->admin_dir,
            'target' => '_blank',
        ];
    }
}