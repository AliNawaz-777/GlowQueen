<?php

namespace Webkul\Admin\DataGrids;

use Webkul\Ui\DataGrid\DataGrid;
use DB;

/**
 * CurrencyDataGrid class
 *
 * @author Arhamsoft (info@arhamsoft.com)
 * @copyright 2020 Arhamsoft (pvt) Ltd (https://arhamsoft.com)
 */
class CurrencyDataGrid extends DataGrid
{
    protected $index = 'id'; //the column that needs to be treated as index column

    protected $sortOrder = 'desc'; //asc or desc

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('currencies')->addSelect('id', 'name', 'code');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index' => 'id',
            'label' => trans('admin::app.datagrid.id'),
            'type' => 'number',
            'searchable' => false,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'name',
            'label' => trans('admin::app.datagrid.name'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'code',
            'label' => trans('admin::app.datagrid.code'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);
    }

    public function prepareActions() {
        $this->addAction([
            'title' => 'Edit Currency',
            'method' => 'GET', // use GET request only for redirect purposes
            'route' => 'admin.currencies.edit',
            'icon' => 'icon pencil-lg-icon'
        ]);

        $this->addAction([
            'title' => 'Delete Currency',
            'method' => 'POST', // use GET request only for redirect purposes
            'route' => 'admin.currencies.delete',
            'icon' => 'icon trash-icon'
        ]);
    }
}