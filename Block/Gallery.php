<?php

namespace Octo\Media\Block;

use b8\Database;
use b8\Form\Element\Button;
use b8\Form\Element\Checkbox;
use Octo\Admin\Form;
use Octo\Admin\Template as AdminTemplate;
use Octo\Block;
use Octo\File\Model\File;
use Octo\Store;
use Octo\Template;

class Gallery extends Block
{
    /**
     * @var \Octo\File\Store\FileStore
     */
    protected $fileStore;

    public static function getInfo()
    {
        return [
            'title' => 'Image Gallery',
            'icon' => 'image',
            'editor' => ['\Octo\Media\Block\Gallery', 'getEditorForm']
        ];
    }

    public static function getEditorForm($item)
    {
        $form = new Form();
        $form->setId('block_' . $item['id']);

        $formSelect = \b8\Form\Element\Select::create('image', 'Add an Image');
        $formSelect->setId('block_gallery_parent_' . $item['id']);
        $formSelect->setClass('octo-image-picker skip-autosave');
        $form->addField($formSelect);

        $controls = Checkbox::create('controls', 'Show Controls', false);
        $controls->setCheckedValue(1);

        if (!empty($item['content']['controls'])) {
            $controls->setValue(1);
        }

        $form->addField($controls);

        $controls = Checkbox::create('captions', 'Show Captions', false);
        $controls->setCheckedValue(1);

        if (!empty($item['content']['captions'])) {
            $controls->setValue(1);
        }

        $form->addField($controls);

        $saveButton = new Button();
        $saveButton->setValue('Save ' . $item['name']);
        $saveButton->setClass('block-save btn btn-success');
        $form->addField($saveButton);

        if (empty($item['content'])) {
            $item['content'] = [];
        }

        $template = AdminTemplate::getAdminTemplate('BlockEditor/Gallery');
        $template->form = $form;
        $template->blockContent = json_encode($item['content']);
        $template->blockId = $item['id'];

        return $template->render();
    }

    public function init()
    {
        $this->fileStore = Store::get('File');
    }

    public function renderNow()
    {
        $this->limit = 25;
        $this->view->hasControls = $this->getContent('controls', false);
        $this->view->hasCaptions = $this->getContent('captions', false);;
        $this->view->imageFormat = 'jpeg';
        $this->view->imageWidth = 1170;

        if (array_key_exists('limit', $this->templateParams)) {
            $this->limit = $this->templateParams['limit'] ? $this->templateParams['limit'] : $this->limit;
        }

        if (array_key_exists('hasControls', $this->templateParams)) {
            $this->view->hasControls = $this->templateParams['hasControls'] ? true : false;
        }

        if (array_key_exists('hasCaptions', $this->templateParams)) {
            $this->view->hasCaptions = $this->templateParams['hasCaptions'] ? true : false;
        }

        if (array_key_exists('imageFormat', $this->templateParams)) {
            $this->view->imageFormat = $this->templateParams['imageFormat'];
        }

        if (array_key_exists('imageWidth', $this->templateParams)) {
            $this->view->imageWidth = $this->templateParams['imageWidth'];
        }

        $galleryImages = $this->getContent('images', []);

        if (is_array($galleryImages)) {
            $images = [];
            $rendered = 0;

            foreach ($galleryImages as $imageId) {
                if (++$rendered >= $this->limit) {
                    break;
                }

                $image = $this->fileStore->getById($imageId);

                if (!is_null($image)) {
                    $images[] = $image;
                }
            }

            $this->view->id = uniqid('gallery_');
            $this->view->images = $images;
            $this->view->imageCount = count($images);

            if (count($images)) {
                $this->view->firstImageId = $images[0]->getId();
            }
        }
    }
}
