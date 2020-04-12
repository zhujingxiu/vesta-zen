<?php


namespace App\Admin\Extensions\Actions;


use Encore\Admin\Actions\RowAction;

class XRowAction extends RowAction
{
    public function render()
    {
        if ($href = $this->href()) {
            return "<a href='{$href}'>{$this->name()}</a>";
        }

        $this->addScript();

        $attributes = $this->formatAttributes();

        return sprintf(
            "<a data-_key='%s' href='javascript:void(0);' class='%s' {$attributes}>%s</a>",
            $this->getKey(),
            $this->getElementClass(),
            $this->asColumn ? $this->display($this->row($this->column->getName())) : $this->name()
        );
    }
}