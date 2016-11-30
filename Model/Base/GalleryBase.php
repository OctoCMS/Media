<?php

/**
 * Gallery base model for table: gallery
 */

namespace Octo\Media\Model\Base;

use DateTime;
use Octo\Model;
use Octo\Store;

/**
 * Gallery Base Model
 */
class GalleryBase extends Model
{
    protected function init()
    {
        $this->table = 'gallery';
        $this->model = 'Gallery';

        // Columns:
        
        $this->data['id'] = null;
        $this->getters['id'] = 'getId';
        $this->setters['id'] = 'setId';
        
        $this->data['parent_id'] = null;
        $this->getters['parent_id'] = 'getParentId';
        $this->setters['parent_id'] = 'setParentId';
        
        $this->data['title'] = null;
        $this->getters['title'] = 'getTitle';
        $this->setters['title'] = 'setTitle';
        
        $this->data['description'] = null;
        $this->getters['description'] = 'getDescription';
        $this->setters['description'] = 'setDescription';
        
        $this->data['sort_order'] = null;
        $this->getters['sort_order'] = 'getSortOrder';
        $this->setters['sort_order'] = 'setSortOrder';
        
        $this->data['hidden'] = null;
        $this->getters['hidden'] = 'getHidden';
        $this->setters['hidden'] = 'setHidden';
        
        $this->data['slug'] = null;
        $this->getters['slug'] = 'getSlug';
        $this->setters['slug'] = 'setSlug';
        
        // Foreign keys:
        
        $this->getters['Parent'] = 'getParent';
        $this->setters['Parent'] = 'setParent';
        
    }

    
    /**
     * Get the value of Id / id
     * @return int
     */

     public function getId()
     {
        $rtn = $this->data['id'];

        return $rtn;
     }
    
    /**
     * Get the value of ParentId / parent_id
     * @return int
     */

     public function getParentId()
     {
        $rtn = $this->data['parent_id'];

        return $rtn;
     }
    
    /**
     * Get the value of Title / title
     * @return string
     */

     public function getTitle()
     {
        $rtn = $this->data['title'];

        return $rtn;
     }
    
    /**
     * Get the value of Description / description
     * @return string
     */

     public function getDescription()
     {
        $rtn = $this->data['description'];

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
     * Get the value of Hidden / hidden
     * @return int
     */

     public function getHidden()
     {
        $rtn = $this->data['hidden'];

        return $rtn;
     }
    
    /**
     * Get the value of Slug / slug
     * @return string
     */

     public function getSlug()
     {
        $rtn = $this->data['slug'];

        return $rtn;
     }
    
    
    /**
     * Set the value of Id / id
     * @param $value int
     */
    public function setId(int $value)
    {

        $this->validateNotNull('Id', $value);

        if ($this->data['id'] === $value) {
            return;
        }

        $this->data['id'] = $value;
        $this->setModified('id');
    }
    
    /**
     * Set the value of ParentId / parent_id
     * @param $value int
     */
    public function setParentId($value)
    {


        // As this column is a foreign key, empty values should be considered null.
        if (empty($value)) {
            $value = null;
        }



        if ($this->data['parent_id'] === $value) {
            return;
        }

        $this->data['parent_id'] = $value;
        $this->setModified('parent_id');
    }
    
    /**
     * Set the value of Title / title
     * @param $value string
     */
    public function setTitle(string $value)
    {

        $this->validateNotNull('Title', $value);

        if ($this->data['title'] === $value) {
            return;
        }

        $this->data['title'] = $value;
        $this->setModified('title');
    }
    
    /**
     * Set the value of Description / description
     * @param $value string
     */
    public function setDescription($value)
    {



        if ($this->data['description'] === $value) {
            return;
        }

        $this->data['description'] = $value;
        $this->setModified('description');
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
     * Set the value of Hidden / hidden
     * @param $value int
     */
    public function setHidden(int $value)
    {

        $this->validateNotNull('Hidden', $value);

        if ($this->data['hidden'] === $value) {
            return;
        }

        $this->data['hidden'] = $value;
        $this->setModified('hidden');
    }
    
    /**
     * Set the value of Slug / slug
     * @param $value string
     */
    public function setSlug($value)
    {



        if ($this->data['slug'] === $value) {
            return;
        }

        $this->data['slug'] = $value;
        $this->setModified('slug');
    }
    
    
    /**
     * Get the Gallery model for this  by Id.
     *
     * @uses \Octo\Media\Store\GalleryStore::getById()
     * @uses \Octo\Media\Model\Gallery
     * @return \Octo\Media\Model\Gallery
     */
    public function getParent()
    {
        $key = $this->getParentId();

        if (empty($key)) {
           return null;
        }

        return Store::get('Gallery')->getById($key);
    }

    /**
     * Set Parent - Accepts an ID, an array representing a Gallery or a Gallery model.
     * @throws \Exception
     * @param $value mixed
     */
    public function setParent($value)
    {
        // Is this a scalar value representing the ID of this foreign key?
        if (is_scalar($value)) {
            return $this->setParentId($value);
        }

        // Is this an instance of Parent?
        if (is_object($value) && $value instanceof \Octo\Media\Model\Gallery) {
            return $this->setParentObject($value);
        }

        // Is this an array representing a Gallery item?
        if (is_array($value) && !empty($value['id'])) {
            return $this->setParentId($value['id']);
        }

        // None of the above? That's a problem!
        throw new \Exception('Invalid value for Parent.');
    }

    /**
     * Set Parent - Accepts a Gallery model.
     *
     * @param $value \Octo\Media\Model\Gallery
     */
    public function setParentObject(\Octo\Media\Model\Gallery $value)
    {
        return $this->setParentId($value->getId());
    }

    public function Gallerys()
    {
        return Store::get('Gallery')->where('parent_id', $this->data['id']);
    }
}
