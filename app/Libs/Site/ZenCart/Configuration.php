<?php


namespace App\Libs\Site\ZenCart;


use Illuminate\Support\Facades\Log;

class Configuration extends ZenCart
{
    /**
     * @param $value
     * @param mixed ...$keys
     * @return  bool
     */
    public function config($value, ...$keys)
    {
        try {
            $model = (new \App\Libs\Site\ZenCart\Models\Configuration())->setConnection($this->conn);
            if (is_array($keys) && count($keys) > 1) {
                $model->whereIn('configuration_key', $keys)->update(['configuration_value'=> $value]);
            } else {
                $model->where('configuration_key', is_array($keys) ? current($keys) : $keys)
                    ->update(['configuration_value'=> $value]);
            }
        } catch (\Exception $e) {
            Log::info($this->hash.'zen-config-error:'.$e->getMessage());
            return false;
        }
        return true;
    }

    public static function SiteConfiguration($site_config, $fs_catalog, $domain, $db_name, $db_user, $db_pass,$admin=false)
    {
        $content = file_get_contents($site_config);
        if ($admin){
            $newContent = self::replaceAdminConfiguration($content,$fs_catalog, $domain, $db_name, $db_user, $db_pass);
        }else{
            $newContent = self::replaceConfiguration($content,$fs_catalog, $domain, $db_name, $db_user, $db_pass);
        }
        chmod($site_config, 0755);
        return file_put_contents($site_config, $newContent);
    }

    protected static function replaceConfiguration($content,$domain,$fs_catalog,$db_user,$db_pass,$db_name)
    {
        return preg_replace(
            [
                "/define\('HTTP_SERVER', '.*'\).*;.*/",
                "/define\('HTTPS_SERVER', '.*'\).*;.*/",
                "/define\('DIR_FS_CATALOG', '.*'\).*;.*/",
                "/define\('DB_SERVER_USERNAME', '.*'\).*;.*/",
                "/define\('DB_SERVER_PASSWORD', '.*'\).*;.*/",
                "/define\('DB_DATABASE', '.*'\).*;.*/",
            ],
            [
                sprintf("define('HTTP_SERVER', 'http://%s');", $domain),
                sprintf("define('HTTPS_SERVER', 'https://%s');", $domain),
                sprintf("define('DIR_FS_CATALOG', '%s');", $fs_catalog),
                sprintf("define('DB_SERVER_USERNAME', '%s');", $db_user),
                sprintf("define('DB_SERVER_PASSWORD', '%s');", $db_pass),
                sprintf("define('DB_DATABASE', '%s');", $db_name),
            ],
            $content);
    }

    protected static function replaceAdminConfiguration($content,$domain,$fs_catalog,$db_user,$db_pass,$db_name)
    {
        return preg_replace(
            [
                "/define\('HTTP_SERVER', '.*'\).*;.*/",
                "/define\('HTTP_CATALOG_SERVER', '.*'\).*;.*/",
                "/define\('HTTPS_CATALOG_SERVER', '.*'\).*;.*/",
                "/define\('DIR_FS_CATALOG', '.*'\).*;.*/",
                "/define\('DB_SERVER_USERNAME', '.*'\).*;.*/",
                "/define\('DB_SERVER_PASSWORD', '.*'\).*;.*/",
                "/define\('DB_DATABASE', '.*'\).*;.*/",
            ],
            [
                sprintf("define('HTTP_SERVER', 'http://%s');", $domain),
                sprintf("define('HTTP_CATALOG_SERVER', 'http://%s');", $domain),
                sprintf("define('HTTPS_CATALOG_SERVER', 'https://%s');", $domain),
                sprintf("define('DIR_FS_CATALOG', '%s');", $fs_catalog),
                sprintf("define('DB_SERVER_USERNAME', '%s');", $db_user),
                sprintf("define('DB_SERVER_PASSWORD', '%s');", $db_pass),
                sprintf("define('DB_DATABASE', '%s');", $db_name),
            ],
            $content);
    }
}