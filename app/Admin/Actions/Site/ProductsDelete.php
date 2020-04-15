<?php

namespace App\Admin\Actions\Site;

use App\Admin\Extensions\Actions\XFormBatchAction;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

class ProductsDelete extends XFormBatchAction
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
        $n = 0;
        $errors = [];

        if ($n) {
            return $this->response()->success(action_msg($this->name,$n,$errors))->refresh();
        }
        return $this->response()->error(action_msg($this->name,$n,$errors));
    }
}