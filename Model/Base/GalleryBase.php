<?php

/**
 * Gallery base model for table: gallery
 */

namespace Octo\Media\Model\Base;

use DateTime;
use Block8\Database\Query;
use Octo\Model;
use Octo\Store;

use Octo\Media\Store\GalleryStore;
use Octo\Media\Model\Gallery;

/**
 * Gallery Base Model
 */
abstract class GalleryBase extends Model
{
    protected $table = 'gallery';
    protected $model = 'Gallery';
    protected $data = [
        'id' => null,
        'parent_id' => null,
        'title' => null,
        'description' => null,
        'sort_order' => 0,
        'hidden' => 0,
        'slug' => null,
    ];

    protected $getters = [
        'id' => 'getId',
        'parent_id' => 'getParentId',
        'title' => 'getTitle',
        'description' => 'getDescription',
        'sort_order' => 'getSortOrder',
        'hidden' => 'getHidden',
        'slug' => 'getSlug',
        'Parent' => 'getParent',
    ];

    protected $setters = [
        'id' => 'setId',
        'parent_id' => 'setParentId',
        'title' => 'setTitle',
        'description' => 'setDescription',
        'sort_order' => 'setSortOrder',
        'hidden' => 'setHidden',
        'slug' => 'setSlug',
        'Parent' => 'setParent',
    ];

    /**
     * Return the database store for this model.
     * @return GalleryStore
     */
    public static function Store() : GalleryStore
    {
        return GalleryStore::load();
    }

    /**
     * Get Gallery by primary key: id
     * @param int $id
     * @return Gallery|null
     */
    public static function get(int $id) : ?Gallery
    {
        return self::Store()->getById($id);
    }

    /**
     * @throws \Exception
     * @return Gallery
     */
    public function save() : Gallery
    {
        $rtn = self::Store()->save($this);

        if (empty($rtn)) {
            throw new \Exception('Failed to save Gallery');
        }

        if (!($rtn instanceof Gallery)) {
            throw new \Exception('Unexpected ' . get_class($rtn) . ' received from save.');
        }

        $this->data = $rtn->toArray();

        return $this;
    }


    /**
     * Get the value of Id / id
     * @return int
     */
     public function getId() : int
     {
        $rtn = $this->data['id'];

        return $rtn;
     }
    
    /**
     * Get the value of ParentId / parent_id
     * @return int
     */
     public function getParentId() : ?int
     {
        $rtn = $this->data['parent_id'];

        return $rtn;
     }
    
    /**
     * Get the value of Title / title
     * @return string
     */
     public function getTitle() : string
     {
        $rtn = $this->data['title'];

        return $rtn;
     }
    
    /**
     * Get the value of Description / description
     * @return string
     */
     public function getDescription() : ?string
     {
        $rtn = $this->data['description'];

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
     * Get the value of Hidden / hidden
     * @return int
     */
     public function getHidden() : int
     {
        $rtn = $this->data['hidden'];

        return $rtn;
     }
    
    /**
     * Get the value of Slug / slug
     * @return string
     */
     public function getSlug() : ?string
     {
        $rtn = $this->data['slug'];

        return $rtn;
     }
    
    
    /**
     * Set the value of Id / id
     * @param $value int
     * @return Gallery
     */
    public function setId(int $value) : Gallery
    {

        if ($this->data['id'] !== $value) {
            $this->data['id'] = $value;
            $this->setModified('id');
        }

        return $this;
    }
    
    /**
     * Set the value of ParentId / parent_id
     * @param $value int
     * @return Gallery
     */
    public function setParentId(?int $value) : Gallery
    {

        // As this column is a foreign key, empty values should be considered null.
        if (empty($value)) {
            $value = null;
        }


        if ($this->data['parent_id'] !== $value) {
            $this->data['parent_id'] = $value;
            $this->setModified('parent_id');
        }

        return $this;
    }
    
    /**
     * Set the value of Title / title
     * @param $value string
     * @return Gallery
     */
    public function setTitle(string $value) : Gallery
    {

        if ($this->data['title'] !== $value) {
            $this->data['title'] = $value;
            $this->setModified('title');
        }

        return $this;
    }
    
    /**
     * Set the value of Description / description
     * @param $value string
     * @return Gallery
     */
    public function setDescription(?string $value) : Gallery
    {

        if ($this->data['description'] !== $value) {
            $this->data['description'] = $value;
            $this->setModified('description');
        }

        return $this;
    }
    
    /**
     * Set the value of SortOrder / sort_order
     * @param $value int
     * @return Gallery
     */
    public function setSortOrder(int $value) : Gallery
    {

        if ($this->data['sort_order'] !== $value) {
            $this->data['sort_order'] = $value;
            $this->setModified('sort_order');
        }

        return $this;
    }
    
    /**
     * Set the value of Hidden / hidden
     * @param $value int
     * @return Gallery
     */
    public function setHidden(int $value) : Gallery
    {

        if ($this->data['hidden'] !== $value) {
            $this->data['hidden'] = $value;
            $this->setModified('hidden');
        }

        return $this;
    }
    
    /**
     * Set the value of Slug / slug
     * @param $value string
     * @return Gallery
     */
    public function setSlug(?string $value) : Gallery
    {

        if ($this->data['slug'] !== $value) {
            $this->data['slug'] = $value;
            $this->setModified('slug');
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
    public function getParent() : ?Gallery
    {
        $key = $this->getParentId();

        if (empty($key)) {
           return null;
        }

        return Gallery::Store()->getById($key);
    }

    /**
     * Set Parent - Accepts an ID, an array representing a Gallery or a Gallery model.
     * @throws \Exception
     * @param $value mixed
     * @return Gallery
     */
    public function setParent($value) : Gallery
    {
        // Is this a scalar value representing the ID of this foreign key?
        if (is_scalar($value)) {
            return $this->setParentId($value);
        }

        // Is this an instance of Parent?
        if (is_object($value) && $value instanceof Gallery) {
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
     * @param $value Gallery
     * @return Gallery
     */
    public function setParentObject(Gallery $value) : Gallery
    {
        return $this->setParentId($value->getId());
    }
}
