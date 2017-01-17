<?php

/**
 * GalleryImage base store for table: gallery_image

 */

namespace Octo\Media\Store\Base;

use Block8\Database\Connection;
use Octo\Store;
use Octo\Media\Model\GalleryImage;
use Octo\Media\Model\GalleryImageCollection;
use Octo\Media\Store\GalleryImageStore;

/**
 * GalleryImage Base Store
 */
class GalleryImageStoreBase extends Store
{
    /** @var GalleryImageStore $instance */
    protected static $instance = null;

    /** @var string */
    protected $table = 'gallery_image';

    /** @var string */
    protected $model = 'Octo\Media\Model\GalleryImage';

    /** @var string */
    protected $key = 'image_id';

    /**
     * Return the database store for this model.
     * @return GalleryImageStore
     */
    public static function load() : GalleryImageStore
    {
        if (is_null(self::$instance)) {
            self::$instance = new GalleryImageStore(Connection::get());
        }

        return self::$instance;
    }

    /**
    * @param $value
    * @return GalleryImage|null
    */
    public function getByPrimaryKey($value)
    {
        return $this->getByImageId($value);
    }


    /**
     * Get a GalleryImage object by GalleryId.
     * @param $value
     * @return GalleryImage|null
     */
    public function getByGalleryId(int $value)
    {
        return $this->where('gallery_id', $value)->first();
    }

    /**
     * Get a GalleryImage object by ImageId.
     * @param $value
     * @return GalleryImage|null
     */
    public function getByImageId(string $value)
    {
        // This is the primary key, so try and get from cache:
        $cacheResult = $this->cacheGet($value);

        if (!empty($cacheResult)) {
            return $cacheResult;
        }

        $rtn = $this->where('image_id', $value)->first();
        $this->cacheSet($value, $rtn);

        return $rtn;
    }
}
