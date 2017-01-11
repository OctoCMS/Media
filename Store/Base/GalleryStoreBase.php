<?php

/**
 * Gallery base store for table: gallery

 */

namespace Octo\Media\Store\Base;

use Block8\Database\Connection;
use Octo\Store;
use Octo\Media\Model\Gallery;
use Octo\Media\Model\GalleryCollection;
use Octo\Media\Store\GalleryStore;

/**
 * Gallery Base Store
 */
class GalleryStoreBase extends Store
{
    /** @var GalleryStore $instance */
    protected static $instance = null;

    /** @var string */
    protected $table = 'gallery';

    /** @var string */
    protected $model = 'Octo\Media\Model\Gallery';

    /** @var string */
    protected $key = 'id';

    /**
     * Return the database store for this model.
     * @return GalleryStore
     */
    public static function load() : GalleryStore
    {
        if (is_null(self::$instance)) {
            self::$instance = new GalleryStore(Connection::get());
        }

        return self::$instance;
    }

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
