<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Grid;
use Encore\Admin\Grid\Tools\AbstractTool;

class AddButton extends AbstractTool
{

    protected $label;

    protected $method;

    protected $target;

    public function __construct($label, $method, $target = '_self')
    {
        $this->label = $label;
        $this->method = $method;
        $this->target = $target;
    }


    /**
     * Render CreateButton.
     *
     * @return string
     */
    public function render()
    {
        //$new = trans('admin.new');
        $new = $this->label;

        return <<<EOT

<div class="btn-group pull-right" style="margin-right: 10px">
    <a target="{$this->target}" href="{$this->grid->resource()}/{$this->method}" class="btn btn-sm btn-success">
        <i class="fa fa-save"></i>&nbsp;&nbsp;{$new}
    </a>
</div>

EOT;
    }
}
