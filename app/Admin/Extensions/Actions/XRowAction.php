<?php


namespace App\Admin\Extensions\Actions;


use Encore\Admin\Actions\RowAction;

class XRowAction extends RowAction
{
    public function render()
    {
        if ($href = $this->href()) {
            if (is_array($href)){
                $_link = $href['link'];
                $_class= $href['class'] ? 'class="'.$href['class'].'"' :'' ;
                $_target= $href['target'] ? 'target="'.$href['target'].'"' :'' ;
                $_onclick= $href['onclick'] ? 'onclick="'.$href['onclick'].'"' :'' ;
                return sprintf('<a href="%s" %s >%s</a>',
                    $_link,($_class.' '.$_target.' '.$_onclick),$this->name());
            }
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