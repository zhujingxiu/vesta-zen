<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\Request;

class SelectButton extends AbstractTool
{
    protected $url;
    protected $icon;

    function __construct($url, $icon, $text)
    {
        $this->url = $url;
        $this->icon = $icon;
        $this->text = $text;
    }

    public function render()
    {
        $url = $this->url;
        $icon = $this->icon;
        $text = $this->text;
        return view('admin.extension.select-button', compact('url', 'icon', 'text'));
    }

}