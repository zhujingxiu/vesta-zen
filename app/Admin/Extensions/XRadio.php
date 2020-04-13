<?php

namespace App\Admin\Extensions;

use Encore\Admin\Form\Field\Radio;

class XRadio extends Radio
{

    public function getView(): string
    {
        return 'admin.extensions.xradio';
    }


}
