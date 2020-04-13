<?php

namespace App\Admin\Extensions;

use Encore\Admin\Form\Field;

class Coder extends Field
{
    public $is_action = false;
    protected $coder = 'php' ;
    public function __construct($column = '', $arguments = [])
    {
        //self::$js = array_merge(self::$js,$this->appendJs());
        //self::$css = array_merge(self::$js,$this->appendCss());
        parent::__construct($column,$arguments);
    }


    public function getView(): string
    {
        return $this->is_action ? 'admin.extensions.coder_action' : 'admin.extensions.coder';
    }
    protected static $css = [
        '/vendor/codemirror-5.52.2/lib/codemirror.css',
    ];

    protected static $js = [
        '/vendor/codemirror-5.52.2/lib/codemirror.js',
    ] ;

    protected function appendCss()
    {
        $coder = strtolower($this->coder);
        switch ($coder){
            case 'go':
                return [
                    '/vendor/codemirror-5.52.2/theme/elegant.css'
                ];
            case 'css':
            case 'html':
                return [
                    "/vendor/codemirror-5.52.2/addon/hint/show-hint.css",
                ];
            default:
                return [];
        }
    }
    protected function appendJs()
    {
        $coder = strtolower($this->coder);
        switch ($coder){
            case 'javascript':
                return [
                    '/vendor/codemirror-5.52.2/addon/edit/matchbrackets.js',
                    '/vendor/codemirror-5.52.2/addon/comment/continuecomment.js',
                    '/vendor/codemirror-5.52.2/addon/comment/comment.js',
                    "/vendor/codemirror-5.52.2/mode/javascript/javascript.js"
                ];
            case 'css':
                return [
                    "/vendor/codemirror-5.52.2/addon/hint/show-hint.js",
                    "/vendor/codemirror-5.52.2/addon/hint/css-hint.js",
                    "/vendor/codemirror-5.52.2/mode/css/css.js"
                ];
            case 'html':
                return [
                    "/vendor/codemirror-5.52.2/addon/hint/css-hint.js",
                    "/vendor/codemirror-5.52.2/addon/selection/selection-pointer.js",
                    "/vendor/codemirror-5.52.2/mode/css/css.js",
                    "/vendor/codemirror-5.52.2/mode/javascript/javascript.js",
                    "/vendor/codemirror-5.52.2/mode/htmlmixed/htmlmixed.js"
                ];
            case 'xml':
                return ["/vendor/codemirror-5.52.2/mode/xml/xml.js"];
            case 'go':
                return [
                    '/vendor/codemirror-5.52.2/addon/edit/matchbrackets.js',
                    "/vendor/codemirror-5.52.2/mode/go/go.js"
                ];
            case 'python':
            case 'python2':
            case 'python3':
                return [
                    '/vendor/codemirror-5.52.2/addon/edit/matchbrackets.js',
                    "/vendor/codemirror-5.52.2/mode/python/python.js"
                ];
            case 'php':
                return [
                    '/vendor/codemirror-5.52.2/addon/edit/matchbrackets.js',
                    "/vendor/codemirror-5.52.2/mode/htmlmixed/htmlmixed.js",
                    "/vendor/codemirror-5.52.2/mode/xml/xml.js",
                    "/vendor/codemirror-5.52.2/mode/css/css.js",
                    "/vendor/codemirror-5.52.2/mode/javascript/javascript.js",
                    "/vendor/codemirror-5.52.2/mode/clike/clike.js",
                    "/vendor/codemirror-5.52.2/mode/php/php.js"
                ];
            default:
                return [];
        }
    }



    protected function getCoderOptions()
    {
        $coder = strtolower($this->coder);
        switch ($coder){
            case 'html':
                return '{
                    mode:{
                        name: "htmlmixed",
                        scriptTypes: [
                            {matches: /\/x-handlebars-template|\/x-mustache/i, mode: null},
                            {matches: /(text|application)\/(x-)?vb(a|script)/i, mode: "vbscript"}
                        ]
                    },
                    selectionPointer: true
                }';
            case 'javascript':
                return '{
                    lineNumbers: true,
                    matchBrackets: true,
                    continueComments: "Enter",
                    extraKeys: {"Ctrl-Q": "toggleComment"}
                }';
            case 'css':
                return '{
                    extraKeys: {"Ctrl-Space": "autocomplete"}
                }';
            case 'xml':
                return '{
                    mode: "text/html",
                    lineNumbers: true
                }';
            case 'php':
                return '{
                    lineNumbers: true,
                    matchBrackets: true,
                    mode: "application/x-httpd-php",
                    indentUnit: 4,
                    indentWithTabs: true
                }';
            case 'go':
                return '{
                    theme: "elegant",
                    matchBrackets: true,
                    indentUnit: 8,
                    tabSize: 8,
                    indentWithTabs: true,
                    mode: "text/x-go"
                  }';
            case 'python2':
                return '{
                    mode: {name: "python",
                           version: 3,
                           singleLineStringErrors: false},
                    lineNumbers: true,
                    indentUnit: 4,
                    matchBrackets: true
                }';
            case 'python':
            case 'python3':
                return '{
                    mode: {name: "text/x-cython",
                           version: 2,
                           singleLineStringErrors: false},
                    lineNumbers: true,
                    indentUnit: 4,
                    matchBrackets: true
                  }';
            default:
                return "";
        }
    }

    public function render()
    {
        $options = $this->getCoderOptions();
        $this->script = <<<EOT
        var options = {$options};
        console.log(options)
        CodeMirror.fromTextArea(document.getElementById("{$this->id}"), options);
EOT;
        return parent::render();

    }
}