<?php


namespace App\Libs\Site\ZenCart;


use App\Libs\Site\ZenCart\Models\Configuration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ZenCart
{
    const DB_PREFIX = '';
    const TABLE_ADDRESS_BOOK = self::DB_PREFIX . 'address_book';
    const TABLE_ADDRESS_FORMAT = self::DB_PREFIX . 'address_format';
    const TABLE_ADMIN = self::DB_PREFIX . 'admin';
    const TABLE_ADMIN_NOTIFICATIONS = self::DB_PREFIX . 'admin_notifications';
    const TABLE_ADMIN_ACTIVITY_LOG = self::DB_PREFIX . 'admin_activity_log';
    const TABLE_ADMIN_MENUS = self::DB_PREFIX . 'admin_menus';
    const TABLE_ADMIN_PAGES = self::DB_PREFIX . 'admin_pages';
    const TABLE_ADMIN_PAGES_TO_PROFILES = self::DB_PREFIX . 'admin_pages_to_profiles';
    const TABLE_ADMIN_PROFILES = self::DB_PREFIX . 'admin_profiles';
    const TABLE_AUTHORIZENET = self::DB_PREFIX . 'authorizenet';
    const TABLE_BANNERS = self::DB_PREFIX . 'banners';
    const TABLE_BANNERS_HISTORY = self::DB_PREFIX . 'banners_history';
    const TABLE_CATEGORIES = self::DB_PREFIX . 'categories';
    const TABLE_CATEGORIES_DESCRIPTION = self::DB_PREFIX . 'categories_description';
    const TABLE_CONFIGURATION = self::DB_PREFIX . 'configuration';
    const TABLE_CONFIGURATION_GROUP = self::DB_PREFIX . 'configuration_group';
    const TABLE_COUNTER = self::DB_PREFIX . 'counter';
    const TABLE_COUNTER_HISTORY = self::DB_PREFIX . 'counter_history';
    const TABLE_COUNTRIES = self::DB_PREFIX . 'countries';
    const TABLE_COUPON_GV_QUEUE = self::DB_PREFIX . 'coupon_gv_queue';
    const TABLE_COUPON_GV_CUSTOMER = self::DB_PREFIX . 'coupon_gv_customer';
    const TABLE_COUPON_EMAIL_TRACK = self::DB_PREFIX . 'coupon_email_track';
    const TABLE_COUPON_REDEEM_TRACK = self::DB_PREFIX . 'coupon_redeem_track';
    const TABLE_COUPON_RESTRICT = self::DB_PREFIX . 'coupon_restrict';
    const TABLE_COUPONS = self::DB_PREFIX . 'coupons';
    const TABLE_COUPONS_DESCRIPTION = self::DB_PREFIX . 'coupons_description';
    const TABLE_CURRENCIES = self::DB_PREFIX . 'currencies';
    const TABLE_CUSTOMERS = self::DB_PREFIX . 'customers';
    const TABLE_CUSTOMERS_BASKET = self::DB_PREFIX . 'customers_basket';
    const TABLE_CUSTOMERS_BASKET_ATTRIBUTES = self::DB_PREFIX . 'customers_basket_attributes';
    const TABLE_CUSTOMERS_INFO = self::DB_PREFIX . 'customers_info';
    const TABLE_DB_CACHE = self::DB_PREFIX . 'db_cache';
    const TABLE_EMAIL_ARCHIVE = self::DB_PREFIX . 'email_archive';
    const TABLE_EZPAGES = self::DB_PREFIX . 'ezpages';
    const TABLE_EZPAGES_CONTENT = self::DB_PREFIX . 'ezpages_content';
    const TABLE_FEATURED = self::DB_PREFIX . 'featured';
    const TABLE_FILES_UPLOADED = self::DB_PREFIX . 'files_uploaded';
    const TABLE_GROUP_PRICING = self::DB_PREFIX . 'group_pricing';
    const TABLE_GET_TERMS_TO_FILTER = self::DB_PREFIX . 'get_terms_to_filter';
    const TABLE_LANGUAGES = self::DB_PREFIX . 'languages';
    const TABLE_LAYOUT_BOXES = self::DB_PREFIX . 'layout_boxes';
    const TABLE_MANUFACTURERS = self::DB_PREFIX . 'manufacturers';
    const TABLE_MANUFACTURERS_INFO = self::DB_PREFIX . 'manufacturers_info';
    const TABLE_META_TAGS_PRODUCTS_DESCRIPTION = self::DB_PREFIX . 'meta_tags_products_description';
    const TABLE_METATAGS_CATEGORIES_DESCRIPTION = self::DB_PREFIX . 'meta_tags_categories_description';
    const TABLE_NEWSLETTERS = self::DB_PREFIX . 'newsletters';
    const TABLE_ORDERS = self::DB_PREFIX . 'orders';
    const TABLE_ORDERS_PRODUCTS = self::DB_PREFIX . 'orders_products';
    const TABLE_ORDERS_PRODUCTS_ATTRIBUTES = self::DB_PREFIX . 'orders_products_attributes';
    const TABLE_ORDERS_PRODUCTS_DOWNLOAD = self::DB_PREFIX . 'orders_products_download';
    const TABLE_ORDERS_STATUS = self::DB_PREFIX . 'orders_status';
    const TABLE_ORDERS_STATUS_HISTORY = self::DB_PREFIX . 'orders_status_history';
    const TABLE_ORDERS_TYPE = self::DB_PREFIX . 'orders_type';
    const TABLE_ORDERS_TOTAL = self::DB_PREFIX . 'orders_total';
    const TABLE_PAYPAL = self::DB_PREFIX . 'paypal';
    const TABLE_PAYPAL_SESSION = self::DB_PREFIX . 'paypal_session';
    const TABLE_PAYPAL_PAYMENT_STATUS = self::DB_PREFIX . 'paypal_payment_status';
    const TABLE_PAYPAL_PAYMENT_STATUS_HISTORY = self::DB_PREFIX . 'paypal_payment_status_history';
    const TABLE_PRODUCTS = self::DB_PREFIX . 'products';
    const TABLE_PRODUCT_TYPES = self::DB_PREFIX . 'product_types';
    const TABLE_PRODUCT_TYPE_LAYOUT = self::DB_PREFIX . 'product_type_layout';
    const TABLE_PRODUCT_TYPES_TO_CATEGORY = self::DB_PREFIX . 'product_types_to_category';
    const TABLE_PRODUCTS_ATTRIBUTES = self::DB_PREFIX . 'products_attributes';
    const TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD = self::DB_PREFIX . 'products_attributes_download';
    const TABLE_PRODUCTS_DESCRIPTION = self::DB_PREFIX . 'products_description';
    const TABLE_PRODUCTS_DISCOUNT_QUANTITY = self::DB_PREFIX . 'products_discount_quantity';
    const TABLE_PRODUCTS_NOTIFICATIONS = self::DB_PREFIX . 'products_notifications';
    const TABLE_PRODUCTS_OPTIONS = self::DB_PREFIX . 'products_options';
    const TABLE_PRODUCTS_OPTIONS_VALUES = self::DB_PREFIX . 'products_options_values';
    const TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS = self::DB_PREFIX . 'products_options_values_to_products_options';
    const TABLE_PRODUCTS_OPTIONS_TYPES = self::DB_PREFIX . 'products_options_types';
    const TABLE_PRODUCTS_TO_CATEGORIES = self::DB_PREFIX . 'products_to_categories';
    const TABLE_PROJECT_VERSION = self::DB_PREFIX . 'project_version';
    const TABLE_PROJECT_VERSION_HISTORY = self::DB_PREFIX . 'project_version_history';
    const TABLE_QUERY_BUILDER = self::DB_PREFIX . 'query_builder';
    const TABLE_REVIEWS = self::DB_PREFIX . 'reviews';
    const TABLE_REVIEWS_DESCRIPTION = self::DB_PREFIX . 'reviews_description';
    const TABLE_SALEMAKER_SALES = self::DB_PREFIX . 'salemaker_sales';
    const TABLE_SESSIONS = self::DB_PREFIX . 'sessions';
    const TABLE_SPECIALS = self::DB_PREFIX . 'specials';
    const TABLE_TEMPLATE_SELECT = self::DB_PREFIX . 'template_select';
    const TABLE_TAX_CLASS = self::DB_PREFIX . 'tax_class';
    const TABLE_TAX_RATES = self::DB_PREFIX . 'tax_rates';
    const TABLE_GEO_ZONES = self::DB_PREFIX . 'geo_zones';
    const TABLE_ZONES_TO_GEO_ZONES = self::DB_PREFIX . 'zones_to_geo_zones';
    const TABLE_UPGRADE_EXCEPTIONS = self::DB_PREFIX . 'upgrade_exceptions';
    const TABLE_WISHLIST = self::DB_PREFIX . 'customers_wishlist';
    const TABLE_WHOS_ONLINE = self::DB_PREFIX . 'whos_online';
    const TABLE_ZONES = self::DB_PREFIX . 'zones';

    const PASSWORD_BCRYPT = 1;
    const PASSWORD_DEFAULT = self::PASSWORD_BCRYPT;

    protected $db = null;
    protected $conn = null;

    public function __construct($host, $db_user, $db_pass, $db_name)
    {
        try {
            $connection = new_db_connection($host, $db_user, $db_pass, $db_name);
            $this->conn = $connection;
            $this->db = DB::connection($connection);
        } catch (\Exception $e) {
            $hash = str_random(16).'==';
            Log::info($hash.'zen-cart-error:'.var_export($e->getTrace(),true));
            Log::info($hash.'zen-cart-databases:'.var_export(app()['config']['database'],true));
        }
    }


    public function getTaxClassByTitle($tax_class_title)
    {
        return $this->db->table(self::TABLE_TAX_CLASS)->where('tax_class_title',$tax_class_title)->first();
    }

    public function getManufacturerByTitle($manufacturers_name)
    {
        return $this->db->table(self::TABLE_MANUFACTURERS)->where('manufacturers_name',$manufacturers_name)->first();
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

    /**
     * @param $value
     * @param mixed ...$keys
     * @return  bool
     */
    public function config($value, ...$keys)
    {
        Log::info('zencart-config:'.var_export($keys,true));
        try {
            $model = (new Configuration())->setConnection($this->conn);
            if (is_array($keys) && count($keys) > 1) {
                $model->whereIn('configuration_key', $keys)->update(['configuration_value'=> $value]);
            } else {
                $model->where('configuration_key', is_array($keys) ? current($keys) : $keys)
                    ->update(['configuration_value'=> $value]);
            }
        } catch (\Exception $e) {
            Log::info('zen-config-error:'.$e->getMessage());
            return false;
        }
        return true;
    }
}