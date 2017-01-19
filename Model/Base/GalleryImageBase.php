<?php

/**
 * GalleryImage base model for table: gallery_image
 */

namespace Octo\Media\Model\Base;

use DateTime;
use Block8\Database\Query;
use Octo\Model;
use Octo\Store;

use Octo\Media\Store\GalleryImageStore;
use Octo\Media\Model\GalleryImage;
use Octo\Media\Model\Gallery;
use Octo\File\Model\File;

/**
 * GalleryImage Base Model
 */
abstract class GalleryImageBase extends Model
{
    protected $table = 'gallery_image';
    protected $model = 'GalleryImage';
    protected $data = [
        'gallery_id' => null,
        'image_id' => null,
        'sort_order' => 9999,
    ];

    protected $getters = [
        'gallery_id' => 'getGalleryId',
        'image_id' => 'getImageId',
        'sort_order' => 'getSortOrder',
        'Gallery' => 'getGallery',
        'Image' => 'getImage',
    ];

    protected $setters = [
        'gallery_id' => 'setGalleryId',
        'image_id' => 'setImageId',
        'sort_order' => 'setSortOrder',
        'Gallery' => 'setGallery',
        'Image' => 'setImage',
    ];

    /**
     * Return the database store for this model.
     * @return GalleryImageStore
     */
    public static function Store() : GalleryImageStore
    {
        return GalleryImageStore::load();
    }

    /**
     * Get GalleryImage by primary key: image_id
     * @param string $image_id
     * @return GalleryImage|null
     */
    public static function get(string $image_id) : ?GalleryImage
    {
        return self::Store()->getByImageId($image_id);
    }

    /**
     * @throws \Exception
     * @return GalleryImage
     */
    public function save() : GalleryImage
    {
        $rtn = self::Store()->save($this);

        if (empty($rtn)) {
            throw new \Exception('Failed to save GalleryImage');
        }

        if (!($rtn instanceof GalleryImage)) {
            throw new \Exception('Unexpected ' . get_class($rtn) . ' received from save.');
        }

        $this->data = $rtn->toArray();

        return $this;
    }


    /**
     * Get the value of GalleryId / gallery_id
     * @return int
     */
     public function getGalleryId() : int
     {
        $rtn = $this->data['gallery_id'];

        return $rtn;
     }
    
    /**
     * Get the value of ImageId / image_id
     * @return string
     */
     public function getImageId() : string
     {
        $rtn = $this->data['image_id'];

        return $rtn;
     }
    
    /**
     * Get the value of SortOrder / sort_order
     * @return int
     */
     public function getSortOrder() : int
     {
        $rtn = $this->data['sort_order'];

        return $rtn;
     }
    
    
    /**
     * Set the value of GalleryId / gallery_id
     * @param $value int
     * @return GalleryImage
     */
    public function setGalleryId(int $value) : GalleryImage
    {

        // As this column is a foreign key, empty values should be considered null.
        if (empty($value)) {
            $value = null;
        }


        if ($this->data['gallery_id'] !== $value) {
            $this->data['gallery_id'] = $value;
            $this->setModified('gallery_id');
        }

        return $this;
    }
    
    /**
     * Set the value of ImageId / image_id
     * @param $value string
     * @return GalleryImage
     */
    public function setImageId(string $value) : GalleryImage
    {

        // As this column is a foreign key, empty values should be considered null.
        if (empty($value)) {
            $value = null;
        }


        if ($this->data['image_id'] !== $value) {
            $this->data['image_id'] = $value;
            $this->setModified('image_id');
        }

        return $this;
    }
    
    /**
     * Set the value of SortOrder / sort_order
     * @param $value int
     * @return GalleryImage
     */
    public function setSortOrder(int $value) : GalleryImage
    {

        if ($this->data['sort_order'] !== $value) {
            $this->data['sort_order'] = $value;
            $this->setModified('sort_order');
        }

        return $this;
    }
    

    /**
     * Get the Gallery model for this  by Id.
     *
     * @uses \Octo\Media\Store\GalleryStore::getById()
     * @uses Gallery
     * @return Gallery|null
     */
    public function getGallery() : ?Gallery
    {
        $key = $this->getGalleryId();

        if (empty($key)) {
           return null;
        }

        return Gallery::Store()->getById($key);
    }

    /**
     * Set Gallery - Accepts an ID, an array representing a Gallery or a Gallery model.
     * @throws \Exception
     * @param $value mixed
     * @return GalleryImage
     */
    public function setGallery($value) : GalleryImage
    {
        // Is this a scalar value representing the ID of this foreign key?
        if (is_scalar($value)) {
            return $this->setGalleryId($value);
        }

        // Is this an instance of Gallery?
        if (is_object($value) && $value instanceof Gallery) {
            return $this->setGalleryObject($value);
        }

        // Is this an array representing a Gallery item?
        if (is_array($value) && !empty($value['id'])) {
            return $this->setGalleryId($value['id']);
        }

        // None of the above? That's a problem!
        throw new \Exception('Invalid value for Gallery.');
    }

    /**
     * Set Gallery - Accepts a Gallery model.
     *
     * @param $value Gallery
     * @return GalleryImage
     */
    public function setGalleryObject(Gallery $value) : GalleryImage
    {
        return $this->setGalleryId($value->getId());
    }

    /**
     * Get the File model for this  by Id.
     *
     * @uses \Octo\File\Store\FileStore::getById()
     * @uses File
     * @return File|null
     */
    public function getImage() : ?File
    {
        $key = $this->getImageId();

        if (empty($key)) {
           return null;
        }

        return File::Store()->getById($key);
    }

    /**
     * Set Image - Accepts an ID, an array representing a File or a File model.
     * @throws \Exception
     * @param $value mixed
     * @return GalleryImage
     */
    public function setImage($value) : GalleryImage
    {
        // Is this a scalar value representing the ID of this foreign key?
        if (is_scalar($value)) {
            return $this->setImageId($value);
        }

        // Is this an instance of Image?
        if (is_object($value) && $value instanceof File) {
            return $this->setImageObject($value);
        }

        // Is this an array representing a File item?
        if (is_array($value) && !empty($value['id'])) {
            return $this->setImageId($value['id']);
        }

        // None of the above? That's a problem!
        throw new \Exception('Invalid value for Image.');
    }

    /**
     * Set Image - Accepts a File model.
     *
     * @param $value File
     * @return GalleryImage
     */
    public function setImageObject(File $value) : GalleryImage
    {
        return $this->setImageId($value->getId());
    }
}
