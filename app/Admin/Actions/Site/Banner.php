<?php

namespace App\Admin\Actions\Site;

use App\Admin\Extensions\Actions\XFormBatchAction;
use App\Libs\Site\Site;
use App\Libs\Site\ZenCart\Models\Banners;
use Carbon\Carbon;
use Encore\Admin\Actions\BatchAction;
use Encore\Admin\Widgets\Form;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Banner extends XFormBatchAction
{
    public $name = '设置首页广告';
    protected $selector = '.site-banner';

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-default site-banner">设置首页广告</a>
HTML;
    }


    public function xForm(Form $form)
    {
        $form->radio('status', '状态')->options([0 => '禁用', 1 => '启用'])
            ->help('广告状态将按生效日期和显示更新')
            ->default(1);
        $form->radio('banners_open_new_windows', '新窗口打开')->options([0 => '否', 1 => '是'])
            ->help('广告将在新窗口打开')
            ->default(1);
        $form->radio('banners_on_ssl', '带SSL')->options([0 => '禁用', 1 => '启用'])
            ->help('广告可以无误地显示在安全页面')
            ->default(1);
        $form->text('banners_title', '标题');
        $form->text('banners_url', 'URL');
        $form->select('banners_group', '组别')->options([
            'BannersAll' => 'BannersAll',
            'SideBox-Banners' => 'SideBox-Banners',
            'Wide-Banners' => 'Wide-Banners',
        ]);
        $form->text('banners_new_group', '其他新组别')->help('新组别会覆盖已选中的广告组别');
        $form->image('banners_image', '图片');
        $form->text('banners_image_local', '其他本地图片')->help('服务器上已存在的网站目录/images下的图片地址');
        $form->text('banners_image_target', '图片保存路径')->help('默认保存在网站目录的/images下');

        $form->coderPHP('banners_html_text', 'HTML文本');
        $form->number('banners_sort_order', '排序值')
            ->help('banners_box_all边框按照设定的顺序显示广告')
            ->default(0);
        $form->date('date_scheduled', '生效日')->help('1.如果设定了生效日期, 该广告将在相应日期激活<br>2.所有广告在生效日期前标记为[等待中], 到期后将标记为[使用中]');
        $form->date('expires_date', '有效期')->help('1.只有发送两个字段中的一个<br>2.如果广告不是自动失效, 那么不要添这些字段');
        $form->number('expires_impressions', '展示次数')->help('有效期和展示次数只设置一个，达到展示次数即不再展示');
        $form->html(<<<NOTE
<ol>
    <li>广告不能同时使用图像和HTML文本.</li>
    <li>HTML文本优先于图像</li>
    <li>HTML文本不记录点击，只记录显示次数</li>
    <li>不要在安全页面上显示绝对路径图像</li>
</ol>
NOTE
            , '广告注释说明');
        $form->html(<<<NOTE
<ol>
    <li>上传目录必须要有适当用户权限(可写)设置!</li>
    <li>如果您没有上传图像到服务器, 不要填写 '保存到' 字段 (例如, 您使用本地 (服务器端) 图像).</li>
    <li>该 '保存到' 字段必须是一个以/结尾的已有目录 (如, banners/)</li>
</ol>
NOTE
            , '图像注释说明');
        return $form;
    }

    protected function validateRequest($data)
    {
        if (!isset($data['banners_title'])) {
            return msg_error('广告标题不能为空');
        }
        if (!empty($data['expires_date']) && isset($data['expires_impressions'])) {
            return msg_error('广告有效期和展示次数不能同时使用');
        }
        if (isset($data['image_path']) && isset($data['banners_html_text'])) {
            return msg_error('广告不能同时使用图像和HTML文本');
        }

        if (!isset($data['image_path']) && !empty($data['banners_image_target'])) {
            return msg_error('请确保上传了图片');
        }

        return msg_success();
    }

    protected function validateImage(UploadedFile $file)
    {
        if (empty($file) || !$file->isValid()) {
            return msg_error('文件无效，请重新选择文件');
        }
        $size = $file->getClientSize();
        if ($size > 8 * 1024 * 1024) {
            return msg_error('图片文件最大支持8M文件大小');
        }
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, ['jpeg', 'jpg', 'png', 'gif'])) {
            return msg_error('支持文件格式为jpg,png,gif');
        }
        return msg_success('');
    }

    public function handle(Collection $collection, Request $request)
    {
        $data = $request->all();
        $image = null;
        if ($request->hasFile('banners_image')) {
            $image = $request->file('banners_image');
            $ret = $this->validateImage($image);
            if ($ret['code'] != 200) {
                return $this->response()->error($ret['msg']);
            }
            $data['image_path'] = $image->getRealPath();
            if (empty($data['banners_image_target'])) {
                $data['banners_image_target'] = 'images';
            }
            $data['banners_image_target'] = trim_all($data['banners_image_target']);
        }
        $ret = $this->validateRequest($data);
        if ($ret['code'] != 200) {
            return $this->response()->error($ret['msg']);
        }
        $n = 0;
        $errors = [];
        foreach ($collection as $model) {
            $image_path = '';
            $server = $model->server;
            $config = $model->config;
            // 1.发送图片到服务器
            if ($image) {
                $ret = $this->sendBanner($server->ip, $server->user, $server->pass,
                    $config->fs_catalog, $data['banners_image_target'], $image);
                if ($ret['code'] != 200) {
                    $errors[] = sprintf('[#%s]%s:%s', $model->id, $model->doamin, $ret['msg']);
                    continue;
                }
                $image_path = $ret['data']['path'];
            }
            // 2.添加到数据表banners
            $tmp = [
                'banners_title' => $data['banners_title'],
                'banners_url' => $data['banners_url'],
                'banners_image' => $image_path,
                'banners_group' => $data['banners_new_group'] ?? $data['banners_group'],
                'banners_html_text' => trim($data['banners_html_text']),
                'expires_impressions' => $data['expires_impressions'],
                'expires_date' => $data['expires_date'],
                'date_scheduled' => $data['date_scheduled'],
                'banners_open_new_windows' => $data['banners_open_new_windows'],
                'banners_on_ssl' => $data['banners_on_ssl'],
                'banners_sort_order' => $data['banners_sort_order'],
                'status' => (int)$data['status'],
                'date_added' => now(),
            ];
            $banners = new Banners($server->ip, $config->db_user, $config->db_pass, $config->db_name, $tmp);
            if ($banners->save()) {
                $n++;
            }
        }
        if ($n) {
            return $this->response()->success(action_msg($this->name, $n, $errors))->refresh();
        }
        return $this->response()->error(action_msg($this->name, $n, $errors));
    }

    /**
     * @param $server_ip
     * @param $server_user
     * @param $server_pass
     * @param $site_folder
     * @param $dest_dir
     * @param $image
     * @return array
     */
    protected function sendBanner($server_ip, $server_user, $server_pass, $site_folder, $dest_dir, UploadedFile $image)
    {
        // 替换后台文件
        $local_image = $image->getRealPath();
        $remote_file = sprintf("%s/%s/%s.%s", rtrim($site_folder, '/'), trim($dest_dir, '/'),
            md5($server_ip . microtime() . str_random(6)), $image->getClientOriginalExtension());
        $ret = ssh_send_file($server_ip, $server_user, $server_pass,$local_image, $remote_file,log_hash(__METHOD__));
        if ($ret['code'] != 200) {
            return $ret;
        }
        return msg_success('上传成功', ['path' => $remote_file]);
    }
}