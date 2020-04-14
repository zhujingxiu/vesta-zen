<?php

namespace App\Admin\Actions\Server;

use App\Libs\Vesta;
use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;

class BatchPackage extends BatchAction
{
    public $name = '批量扩展Vesta用户参数';

    public function handle(Collection $collection)
    {
        $n = 0;
        $errors = [];
        foreach ($collection as $model) {
            // ...
            $cp = new Vesta($model->ip, $model->user, $model->pass);
            $ret = $cp->changeAdminPackageConfig($model->root, 'admin');
            if (is_array($ret)) {
                $errors[] = sprintf("%s[%s] 扩展失败:%s", $model->name, $model->id, implode("-", $ret));
            } else {
                $n++;
            }
        }
        if ($n) {
            return $this->response()
                ->success(sprintf('操作完成：成功%s,失败信息：%s', $n, implode(";", $errors)))
                ->refresh();
        }
        return $this->response()->error("操作失败：" . implode(";", $errors));
    }

    public function dialog()
    {
        $this->confirm('确定扩展？');
    }
}