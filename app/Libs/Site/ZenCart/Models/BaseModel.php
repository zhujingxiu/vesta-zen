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

    public function __construct($host=null,$db_user=null,$db_pass=null,$db_name=null,array $attributes = [])
    {
        if (!$this->table){
            $this->table = $this->getTable();
        }
        if (is_array($this->_pk)){
            $this->incrementing = false;
        }
        $this->primaryKey = $this->_pk ?? $this->table.'_'.$this->primaryKey;

        if ($host && $db_user && $db_pass && $db_name){
            $this->setConnection(new_db_connection($host,$db_user,$db_pass,$db_name));
        }
        parent::__construct($attributes);
    }


}