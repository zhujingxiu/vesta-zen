<?php


namespace App\Repositories;


use App\Models\Domain;
use App\Models\SiteDNSRecord;

class DomainRepository
{
    protected $domain;

    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
    }

    public function getDomain($domain)
    {
        return $this->domain->where('domain', $domain)->first();
    }

    public function getDomainByZoneId($zongId)
    {
        return $this->domain->where('zong_id', $zongId)->first();
    }

    public function getRecordsByDomain($domain)
    {
        return SiteDNSRecord::status(1)->where('domain', $domain)->get();
    }

    public function getRecordsBySiteId($id)
    {
        return SiteDNSRecord::status(1)->where('site_id', $id)->get();
    }
}