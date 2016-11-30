<?php

/**
 * Gallery base store for table: gallery

 */

namespace Octo\Media\Store\Base;

use Octo\Store;
use Octo\Media\Model\Gallery;
use Octo\Media\Model\GalleryCollection;

/**
 * Gallery Base Store
 */
class GalleryStoreBase extends Store
{
    protected $table = 'gallery';
    protected $model = 'Octo\Media\Model\Gallery';
    protected $key = 'id';

    /**
    * @param $value
    * @return Gallery|null
    */
    public function getByPrimaryKey($value)
    {
        return $this->getById($value);
    }


    /**
     * Get a Gallery object by Id.
     * @param $value
     * @return Gallery|null
     */
    public function getById(int $value)
    {
        // This is the primary key, so try and get from cache:
        $cacheResult = $this->cacheGet($value);

        if (!empty($cacheResult)) {
            return $cacheResult;
        }

        $rtn = $this->where('id', $value)->first();
        $this->cacheSet($value, $rtn);

        return $rtn;
    }

    /**
     * Get all Gallery objects by ParentId.
     * @return \Octo\Media\Model\GalleryCollection
     */
    public function getByParentId($value, $limit = null)
    {
        return $this->where('parent_id', $value)->get($limit);
    }

    /**
     * Gets the total number of Gallery by ParentId value.
     * @return int
     */
    public function getTotalByParentId($value) : int
    {
        return $this->where('parent_id', $value)->count();
    }
}
