<?php

namespace Octo\Media\Block;

use b8\Config;
use b8\Form\Element\Button;
use Octo\Admin\Form;
use Octo\Block;
use Octo\Store;

class Image extends Block
{
    public static function getInfo()
    {
        return [
            'title' => 'Image',
            'icon' => 'picture-o',
            'editor' => ['\Octo\Media\Block\Image', 'getEditorForm'],
        ];
    }

    public static function getEditorForm($item)
    {
        $form = new Form('block_image_' . $item['id']);
        $form->setId('block_' . $item['id']);

        $image = \b8\Form\Element\Text::create('image', 'Image', false);
        $image->setId('block_image_image_' . $item['id']);
        $image->setClass('octo-image-picker');

        $saveButton = new Button();
        $saveButton->setValue('Save ' . $item['name']);
        $saveButton->setClass('block-save btn btn-success');
        $form->addField($image);
        $form->addField($saveButton);

        if (isset($item['content']) && is_array($item['content'])) {
            $form->setValues($item['content']);
        }

        return $form;
    }

    public function renderNow()
    {
        $image = $this->getContent('image', null);

        if (!empty($image)) {
            $file = Store::get('File')->getById($image);
            return new \Octo\Media\Image($file);
        }
    }
}
