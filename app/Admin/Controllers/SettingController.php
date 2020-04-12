<?php

namespace App\Admin\Controllers;

use App\Admin\Forms\Settings;
use App\Http\Controllers\Controller;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Tab;

class SettingController extends Controller
{

    public function index(Content $content)
    {
        $forms = [
            'basic' => Settings\Basic::class,
            'site' => Settings\Site::class,
            'upload' => Settings\Uploads::class,
        ];

        return $content
            ->title('系统设置')
            ->body(Tab::forms($forms));
    }


}
