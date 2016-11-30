<?php

/**
 * Gallery model for table: gallery
 */

namespace Octo\Media\Model;

use Octo\Media\Model\Base\GalleryBase;
use Octo\Store;

/**
 * Gallery Model
 */
class Gallery extends GalleryBase
{
    public function setTitle(string $title)
    {
        parent::setTitle($title);

        $base = $this->getParentId() ? $this->getParent()->getSlug() . '/' : '';
        $slug = strtolower($title);
        $slug = str_replace('\'', '', $slug);
        $slug = preg_replace('/([^a-zA-Z0-9\-])/', '-', $slug);
        $slug = str_replace('--', '-', $slug);

        $this->setSlug($base . $slug);
    }

    public function imageCount()
    {
        return Store::get('File')
            ->find()
            ->join('gallery_image', 'image_id', 'id')
            ->where('gallery_id', $this->getId())
            ->order('sort_order', 'ASC')
            ->count();
    }

    public function cover()
    {
        $image = Store::get('File')
            ->find()
            ->join('gallery_image', 'image_id', 'id')
            ->where('gallery_id', $this->getId())
            ->order('sort_order', 'ASC')
            ->first();

        if (!$image) {
            $firstChild = Store::get('Gallery')
                ->find()
                ->where('parent_id', $this->getId())
                ->order('sort_order', 'ASC')
                ->first();

            $image = $firstChild->cover();
        }

        return $image;
    }

    public function images()
    {
        return Store::get('File')
            ->find()
            ->join('gallery_image', 'image_id', 'id')
            ->where('gallery_id', $this->getId())
            ->order('sort_order', 'ASC')
            ->get();
    }
}
