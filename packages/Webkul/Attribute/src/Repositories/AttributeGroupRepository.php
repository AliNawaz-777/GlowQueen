<?php

namespace Webkul\Attribute\Repositories;

use Webkul\Core\Eloquent\Repository;

/**
 * Attribute Group Reposotory
 *
 * @author    Arhamsoft <info@arhamsoft.com>
 * @copyright 2020 Arhamsoft (pvt) Ltd (https://arhamsoft.com)
 */
class AttributeGroupRepository extends Repository
{

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Attribute\Contracts\AttributeGroup';
    }
}