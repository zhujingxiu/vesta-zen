<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Grid\Tools\BatchAction;

class Action extends BatchAction
{
    protected $action;

    public function __construct($action = '')
    {
        $this->action = $action;
    }

    public function script()
    {
        return <<<EOT

$('{$this->getElementClass()}').click(function(){
    var id = $.admin.grid.selected();
    console.log(id);
    if (id.length == 0) {
        toastr.error('请先选择一行');
        return;
    } else if (id.length == 1) {
        window.location.href = '{$this->resource}/' + id[0] + '{$this->action}';
    } else {
        toastr.error('该操作只允许选择一行');
        return;
    }
});
EOT;

    }
}