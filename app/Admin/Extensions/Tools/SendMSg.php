<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Grid\Tools\BatchAction;

class SendMSg extends BatchAction
{


    public function script()
    {
        return <<<EOT
$('{$this->getElementClass()}').on('click', function() {
    
    $.ajax({
        method: 'post',
        url: '/admin/order/SendMsgData',
        async: false, 
        data: {
            _token:LA.token,
            ids: selectedRows(),
        },
        success: function (result) {
            if(result.code == 200){
                return 1;
            }
        }    
    });
});

EOT;

    }
}