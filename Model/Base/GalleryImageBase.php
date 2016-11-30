<?php

/**
 * GalleryImage base model for table: gallery_image
 */

namespace Octo\Media\Model\Base;

use DateTime;
use Octo\Model;
use Octo\Store;

/**
 * GalleryImage Base Model
 */
class GalleryImageBase extends Model
{
    protected function init()
    {
        $this->table = 'gallery_image';
        $this->model = 'GalleryImage';

        // Columns:
        
        $this->data['gallery_id'] = null;
        $this->getters['gallery_id'] = 'getGalleryId';
        $this->setters['gallery_id'] = 'setGalleryId';
        
        $this->data['image_id'] = null;
        $this->getters['image_id'] = 'getImageId';
        $this->setters['image_id'] = 'setImageId';
        
        $this->data['sort_order'] = null;
        $this->getters['sort_order'] = 'getSortOrder';
        $this->setters['sort_order'] = 'setSortOrder';
        
        // Foreign keys:
        
        $this->getters['Image'] = 'getImage';
        $this->setters['Image'] = 'setImage';
        
    }

    
    /**
     * Get the value of GalleryId / gallery_id
     * @return int
     */

     public function getGalleryId()
     {
        $rtn = $this->data['gallery_id'];

        return $rtn;
     }
    
    /**
     * Get the value of ImageId / image_id
     * @return string
     */

     public function getImageId()
     {
        $rtn = $this->data['image_id'];

        return $rtn;
     }
    
    /**
     * Get the value of SortOrder / sort_order
     * @return int
     */

     public function getSortOrder()
     {
        $rtn = $this->data['sort_order'];

        return $rtn;
     }
    
    
    /**
     * Set the value of GalleryId / gallery_id
     * @param $value int
     */
    public function setGalleryId(int $value)
    {

        $this->validateNotNull('GalleryId', $value);

        if ($this->data['gallery_id'] === $value) {
            return;
        }

        $this->data['gallery_id'] = $value;
        $this->setModified('gallery_id');
    }
    
    /**
     * Set the value of ImageId / image_id
     * @param $value string
     */
    public function setImageId(string $value)
    {


        // As this column is a foreign key, empty values should be considered null.
        if (empty($value)) {
            $value = null;
        }

        $this->validateNotNull('ImageId', $value);

        if ($this->data['image_id'] === $value) {
            return;
        }

        $this->data['image_id'] = $value;
        $this->setModified('image_id');
    }
    
    /**
     * Set the value of SortOrder / sort_order
     * @param $value int
     */
    public function setSortOrder(int $value)
    {

        $this->validateNotNull('SortOrder', $value);

        if ($this->data['sort_order'] === $value) {
            return;
        }

        $this->data['sort_order'] = $value;
        $this->setModified('sort_order');
    }
    
    
    /**
     * Get the File model for this  by Id.
     *
     * @uses \Octo\File\Store\FileStore::getById()
     * @uses \Octo\File\Model\File
     * @return \Octo\File\Model\File
     */
    public function getImage()
    {
        $key = $this->getImageId();

        if (empty($key)) {
           return null;
        }

        return Store::get('File')->getById($key);
    }

    /**
     * Set Image - Accepts an ID, an array representing a File or a File model.
     * @throws \Exception
     * @param $value mixed
     */
    public function setImage($value)
    {
        // Is this a scalar value representing the ID of this foreign key?
        if (is_scalar($value)) {
            return $this->setImageId($value);
        }

        // Is this an instance of Image?
        if (is_object($value) && $value instanceof \Octo\File\Model\File) {
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
     * @param $value \Octo\File\Model\File
     */
    public function setImageObject(\Octo\File\Model\File $value)
    {
        return $this->setImageId($value->getId());
    }
}
