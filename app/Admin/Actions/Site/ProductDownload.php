<?php

namespace App\Admin\Actions\Site;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ProductDownload extends BatchAction
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
        foreach ($collection as $model) {

        }

        return $this->response()->success('Success message.')->refresh();
    }
}