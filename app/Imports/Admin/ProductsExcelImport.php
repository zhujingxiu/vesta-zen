<?php

namespace App\Imports\Admin;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProductsExcelImport implements ToArray
{
    /**
     * @param array $array
     * @return array
     */
    public function array(array $array)
    {
        //
        return $array;
    }

}
