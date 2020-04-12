<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Grid\Tools\BatchAction;

class SoftDelete extends BatchAction
{
    protected $action;

    public function __construct($action = 0)
    {
        $this->action = $action;
    }

    public function script()
    {
        return <<<EOT

$('{$this->getElementClass()}').on('click', function() {

    $.ajax({
        method: 'post',
        url: '{$this->resource}/softDelete',
        data: {
            _token:LA.token,
            ids: selectedRows(),
            action: {$this->action}
        },
        success: function (res) {
            console.log(res)
            if (res.status == 200) {
                $.pjax.reload('#pjax-container');
                toastr.success(res.msg);
            } else {
                toastr.error(res['msg']);
            }    
        }
    });
});

EOT;

    }
}