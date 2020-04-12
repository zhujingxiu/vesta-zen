<?php


namespace App\Admin\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ToolsController extends Controller
{

    public function apiParseDomain(Request $request)
    {
        $domain = $request->get('domain', '');
        list($level, $top_domain) = parse_domain($domain);
        if (!$level) {
            return msg_error('域名不合法');
        }
        return msg_success('', compact('level', 'top_domain'));
    }

}