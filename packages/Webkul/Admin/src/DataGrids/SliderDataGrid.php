<?php

namespace Webkul\Admin\DataGrids;

use Webkul\Ui\DataGrid\DataGrid;
use DB;

/**
 * SliderDataGrid Class
 *
 * @author Arhamsoft (info@arhamsoft.com)
 * @copyright 2020 Arhamsoft (pvt) Ltd (https://arhamsoft.com)
 */
class SliderDataGrid extends DataGrid
{
    protected $index = 'slider_id';

    protected $sortOrder = 'desc'; //asc or desc

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('sliders as sl')->addSelect('sl.id as slider_id', 'sl.title', 'ch.name')->leftJoin('channels as ch', 'sl.channel_id', '=',
        'ch.id');

        $this->addFilter('slider_id', 'sl.id');
        $this->addFilter('channel_name', 'ch.name');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index' => 'slider_id',
            'label' => trans('admin::app.datagrid.id'),
            'type' => 'number',
            'searchable' => false,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'title',
            'label' => trans('admin::app.datagrid.title'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index' => 'name',
            'label' => trans('admin::app.datagrid.channel-name'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true
        ]);
    }

    public function prepareActions() {
        $this->addAction([
            'title' => 'Edit Slider',
            'method' => 'GET', // use GET request only for redirect purposes
            'route' => 'admin.sliders.edit',
            'icon' => 'icon pencil-lg-icon'
        ]);

        $this->addAction([
            'title' => 'Delete Slider',
            'method' => 'POST', // use GET request only for redirect purposes
            'route' => 'admin.sliders.delete',
            'icon' => 'icon trash-icon'
        ]);
    }
}