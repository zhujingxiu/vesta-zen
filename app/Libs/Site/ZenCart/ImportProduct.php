<?php


namespace App\Libs\Site\ZenCart;


use App\Libs\Site\ZenCart\Models\Manufacturers;
use App\Libs\Site\ZenCart\Models\Products;
use App\Libs\Site\ZenCart\Models\Specials;
use App\Libs\Site\ZenCart\Models\TaxClass;
use Illuminate\Support\Facades\Log;

class ImportProduct extends ZenCart
{
    protected $keys = [
        // table products
        'v_products_model' => "products_model",
        'v_products_image' => "products_image",
        'v_products_price' => "products_price",
        'v_products_weight' => "products_weight",
        'v_date_avail' => "products_date_available",
        'v_date_added' => "products_date_added",
        'v_products_quantity' => "products_quantity",
        'v_status' => "products_status",
        'v_metatags_products_name_status' => "metatags_products_name_status",
        'v_metatags_title_status' => "metatags_title_status",
        'v_metatags_model_status' => "metatags_model_status",
        'v_metatags_price_status' => "metatags_price_status",
        'v_metatags_title_tagline_status' => "metatags_title_tagline_status",
        // table products_description
        'v_products_name' => "products_name",
        'v_products_description' => "products_description",
        'v_products_url' => "products_url",
        // table specials
        'v_specials_price' => "specials_new_products_price",
        'v_specials_date_avail' => "specials_date_available",
        'v_specials_expires_date' => "expires_date",
        // table tax_class
        'v_tax_class_title' => "tax_class_title",
        // table manufacturers
        'v_manufacturers_name' => "manufacturers_name",
        // table categories_description
        'v_categories_name' => "categories_name",
        // table meta_tags_products_description
        'v_metatags_title' => "metatags_title",
        'v_metatags_keywords' => "metatags_keywords",
        'v_metatags_description' => "metatags_description",
        // table product_attr
        'v_attr_name' => "v_attr_name",
        'v_attr_values' => "v_attr_values",
    ];

    protected function keyMap($key)
    {
        return $this->keys[$key] ?? null;
    }

    protected function extractKeyMap($record, ...$keys)
    {
        $data = [];
        foreach ($keys as $key) {
            if (!isset($record[$key]) || !$this->keyMap($key)) {
                continue;
            }
            $data[$this->keyMap($key)] = $record[$key];
        }
        return $data;
    }

    public function storeProducts($records)
    {
        $n = 0;
        $errors = [];
        $rowColumns = false;
        foreach ($records as $row => $record) {
            if (!is_array($record)) {
                continue;
            }
            if (!$rowColumns) {
                $rowColumns = count($record);
            } else if ($rowColumns != count($record) || !isset($record['v_products_model'])) {
                $errors[] = sprintf("第%s条记录格式出错", $row + 1);
                continue;
            }
            $taxClass = $manufacturer = null;
            // tax_class
            if ($record['v_tax_class_title'] && $record['v_tax_class_title'] != '--none--') {
                $taxClass = (new TaxClass())->setConnection($this->conn)
                    ->where($this->extractKeyMap($record,'v_tax_class_title'))->first();
                if (!$taxClass) {
                    $errors[] = sprintf("第%s条记录出错:%s", $row + 1,'v_tax_class_title');
                    continue;
                }
            }
            // manufacturers
            if ($record['v_manufacturers_name']) {
                $manufacturer = (new Manufacturers())->setConnection($this->conn)
                    ->where($this->extractKeyMap($record,'v_manufacturers_name'))->first();
                if (!$manufacturer) {
                    $errors[] = sprintf("第%s条记录出错:%s", $row + 1,'v_manufacturers_name');
                    continue;
                }
            }
            $this->db->beginTransaction();
            try {// products
                $product = $this->store($record,$taxClass,$manufacturer);
                if ($product->products_id){
                    $n++;
                }
                $this->db->commit();
            } catch (\Exception $e) {
                $this->db->rollBack();
                $errors[] = sprintf("第%s条记录插入数据表出错", $e->getMessage());
                continue;
            }

        }

        return $n   ? msg_success(sprintf('插入成功记录:%s条,失败信息:%s',$n,implode(",",$errors)),['success'=>$n,'errors'=>$errors])
                    : msg_error(implode(',',$errors));
    }

    protected function store($record,$tax,$manufacturer)
    {
        $modelProduct = (new Products())->setConnection($this->conn);
        $product = $modelProduct->where($this->keyMap('v_products_model'), $record['v_products_model'])->first();
        if (!$product) {
            $tmp = $this->extractKeyMap($record, 'v_products_model',
                'v_products_image',
                'v_products_price',
                'v_products_weight',
                'v_date_avail',
                'v_date_added',
                'v_products_quantity',
                'v_metatags_products_name_status',
                'v_metatags_title_status',
                'v_metatags_model_status',
                'v_metatags_price_status',
                'v_metatags_title_tagline_status');
            $product = app(Products::class, ['attributes' => $tmp])->setConnection($this->conn);
            $product->save();
            $special = $this->extractKeyMap($record, 'v_specials_price',
                'v_specials_date_avail',
                'v_specials_expires_date');
            $product->specials()->save(app(Specials::class, ['attributes' => $special ])->setConnection($this->conn)
            );
            Log::info($this->hash.'store-product-data:'.var_export($special,true));
            if ($tax){
                $product->tax()->save($tax);
            }
            if ($manufacturer){
                $product->manufacturer()->save($manufacturer);
            }

        }
        return $product;
    }
}