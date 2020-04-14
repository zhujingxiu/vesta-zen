<?php

namespace App\Admin\Extensions;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Form\Field\Select;
use Illuminate\Support\Str;

class LoadSelect extends Select
{

    protected $view = 'admin.extensions.load-select';

    public function load($field, $sourceUrl, $idField = 'id', $textField = 'text', bool $allowClear = true)
    {
        if (Str::contains($field, '.')) {
            $field = $this->formatName($field);
            $class = str_replace(['[', ']'], '_', $field);
        } else {
            $class = $field;
        }

        $placeholder = json_encode([
            'id' => '',
            'text' => trans('admin.choose'),
        ]);

        $strAllowClear = var_export($allowClear, true);

        $script = <<<EOT
$(document).off('change', "{$this->getElementClassSelector()}");
$(document).on('change', "{$this->getElementClassSelector()}", function () {
    var target = $(this).closest('.form-group').parent().find(".$class");
    console.log(target,$(this))
    $.get("$sourceUrl",{q : this.value}, function (json) {
        if(json.code!=200){
            toastr.error('获取信息出错');
            return;
        }
        var data = json.data;
        target.find("option").remove();
        $(target).select2({
            placeholder: $placeholder,
            allowClear: $strAllowClear,
            data: $.map(data, function (d) {
                d.id = d.$idField;
                d.text = d.$textField;
                return d;
            })
        }).trigger('change');
    });
});
EOT;

        Admin::script($script);

        return $this;
    }

}
