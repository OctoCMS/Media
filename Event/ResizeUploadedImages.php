<?php

namespace Octo\Media\Event;

use b8\Image;
use Octo\Event\Listener;
use Octo\Event\Manager;

class ResizeUploadedImages extends Listener
{
    public function registerListeners(Manager $manager)
    {
        $manager->registerListener('BeforePutFile', array($this, 'beforePutFile'), true);
    }

    public function beforePutFile(array &$fileInfo)
    {
        /** @var \Octo\File\Model\File $file */
        $file = $fileInfo['file'];

        if ($file->getScope() == 'images') {
            Image::$cacheEnabled = OCTO_CACHE_ENABLED;
            Image::$baseCachePath = OCTO_CACHE_PATH;

            try {
                $image = new Image($fileInfo['data'], $file->getId());

                switch ($file->getMimeType()) {
                    case 'image/gif':
                        $format = 'gif';
                        break;
                    case 'image/png':
                        $format = 'png';
                        break;
                    default:
                        $format = 'jpeg';
                        break;
                }

                $width = $image->getImageWidth();
                $height = $image->getImageHeight();

                if ($width > $height && $width > 1920) {
                    $fileInfo['data'] = (string)$image->render(1920, 'auto', $format);
                } elseif ($height > $width && $height > 1920) {
                    $fileInfo['data'] = (string)$image->render('auto', 1920, $format);
                }
            } catch (\Exception $ex) {}
        }

        return true;
    }
}