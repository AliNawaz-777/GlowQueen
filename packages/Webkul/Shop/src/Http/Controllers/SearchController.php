<?php

namespace Webkul\Shop\Http\Controllers;

use Webkul\Product\Repositories\SearchRepository;

/**
 * Search controller
 *
 * @author  Arhamsoft (info@arhamsoft.com)
 * @copyright 2020 Arhamsoft (pvt) Ltd (https://arhamsoft.com)
 */
 class SearchController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * SearchRepository object
     *
     * @var Object
    */
    protected $searchRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Product\Repositories\SearchRepository $searchRepository
     * @return void
    */
    public function __construct(SearchRepository $searchRepository)
    {
        $this->_config = request('_config');

        $this->searchRepository = $searchRepository;
    }

    /**
     * Index to handle the view loaded with the search results
     * 
     * @return \Illuminate\View\View 
     */
    // public function index()
    // {
    //     $results = $this->searchRepository->search(request()->all());

    //     return view($this->_config['view'])->with('results', $results->count() ? $results : null);
    // }

    public function index()
    {
        $search = (isset(request()->all()['term'])) ? request()->all()['term'] : '';
        return view($this->_config['view'])->with('search',$search);
    }
}
