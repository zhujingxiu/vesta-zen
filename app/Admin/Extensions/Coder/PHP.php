<?php
namespace App\Admin\Extensions\Coder;

use App\Admin\Extensions\Coder;

class PHP extends Coder
{
    protected $coder = 'php';

    protected static $css = [
        '/vendor/codemirror-5.52.2/lib/codemirror.css',
    ];

    protected static $js = [
        '/vendor/codemirror-5.52.2/lib/codemirror.js',
        '/vendor/codemirror-5.52.2/addon/edit/matchbrackets.js',
        "/vendor/codemirror-5.52.2/mode/htmlmixed/htmlmixed.js",
        "/vendor/codemirror-5.52.2/mode/xml/xml.js",
        "/vendor/codemirror-5.52.2/mode/css/css.js",
        "/vendor/codemirror-5.52.2/mode/javascript/javascript.js",
        "/vendor/codemirror-5.52.2/mode/clike/clike.js",
        "/vendor/codemirror-5.52.2/mode/php/php.js"
    ] ;
}