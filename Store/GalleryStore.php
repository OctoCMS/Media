<?php

/**
 * GalleryStore for table: gallery
 */

namespace Octo\Media\Store;

use b8\Database;
use Octo\Media\Store\Base\GalleryStoreBase;

/**
 * Gallery Store
 */
class GalleryStore extends GalleryStoreBase
{
    public function attachImage($galleryId, $imageId)
    {
        $db = Database::getConnection('write');
        $stmt = $db->prepare('REPLACE INTO gallery_image (gallery_id, image_id) VALUES (:gallery, :image)');
        $stmt->bindValue(':gallery', $galleryId);
        $stmt->bindValue(':image', $imageId);

        $stmt->execute();
    }

    public function detachImage($galleryId, $imageId)
    {
        $db = Database::getConnection('write');
        $stmt = $db->prepare('DELETE FROM gallery_image WHERE gallery_id = :gallery AND image_id = :image');
        $stmt->bindValue(':gallery', $galleryId);
        $stmt->bindValue(':image', $imageId);

        $stmt->execute();
    }

    public function order($galleryId, $order)
    {
        if (!is_array($order)) {
            return;
        }

        $db = Database::getConnection('write');
        $stmt = $db->prepare('UPDATE gallery_image SET sort_order = :pos WHERE gallery_id = :gallery AND image_id = :image');

        foreach ($order as $imageId => $position) {
            $stmt->bindValue(':gallery', $galleryId);
            $stmt->bindValue(':image', $imageId);
            $stmt->bindValue(':pos', $position);
            $stmt->execute();
        }
    }
}
