<?php
namespace Octo\Media\Admin\Controller;

use Block8\Database\Query;
use Octo\Admin\Controller;
use Octo\Admin\Menu;
use Octo\File\Store\FileStore;
use Octo\Media\Model\Gallery;
use Octo\Media\Store\GalleryStore;
use Octo\Store;

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

    public static function registerMenus(Menu $menu)
    {
        $media = $menu->addRoot('Media', '/media')->setIcon('picture-o');
        $media->addChild(new Menu\Item('Photo Galleries', '/gallery'));
    }

    public function init()
    {
        parent::init();
        $this->galleryStore = Store::get('Gallery');
        $this->fileStore = Store::get('File');
    }

    public function index()
    {
        $this->setTitle('Galleries');
        $this->template->galleries = $this->galleryStore
            ->find()
            ->where('parent_id', null, Query::IS_NULL)
            ->get();
    }

    public function create($parentId = null)
    {
        $gallery = new Gallery();
        $gallery->setParentId($parentId);
        $gallery->setTitle('Untitled Gallery');
        $gallery = $this->galleryStore->save($gallery);

        return $this->redirect('/gallery/edit/' . $gallery->getId());
    }

    public function edit($galleryId)
    {
        $gallery = $this->galleryStore->getById($galleryId);

        if ($this->request->getMethod() == 'POST') {
            $gallery->setTitle($this->getParam('title'));
            $gallery->setDescription($this->getParam('description'));

            $gallery = $this->galleryStore->save($gallery);
        }

        $this->setTitle($gallery->getTitle(), 'Galleries');

        $this->template->gallery = $gallery;
        $this->template->galleries = $this->galleryStore
            ->find()
            ->where('parent_id', $gallery->getId())
            ->order('sort_order', 'ASC')
            ->get();

        $this->template->images = $this->fileStore
            ->find()
            ->join('gallery_image', 'image_id', 'id')
            ->where('gallery_id', $gallery->getId())
            ->order('sort_order', 'ASC')
            ->get();
    }
    
    public function delete($galleryId)
    {
        $gallery = $this->galleryStore->getById($galleryId);
        $this->galleryStore->delete($gallery);
        
        return $this->return()->success('Gallery deleted.');
    }

    public function attach($galleryId, $imageId)
    {
        $this->galleryStore->attachImage($galleryId, $imageId);
        return $this->json(['error' => false]);
    }

    public function detach($galleryId, $imageId)
    {
        $this->galleryStore->detachImage($galleryId, $imageId);
        return $this->json(['error' => false]);
    }

    public function order($galleryId)
    {
        $order = json_decode($this->getParam('order', '{}'), true);

        $this->galleryStore->order($galleryId, $order);
        return $this->json(['error' => false]);
    }
}
