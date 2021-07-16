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
class BlogDataGrid extends DataGrid
{
    protected $index = 'id';

    protected $sortOrder = 'desc'; //asc or desc

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('blogs')                
                ->addSelect('id','blog_title', 'status', 'blog_date');

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
            'index' => 'blog_title',
            'label' => trans('admin::app.datagrid.blog_title'),
            'type' => 'string',
            'searchable' => false,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            // 'index' => date('d/M/Y', strtotime('blog_date')),
            'index' => 'blog_date',
            'label' => trans('admin::app.datagrid.blog-date'),
            'type' => 'datetime',
            'sortable' => false,
            'searchable' => false,
            'closure' => true,
            'filterable' => true,
            'wrapper' => function ($value)
            {
                return date('d/M/Y', strtotime($value->blog_date));
            }
        ]);

        $this->addColumn([
            'index' => 'status',
            'label' => trans('admin::app.datagrid.status'),
            'type' => 'string',
            'sortable' => true,
            'searchable' => true,
            'closure' => true,
            'filterable' => true,
            'wrapper' => function ($value) {
                if ($value->status == 'confirm')
                    return '<span class="badge badge-md badge-success">Active</span>';
                else if ($value->status == "cancel")
                    return '<span class="badge badge-md badge-danger">Closed</span>';
                else if ($value->status == "pending")
                    return '<span class="badge badge-md badge-warning">Pending</span>';
            }
        ]);
    }

    public function prepareActions() {
        $this->addAction([
            'title' => 'Blog View',
            'method' => 'GET', // use GET request only for redirect purposes
            'route' => 'admin.blog.view',
            'icon' => 'icon eye-icon'
        ]);
    }
}