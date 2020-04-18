<?php


namespace App\Libs\Site\ZenCart;

use App\Libs\Site\ZenCart\Models\Configuration;
use App\Libs\Site\ZenCart\Struct\DBTables;
use App\Libs\Site\ZenCart\Struct\FileName;
use App\Libs\Site\ZenCart\Struct\FilePath;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ZenCart implements DBTables,FileName,FilePath
{
    const PASSWORD_BCRYPT = 1;
    const PASSWORD_DEFAULT = self::PASSWORD_BCRYPT;

    protected $db = null;
    protected $hash = null;
    protected $conn = null;

    public function __construct($host, $db_user, $db_pass, $db_name,$hash=null)
    {
        $this->hash = $hash ?? log_hash(__CLASS__);
        try {
            $connection = new_db_connection($host, $db_user, $db_pass, $db_name);
            $this->conn = $connection;
            $this->db = DB::connection($connection);

        } catch (\Exception $e) {

//            Log::info($this->hash.'zen-cart-error:'.var_export($e->getTrace(),true));
//            Log::info($this->hash.'zen-cart-databases:'.var_export(app()['config']['database'],true));
        }
    }

    public static function zen_rand($min = null, $max = null)
    {
        static $seeded;

        if (!$seeded) {
            mt_srand((double)microtime() * 1000000);
            $seeded = true;
        }

        if (isset($min) && isset($max)) {
            if ($min >= $max) {
                return $min;
            } else {
                return mt_rand($min, $max);
            }
        } else {
            return mt_rand();
        }
    }

    public static function zen_encrypt_password_new($plain)
    {
        $password = '';
        for ($i = 0; $i < 40; $i++) {
            $password .= self::zen_rand();
        }
        $salt = hash('sha256', $password);
        $password = hash('sha256', $salt . $plain) . ':' . $salt;
        return $password;
    }

    public static function password_hash($password, $algo = null)
    {
        return self::zen_encrypt_password_new($password);
    }

    public function clearModules(...$modules)
    {
        $query = [];
        foreach ($modules as $module){
            $query = array_merge($query,$this->getModuleTruncateQuery($module));
        }
        $n = 0;
        if ($query){
            try {
                $this->db->statement('SET FOREIGN_KEY_CHECKS=0;');
                $this->db->beginTraction();
                foreach (array_filter($query) as $sql) {
                    $this->db->select($sql);
                    $n++;
                }
                $this->db->commit();
                $this->db->statement('SET FOREIGN_KEY_CHECKS=1;');
            } catch (\Exception $e) {
                $this->db->rollback();
                return $e->getMessage();
            }
        }

        return $n;
    }

    protected function getModuleTruncateQuery($module)
    {
        $query = [];
        switch (strtolower($module)){
            case 'category':
                # 清空商品分类、商品、属性
                $query[] =  ['TRUNCATE TABLE '.self::TABLE_CATEGORIES];
                $query[] =  ['TRUNCATE TABLE '.self::TABLE_CATEGORIES_DESCRIPTION];
                break;
            case 'manufacturer':
                # 清空厂家及资料
            $query[] =  ['TRUNCATE TABLE '.self::TABLE_MANUFACTURERS];
                $query[] =  ['TRUNCATE TABLE '.self::TABLE_MANUFACTURERS_INFO];
                break;
            case 'group_pricing':
                # 清空团体价格
                $query[] =  ['TRUNCATE TABLE '.self::TABLE_GROUP_PRICING];
                break;
            case 'review':
                # 清空客户评论
                $query[] =  ['TRUNCATE TABLE '.self::TABLE_REVIEWS];
                $query[] =  ['TRUNCATE TABLE '.self::TABLE_REVIEWS_DESCRIPTION];
                break;
            case 'product':
                # 清空商品以及属性
                $query[] =  ['TRUNCATE TABLE '.self::TABLE_PRODUCT_TYPES_TO_CATEGORY];
                $query[] =  ['TRUNCATE TABLE '.self::TABLE_PRODUCTS];
                $query[] =  ['TRUNCATE TABLE '.self::TABLE_PRODUCTS_ATTRIBUTES];
                $query[] =  ['TRUNCATE TABLE '.self::TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD];
                $query[] =  ['TRUNCATE TABLE '.self::TABLE_PRODUCTS_DESCRIPTION];
                $query[] =  ['TRUNCATE TABLE '.self::TABLE_PRODUCTS_DISCOUNT_QUANTITY];
                $query[] =  ['TRUNCATE TABLE '.self::TABLE_PRODUCTS_NOTIFICATIONS];
                $query[] =  ['TRUNCATE TABLE '.self::TABLE_PRODUCTS_OPTIONS];
                $query[] =  ['TRUNCATE TABLE '.self::TABLE_PRODUCTS_OPTIONS_TYPES];
                $query[] =  ['TRUNCATE TABLE '.self::TABLE_PRODUCTS_OPTIONS_VALUES];
                $query[] =  ['TRUNCATE TABLE '.self::TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS];
                $query[] =  ['TRUNCATE TABLE '.self::TABLE_PRODUCTS_TO_CATEGORIES];
                break;
            case 'featured':
                # 清空推荐商品
                $query[] =  ['TRUNCATE TABLE '.self::TABLE_FEATURED];
                break;
            case 'salemaker':
                # 清空促销商品
                $query[] =  ['TRUNCATE TABLE '.self::TABLE_SALEMAKER_SALES];
                break;
            case 'special':
                # 清空特价商品
                $query[] =  ['TRUNCATE TABLE '.self::TABLE_SPECIALS];
                break;
            case 'tax':
                # 清空特价商品
                $query[] =  ['TRUNCATE TABLE '.self::TABLE_TAX_CLASS];
                $query[] =  ['TRUNCATE TABLE '.self::TABLE_TAX_RATES];
                break;
            case 'banner':
                # 清空特价商品
                $query[] =  ['TRUNCATE TABLE '.self::TABLE_BANNERS];
                $query[] =  ['TRUNCATE TABLE '.self::TABLE_BANNERS_HISTORY];
                break;
            default:
                $table = trim_all($module);
                $query[] =  ['TRUNCATE TABLE '.$table];
                break;
        }
        return $query;
    }

    public static function AdminDir()
    {
        return cache_setting('dir_admin') ?? 'xadmin';
    }

    public static function PreviewImg()
    {
        return cache_setting('tpl_preview') ?? 'scr_template_default.jpg';
    }

    public static function DBFile()
    {
        return cache_setting('db_file') ?? 'zencart.sql';
    }

    public static function DBUser()
    {
        return cache_setting('db_user') ?? 'admin_myZenCart';
    }

    public static function DBPass()
    {
        return cache_setting('db_pass') ?? 'tgJoyl5C9UgvZ';
    }

    /**
     * 站点目录-绝对路径
     * @param $folder
     * @param bool $local
     * @return string
     */
    public static function SiteFolder($folder,$local=false)
    {
        $folder = rtrim($folder,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        return $local ? storage_path($folder) : $folder;
    }

    /**
     * 站点后台目录
     * @param $folder
     * @param $admin_dir
     * @return string
     */
    public static function SiteAdminFolder($folder,$admin_dir)
    {
        return $folder.$admin_dir;
    }

    /**
     * 站点语言目录
     * @param $folder
     * @param $lang_dir
     * @return string
     */
    public static function SiteLangFolder($folder,$lang_dir)
    {
        return $folder.self::DIR_WS_LANGUAGES.$lang_dir;
    }

    /**
     * 站点数据库文件
     * @param $folder
     * @param $db_file
     * @return string
     */
    public static function SiteDBFile($folder,$db_file)
    {
        return $folder.$db_file;
    }
    public static function SiteConfigFile($folder)
    {
        return $folder.self::DIR_WS_INCLUDES.self::FILENAME_CONFIGURATION;
    }
    public static function SiteAdminConfigFile($folder,$admin_dir)
    {
        return self::SiteAdminFolder($folder,$admin_dir).self::DIR_WS_INCLUDES.self::FILENAME_CONFIGURATION;
    }
    public static function checkSiteFolder($folder)
    {
        return is_dir(self::SiteFolder($folder));
    }
    public static function checkSiteAdminFolder($folder,$admin_dir)
    {
        return is_dir(self::SiteAdminFolder($folder,$admin_dir));
    }
    public static function checkSiteLangFolder($folder,$lang_dir)
    {
        return is_dir(self::SiteLangFolder($folder,$lang_dir));
    }

    public static function checkSiteDBFile($folder,$db_file)
    {
        return file_exists(self::SiteDBFile($folder,$db_file));
    }
}