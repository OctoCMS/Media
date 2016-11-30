<?php

/**
 * GalleryImage model collection
 */

namespace Octo\Media\Model;

use Block8\Database\Model\Collection;

/**
 * GalleryImage Model Collection
 */
class GalleryImageCollection extends Collection
{
    /**
     * Add a GalleryImage model to the collection.
     * @param string $key
     * @param GalleryImage $value
     * @return GalleryImageCollection
     */
    public function addGalleryImage($key, GalleryImage $value)
    {
        return parent::add($key, $value);
    }

    /**
     * @param $key
     * @return GalleryImage|null
     */
    public function get($key)
    {
        return parent::get($key);
    }
}
