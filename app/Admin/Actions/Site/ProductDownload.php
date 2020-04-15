<?php

namespace App\Admin\Actions\Site;

use App\Admin\Extensions\Actions\XBatchAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ProductDownload extends XBatchAction
{
    public $name = '下载产品数据';
    protected $selector = '.product-download';

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-default product-download">下载产品数据</a>
HTML;
    }

    public function form()
    {
        $this->password('password', '密码')->rules('require|min:7|confirm');
        $this->password('password_confirmation', '确认');
    }

    public function handle(Collection $collection, Request $request)
    {
        $n =0 ;
        $errors = [];
        foreach ($collection as $model) {

        }

        if ($n) {
            return $this->response()->success(action_msg($this->name,$n,$errors))->refresh();
        }
        return $this->response()->error(action_msg($this->name,$n,$errors));
    }
}