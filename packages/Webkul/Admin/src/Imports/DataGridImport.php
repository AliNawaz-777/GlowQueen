<?php

namespace Webkul\Admin\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;


/**
 * DataGridImport class
 *
 * @author    Rahul Shukla <rahulshukla.symfony517@webkul.com>
 * @copyright 2020 Arhamsoft (pvt) Ltd (https://arhamsoft.com)
*/

class DataGridImport implements ToCollection, WithHeadingRow
{
    use Importable;

    /**
     * @param array $row
     * @return void
    */
    public function collection(Collection $rows)
    {
    }
}