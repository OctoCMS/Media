<?php

namespace Octo\Media\Controller;

use Block8\Database\Query;
use Octo\Controller;
use Octo\File\Store\FileStore;
use Octo\Media\Store\GalleryStore;
use Octo\Store;
use Octo\Template;

class GalleryController extends Controller
{
    /**
     * @var GalleryStore
     */
    protected $galleryStore;

    /**
     * @var FileStore
     */
    protected $fileStore;

    public function init()
    {
        parent::init();
        $this->galleryStore = Store::get('Gallery');
        $this->fileStore = Store::get('File');
    }

    public function index()
    {
        $template = new Template('Gallery/index');
        $template->galleries = $this->galleryStore
            ->find()
            ->where('parent_id', null, Query::IS_NULL)
            ->order('sort_order', 'ASC')
            ->get();

        return $template->render();
    }

    public function __call($method, $args)
    {
        $slug = $this->request->getPath();
        $prefix = '/gallery/';

        if (substr($slug, 0, strlen($prefix)) == $prefix) {
            $slug = substr($slug, strlen($prefix));
        }

        $gallery = $this->galleryStore
            ->find()
            ->where('slug', $slug)
            ->first();
        
        $template = new Template('Gallery/view');
        $template->gallery = $gallery;
        $template->galleries = $this->galleryStore
            ->find()
            ->where('parent_id', $gallery->getId())
            ->order('sort_order', 'ASC')
            ->get();

        return $template->render();
    }
}