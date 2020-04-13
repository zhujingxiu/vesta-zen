<?php


namespace App\Admin\Extensions\Coder;


use App\Admin\Extensions\Coder;

class HTML extends Coder
{
    protected $coder = 'html';
    protected static $js = [
        '/vendor/codemirror-5.52.2/lib/codemirror.js',
        '/vendor/codemirror-5.52.2/addon/edit/matchbrackets.js',
        '/vendor/codemirror-5.52.2/addon/comment/continuecomment.js',
        '/vendor/codemirror-5.52.2/addon/comment/comment.js',
        "/vendor/codemirror-5.52.2/mode/javascript/javascript.js"
    ];
    protected static $css = [
        '/vendor/codemirror-5.52.2/lib/codemirror.css',
        "/vendor/codemirror-5.52.2/addon/hint/show-hint.css",
    ];

}