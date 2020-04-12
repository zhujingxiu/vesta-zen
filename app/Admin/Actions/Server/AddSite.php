<?php

namespace App\Admin\Actions\Server;

use App\Admin\Extensions\Actions\XBatchAction;
use App\Models\SiteLanguage;
use Illuminate\Http\Request;

class AddSite extends XBatchAction
{
    public $name = "添加站点";
    protected $selector = '.add-site';

    public function actionScript()
    {
        $script = <<<SCRIPT
    var selected = $.admin.grid.selected();
    if (selected.length == 0) {
        return $.admin.swal({type: 'error', title: '请先选择一行!'});
    } else if (selected.length == 1) {
        var server_id = selected[0]
        console.log(server_id)
        $('.modal input[name="server_id"]').val(server_id);
        var server_name = $('#server-entity-'+server_id).data('name'),
        server_ip = $('#server-entity-'+server_id).data('ip'),
        server_user = $('#server-entity-'+server_id).data('user'),
        server_pwd = $('#server-entity-'+server_id).data('pwd');
        var server = server_user+':'+server_pwd+'@'+server_ip+'[#'+server_id+':'+server_name+']';
        $('.modal input[name="server_name"]').val(server);
        $('.modal input[name="server_user"]').val(server_user);
        $('.modal input[name="server_pwd"]').val(server_pwd);
        $('.modal input[name="server_ip"]').val(server_ip);
    } else {
        return $.admin.swal({type: 'error', title: '该操作只允许选择一行!'});
    }
SCRIPT;
        return $script;
    }

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-default add-site">添加站点</a>
HTML;
    }

    public function form()
    {
        $this->hidden('server_id', '服务器ID');
        $this->text('server_name', '服务器')->readonly();
        $this->hidden('server_ip', 'hostIP');
        $this->hidden('server_user', 'hostUser');
        $this->hidden('server_pwd', 'hostPwd');
        $this->text('server_ip', '选择IP')->readonly();
        $this->checkbox('parse_cf', 'DNS解析')->options([1 => "使用CloudFlare自动解析"]);
        $this->text('domain', '域名')->help("可以直接写二级域名");
        $this->radio('level', '域名级别')->options([1 => "一级", 2 => "二级"])->default(1);
        //$this->php('sub_domain', '别名/二级域名')->help('可删空,自动加www')->placeholder("暂时无用");
        $languages = SiteLanguage::status(1)->selectRaw('CONCAT(title," ",code) AS t,id')->pluck('t', 'id');
        $this->loadSelect('lang', '选择语言模板')->options($languages)->load('template', '/api/templates');
        $this->select('template', '选择站点模板');
    }

    // 权限控制
    public function authorize($user, $model)
    {
        return true;
    }

    public function handle(Request $request)
    {
        dd('add-site-handle', $request->all());
    }

}