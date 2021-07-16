<?php

namespace Webkul\Admin\DataGrids;

use Webkul\Ui\DataGrid\DataGrid;
use DB;

/**
 * OrderDataGrid Class
 *
 * @author Arhamsoft (info@arhamsoft.com)
 * @copyright 2020 Arhamsoft (pvt) Ltd (https://arhamsoft.com)
 */
class TestimonailDataGrid extends DataGrid
{
    protected $index = 'id';

    protected $sortOrder = 'desc'; //asc or desc

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('testimonails')                
                ->addSelect('id','testimonail_title', 'short_description','name','created_at');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index' => 'id',
            'label' => trans('admin::app.datagrid.id'),
            'type' => 'string',
            'searchable' => false,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'testimonail_title',
            'label' => trans('admin::app.datagrid.blog_title'),
            'type' => 'string',
            'searchable' => false,
            'sortable' => true,
            'filterable' => true
        ]);

         $this->addColumn([
            'index' => 'name',
            'label' => trans('admin::app.datagrid.name'),
            'type' => 'string',
            'searchable' => false,
            'sortable' => true,
            'filterable' => true
        ]);

    
    }

    public function prepareActions() {
        $this->addAction([
            'title' => 'Testimonail View',
            'method' => 'GET', // use GET request only for redirect purposes
            'route' => 'admin.testimonail.view',
            'icon' => 'icon eye-icon'
        ]);
    }
}