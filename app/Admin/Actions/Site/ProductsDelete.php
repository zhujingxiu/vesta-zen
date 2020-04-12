<?php

namespace App\Admin\Actions\Site;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

class ProductsDelete extends BatchAction
{
    public $name = '批量删除产品';
    protected $selector = '.products-delete';

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-default products-delete">批量删除产品</a>
HTML;
    }

    public function form()
    {
        $this->checkbox('type', '类型')->options([]);
        $this->textarea('reason', '原因')->rules('required');
    }

    public function handle(Collection $collection, Request $request)
    {
        // $request ...

        return $this->response()->success('Success message...')->refresh();
    }
}