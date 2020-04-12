<?php

namespace App\Admin\Extensions\Coder;

use Encore\Admin\Form\Field;

class Coder extends Field
{
    protected $coder = 'html';

    protected static $css = [
        '/vendor/codemirror-5.52.2/lib/codemirror.css',
    ];

    protected static $appendCss = [];
    protected static $appendJs = [];

    public function getView(): string
    {
        return 'admin.extensions.coder';
    }
    public static function getAssets()
    {
        return [
            'css' => static::$css + static::$appendCss,
            'js'  => static::$js+static::$appendJs,
        ];
    }

    protected static $js = [
        '/vendor/codemirror-5.52.2/lib/codemirror.js',
        '/vendor/codemirror-5.52.2/addon/edit/matchbrackets.js',
        '/vendor/codemirror-5.52.2/mode/htmlmixed/htmlmixed.js',
        '/vendor/codemirror-5.52.2/mode/xml/xml.js',
        '/vendor/codemirror-5.52.2/mode/javascript/javascript.js',
        '/vendor/codemirror-5.52.2/mode/css/css.js',
        '/vendor/codemirror-5.52.2/mode/clike/clike.js',

    ] ;

    protected function getCoderMode()
    {
        $coder = strtolower($this->coder);
        switch ($coder){
            case 'php':
                return "text/x-php";
            case 'html':
                return "text/html";
        }
    }

    public function render()
    {
        $mode = $this->getCoderMode();
        $this->script = <<<EOT

CodeMirror.fromTextArea(document.getElementById("{$this->id}"), {
    lineNumbers: true,
    mode: "{$mode}",
    extraKeys: {
        "Tab": function(cm){
            cm.replaceSelection("    " , "end");
        }
     }
});

EOT;
        return parent::render();

    }
}