<?php

namespace Webkul\Admin\DataGrids;

use Webkul\Ui\DataGrid\DataGrid;
use DB;

/**
 * NewsLetterDataGrid Class
 *
 * @author Arhamsoft (info@arhamsoft.com)
 * @copyright 2020 Arhamsoft (pvt) Ltd (https://arhamsoft.com)
 */
class NewsLetterDataGrid extends DataGrid
{
    protected $index = 'id';

    protected $sortOrder = 'desc'; //asc or desc

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('subscribers_list')->addSelect('id', 'is_subscribed', 'email');

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
            'index' => 'is_subscribed',
            'label' => trans('admin::app.datagrid.subscribed'),
            'type' => 'string',
            'searchable' => false,
            'sortable' => true,
            'filterable' => true,
            'wrapper' => function($value) {
                if ($value->is_subscribed == 1)
                    return 'True';
                else
                    return 'False';
            }
        ]);

        $this->addColumn([
            'index' => 'email',
            'label' => trans('admin::app.datagrid.email'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);
    }

    public function prepareActions() {
        $this->addAction([
            'title' => 'Edit News Letter',
            'method' => 'GET', // use GET request only for redirect purposes
            'route' => 'admin.customers.subscribers.edit',
            'icon' => 'icon pencil-lg-icon'
        ]);

        $this->addAction([
            'title' => 'Delete News Letter',
            'method' => 'POST', // use GET request only for redirect purposes
            'route' => 'admin.customers.subscribers.delete',
            'confirm_text' => trans('ui::app.datagrid.massaction.delete', ['resource' => 'Exchange Rate']),
            'icon' => 'icon trash-icon'
        ]);
    }
}