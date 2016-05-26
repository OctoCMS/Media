<?php

namespace Octo\Media\Block;

use Octo\Pages\Block\Text;
use Octo\Admin\Form;
use b8\Form\Element\Button;
use b8\Form\Element\TextArea;
use b8\Form\FieldSet;
use Octo\Store;

class TextImage extends Text
{
    public static function getInfo()
    {
        return [
            'title' => 'Text & Image',
            'icon' => 'picture-o',
            'editor' => ['\Octo\Media\Block\TextImage', 'getEditorForm'],
        ];
    }

    public static function getEditorForm($item)
    {
        $form = new Form('block_textimage_' . $item['id']);
        $form->setId('block_' . $item['id']);
        $fieldset = new FieldSet();
        $form->addField($fieldset);

        $content = TextArea::create('content', 'Content', false);
        $content->setId('block_textimage_content_' . $item['id']);
        $content->setClass('ckeditor advanced');

        $image = \b8\Form\Element\Select::create('image', 'Image', false);
        $image->setId('block_image_image_' . $item['id']);
        $image->setClass('octo-image-picker');

        if (isset($item['content']['image'])) {
            $file = Store::get('File')->getById($item['content']['image']);

            if ($file) {
                $image->setOptions([$file->getId() => $file->getTitle()]);
            }
        }

        $link = \b8\Form\Element\Text::create('link', 'Link', false);
        $link->setId('block_textimage_link_'.$item['id']);

        $saveButton = new Button();
        $saveButton->setValue('Save ' . $item['name']);
        $saveButton->setClass('block-save btn btn-success');
        $fieldset->addField($content);
        $fieldset->addField($image);
        $fieldset->addField($link);
        $fieldset->addField($saveButton);

        if (isset($item['content']) && is_array($item['content'])) {
            $form->setValues($item['content']);
        }

        return $form;
    }

    public function renderNow()
    {
        return $this;
    }

    public function __toString()
    {
        $this->view->width = 512;
        $this->view->height = 'auto';

        if (isset($this->templateParams['width'])) {
            $this->view->width = $this->templateParams['width'];
        }

        if (isset($this->templateParams['height'])) {
            $this->view->height = $this->templateParams['height'];
        }

        if (isset($this->content['image'])) {
            $this->view->image = $this->content['image'];
        }

        if (isset($this->content['link'])) {
            $this->view->link = $this->content['link'];
        }

        if (array_key_exists('content', $this->content)) {
            $content = $this->content['content'];

            // Replace file blocks
            $content = preg_replace_callback('/\<img id\=\"([a-zA-Z0-9]{32})".*>/', [$this, 'replaceFile'], $content);
            $this->view->content = $content;
        }

        return $this->view->render();
    }

    public function getText()
    {
        return $this->getContent('content', '');
    }

    public function getImage()
    {
        $image = $this->getContent('image', null);

        if (!empty($image)) {
            $file = Store::get('File')->getById($image);
            return new \Octo\Media\Image($file);
        }
    }
}
