<?php

/**
 * GalleryImage base store for table: gallery_image

 */

namespace Octo\Media\Store\Base;

use Octo\Store;
use Octo\Media\Model\GalleryImage;
use Octo\Media\Model\GalleryImageCollection;

/**
 * GalleryImage Base Store
 */
class GalleryImageStoreBase extends Store
{
    protected $table = 'gallery_image';
    protected $model = 'Octo\Media\Model\GalleryImage';
    protected $key = 'image_id';

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
