<?php

/**
 * Gallery model collection
 */

namespace Octo\Media\Model;

use Block8\Database\Model\Collection;

/**
 * Gallery Model Collection
 */
class GalleryCollection extends Collection
{
    /**
     * Add a Gallery model to the collection.
     * @param string $key
     * @param Gallery $value
     * @return GalleryCollection
     */
    public function addGallery($key, Gallery $value)
    {
        return parent::add($key, $value);
    }

    /**
     * @param $key
     * @return Gallery|null
     */
    public function get($key)
    {
        return parent::get($key);
    }
}
