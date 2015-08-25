<?php
namespace Octo\Media\Controller;

use b8\Image;
use b8\Form;
use Octo\Controller;
use Octo\Store;
use Octo\File\Store\FileStore;
use Octo\File\Store\FileDownloadStore;
use Octo\File\Model\File;
use Octo\File\Model\FileDownload;

class MediaController extends Controller
{

    /**
     * @var FileStore
     */
    protected $fileStore;

    /**
     *
     */
    public function init()
    {
        $this->fileStore = Store::get('File');
    }

    /**
     * @param $fileId
     * @param int $width
     * @param int $height
     */
    public function render($fileId, $width = null, $height = 'auto', $type = 'jpeg', $debug = false)
    {
        $file = $this->fileStore->getById($fileId);

        if ($debug) {
            ini_set('error_reporting', E_ALL);
            ini_set('log_errors', 'on');
            ini_set('display_errors', 'off');

            Image::$cacheEnabled = false;
            Image::$forceGd = true;
        }

        Image::$sourcePath = APP_PATH . '/public/uploads/';

        $image = new Image($file->getId() . '.' . $file->getExtension());

        list($originalWidth, $originalHeight) = getimagesize($file->getPath());

        if ($width == null) {
            $width = $originalWidth;
        }

        $focal = $file->getMeta('focal_point');
        if (is_null($focal) || !is_array($focal)) {
            $focal = [round($originalWidth/2), round($originalHeight/2)];
        }

        $image->setFocalPoint($focal[0], $focal[1]);

        $output = (string)$image->render($width, $height, $type);

        header('Content-Type: image/'.$type);
        header('Content-Length: ' . strlen($output));
        header('Cache-Control: public');
        header('Pragma: cache');
        header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + (86400*100)));

        die($output);
    }

    public function resize($width, $height = 'auto', $type = 'jpeg')
    {
        Image::$sourcePath = '';

        try {
            $image = new Image($this->getParam('url'));
            $output = (string)$image->render($width, $height, $type);
        } catch (\Exception $ex) {
            $image = new \Imagick();
            $image->newImage($width, $height, new \ImagickPixel('grey'));
            $image->setImageFormat($type);
            $output = (string)$image->getImage();
        }

        header('Content-Type: image/'.$type);
        header('Content-Length: ' . strlen($output));
        header('Cache-Control: public');
        header('Pragma: cache');
        header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + (86400*100)));

        die($output);
    }

    /**
     * Return an AJAX list of all images
     *
     * @param $scope
     * @return string JSON
     */
    public function ajax($scope)
    {
        $files = $this->fileStore->getAllForScope($scope);
//        File::$sleepable = array('id', 'url', 'title');
        foreach ($files as &$item) {
            if (file_exists($item->getPath())) {
                $imageData = getimagesize($item->getPath());
                $item = $item->toArray(1);
                $item['width'] = $imageData[0];
                $item['height'] = $imageData[1];
            } else {
                $item = $item->toArray(1);
            }
        }
        print json_encode($files);
        exit;
    }

    /**
     * Download a file
     *
     * Download the file with its original filename and content type, and log the download
     *
     * @param $fileId File ID to download
     */
    public function download($fileId)
    {
        $file = $this->fileStore->getById($fileId);
        $download = new FileDownload();
        $download->setFileId($file->getId());
        $download->setDownloaded(new \DateTime);

        $fileDownloadStore = new FileDownloadStore;
        $fileDownloadStore->save($download);

        header('Content-type: ' . $file->getMimeType());
        header('Content-Disposition: attachment; filename="' . $file->getFilename() . '"');
        readfile($file->getPath());
    }
}
