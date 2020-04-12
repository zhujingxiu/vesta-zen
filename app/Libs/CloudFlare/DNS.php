<?php

namespace App\Libs\CloudFlare;


class DNS extends Base
{
    /**
     * 所有DNS记录
     * @param $zone_id
     * @param string $type
     * @param string $name
     * @param string $content
     * @param int $page
     * @param int $per_page
     * @param string $order
     * @param string $direction
     * @param string $match
     * @return array
     */
    public function records($zone_id, $name = "", $type = "", $content = "", $page = 1, $per_page = 100, $order = "type", $direction = "desc", $match = "all")
    {
        $api_url = sprintf("%s/zones/%s/dns_records", $this->base_url, $zone_id);
        $data = [
            'page' => strval($page),
            'per_page' => strval($per_page),
            'order' => $order,
            'direction' => $direction,
            'match' => $match,
        ];

        if ($type) {
            $data['type'] = $type;
        }
        if ($name) {
            $data['name'] = $name;
        }
        if ($content) {
            $data['content'] = $content;
        };
        return $this->request($api_url . '?' . http_build_query($data), []);
    }

    /**
     * 校验Type
     * @param string $type
     * @return bool
     */
    private function validateRecordType($type = 'A')
    {
        return in_array(strtoupper($type), $this->recordTypes());
    }

    /**
     * 校验域名
     * @param $name
     * @return bool
     */
    private function validateRecordName($name)
    {
        return strlen($name) <= 255;
    }

    /**
     * 校验IP
     * @param $content
     * @return bool
     */
    private function validateRecordContent($content)
    {
        return is_ip($content);
    }

    /**
     * 添加记录
     * @param $zone_id
     * @param string $name
     * @param string $content
     * @param string $type
     * @param int $ttl
     * @param int $priority
     * @param bool $proxied
     * @return array
     */
    public function addRecord($zone_id, $name = "", $content = "", $type = "A", $ttl = 1, $priority = 10, $proxied = false)
    {
        $type = trim_all($type);
        if (!$this->validateRecordType($type)) {
            return $this->error(self::ERR_DNS_TYPE);
        }
        $name = trim_all($name);
        if (!$this->validateRecordName($name)) {
            return $this->error(self::ERR_DNS_CONTENT);
        }
        $content = trim_all($content);
        if (!$this->validateRecordContent($content)) {
            return $this->error(self::ERR_DNS_CONTENT);
        }
        $api_url = sprintf("%s/zones/%s/dns_records", $this->base_url, $zone_id);
        return $this->request($api_url, [
            'type' => $type,
            'name' => $name,
            'content' => $content,
            'ttl' => $ttl,
            'priority' => $priority,
            'proxied' => $proxied,
        ], 'POST');
    }

    /**
     * 更新记录
     * @param $zone_id
     * @param $record_id
     * @param string $name
     * @param string $content
     * @param string $type
     * @param int $ttl
     * @param int $priority
     * @param bool $proxied
     * @return array
     */
    public function updateRecord($zone_id, $record_id, $name = "", $content = "", $type = "A", $ttl = 1, $priority = 10, $proxied = false)
    {
        $type = trim_all($type);
        if (!$this->validateRecordType($type)) {
            return $this->error(self::ERR_DNS_TYPE);
        }
        $name = trim_all($name);
        if (!$this->validateRecordName($name)) {
            return $this->error(self::ERR_DNS_CONTENT);
        }
        $content = trim_all($content);
        if (!$this->validateRecordContent($content)) {
            return $this->error(self::ERR_DNS_CONTENT);
        }
        $api_url = sprintf("%s/zones/%s/dns_records/%s", $this->base_url, $zone_id, $record_id);
        return $this->request($api_url, [
            'type' => $type,
            'name' => $name,
            'content' => $content,
            'ttl' => $ttl,
            'priority' => $priority,
            'proxied' => $proxied,
        ], 'PUT');
    }

    /**
     * 删除记录
     * @param $zone_id
     * @param $record_id
     * @return array
     */
    public function deleteRecord($zone_id, $record_id)
    {
        $api_url = sprintf("%s/zones/%s/dns_records/%s", $this->base_url, $zone_id, $record_id);
        return $this->request($api_url, [], 'DELETE');
    }

    /**
     * 记录详情
     * @param $zone_id
     * @param $record_id
     * @return array
     */
    public function record($zone_id, $record_id)
    {
        $api_url = sprintf("%s/zones/%s/dns_records/%s", $this->base_url, $zone_id, $record_id);
        return $this->request($api_url, []);
    }

    /**
     * 按Name获取记录
     * @param $zone_id
     * @param $name
     * @param $records
     * @return mixed|null
     */
    public function getRecordByName($zone_id, $name, $records = [])
    {
        if (!$records) {
            $result = $this->records($zone_id);
            if ($result['code'] != self::ERR_OK || !$result['data']) {
                return $result;
            }
            $records = $result['data'];
        }
        foreach ($records as $item) {
            if (!isset($item['name']) || trim_all($name) != $item['name']) {
                continue;
            }
            return $this->success($item);
        }
        return $this->error(self::ERR_DNS_RECORD);
    }

    /**
     * @return array
     */
    private function recordTypes()
    {
        return [
            'A',
            'AAAA',
            'CNAME',
            'TXT',
            'SRV',
            'LOC',
            'MX',
            'NS',
            'SPF',
            'CERT',
            'DNSKEY',
            'DS',
            'NAPTR',
            'SMIMEA',
            'SSHFP',
            'TLSA',
            'URI'
        ];
    }
}