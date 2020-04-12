<?php


namespace App\Admin\Controllers;

use App\Models\CloudFlare;
use App\Models\SiteLanguage;
use App\Models\SiteTemplate;
use App\Repositories\SiteTemplateRepository;
use Illuminate\Http\Request;

class CloudFlareController extends BaseController
{
    protected $header = "CloudFlare";
    protected $model = CloudFlare::class;
    protected $_url = '/admin/cloud-flare';

    protected $repository;

    public function __construct(SiteTemplateRepository $siteTemplateRepository)
    {
        parent::__construct();
        $this->repository = $siteTemplateRepository;
    }

    protected function _model_init()
    {
        parent::_model_init();
        $this->field_config['after']['auth_key'] = 'columnDomains';
    }

    public static function columnDomains($grid)
    {
        $grid->column('domains', '分管域名')->display(function () {
            $data = [];

            foreach ($this->domains as $v) {
                $data[] = $v->domain;
            }
            return implode('<br>', $data);
        });
    }

    public function store(Request $request)
    {
        //dd($request->all());
        return $this->field_create()->store();
    }

    public function update($id)
    {
        return $this->field_edit($id)->update($id);
    }
}