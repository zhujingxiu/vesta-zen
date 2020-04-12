<?php
namespace App\Libs\Site\ZenCart\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected $_pk;
    public $timestamps = false;
    protected $guarded = [];
    public const CREATED_AT = 'date_added';
    public const UPDATED_AT = 'last_modified';

    public function __construct(array $attributes = [])
    {
        if (!$this->table){
            $this->table = $this->getTable();
        }
        if (is_array($this->_pk)){
            $this->incrementing = false;
        }
        $this->primaryKey = $this->_pk ?? $this->table.'_'.$this->primaryKey;

        parent::__construct($attributes);
    }


}