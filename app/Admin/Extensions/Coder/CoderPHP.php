<?php


namespace App\Admin\Extensions\Coder;


class CoderPHP extends Coder
{
    protected $coder = 'php';

    protected static $appendJs = [
        '/vendor/codemirror-5.52.2/mode/php/php.js'
    ];


}