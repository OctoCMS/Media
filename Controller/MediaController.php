<?php
namespace Octo\Media\Controller;

use b8\Cache;
use b8\Image;
use b8\Form;
use Octo\Controller;
use Octo\Event;
use Octo\Http\Response;
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
     * @param string $fileId
     * @param string $width
     * @param string $height
     * @param string $type
     *
     * @return Response
     */
    public function render(string $fileId, string $width = 'auto', string $height = 'auto', string $type = 'auto')
    {
        $file = $this->fileStore->getById($fileId);

        if ($type === 'auto') {
            $type = $this->autoImageType();
        }

        Image::$cacheEnabled = OCTO_CACHE_ENABLED;
        Image::$baseCachePath = OCTO_CACHE_PATH;
        Image::$forceGd = true;

        if ($this->getParam('nocache', 0)) {
            Image::$cacheEnabled = false;
        }

        $imageInfo = ['id' => $file->getId(), 'extension' => $file->getExtension(), 'data' => null];

        Event::trigger('GetFile', $imageInfo);

        if (!empty($imageInfo['data'])) {
            $image = new Image($imageInfo['data'], $file->getId());
            $focal = $file->getMetaKey('focal_point');

            if (!is_null($focal) && is_array($focal)) {
                $image->setFocalPoint($focal[0], $focal[1]);
            }

            $output = (string)$image->render($width, $height, $type);

            return $this->raw($output)->headers([
                'Content-Type' => $image->getMime(),
                'Content-Length' => strlen($output),
                'Cache-Control' => 'public',
                'Pragma' => 'cache',
                'Expires' => gmdate('D, d M Y H:i:s \G\M\T', time() + (86400*100)),
            ]);
        }

        $output = Image\GdImage::blankImage(1, 1);

        return $this->raw($output)->headers([
            'Content-Type' => 'image/gif',
            'Content-Length' => strlen($output),
            'Cache-Control' => 'private',
            'Pragma' => 'no-cache',
            'Expires' => gmdate('D, d M Y H:i:s \G\M\T', time()),
        ]);
    }

    public function resize($width, $height = 'auto', $type = 'jpeg')
    {
        Image::$sourcePath = '';

        $images = $this->getParam('url', []);

        if (is_string($images)) {
            $images = [$images];
        }

        $imageRequestId = 'image_' . md5(http_build_query($images) . '_'.$width.'_'.$height.'_'.$type);

        $cache = Cache::getCache();

        if ($cache->contains($imageRequestId)) {
            $output = $cache->get($imageRequestId);

            header('Content-Type: image/'.$type);
            header('Content-Length: ' . strlen($output));
            header('Cache-Control: public');
            header('Pragma: cache');
            header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + (86400*100)));
            die($output);
        }

        foreach ($images as $url) {
            try {
                $data = @file_get_contents($url);
                $image = new Image($data);
                break;
            } catch (\Exception $ex) {
            }
        }

        if (!empty($image)) {
            try {
                $output = (string)$image->render($width, $height, $type);
            } catch (\Exception $ex) {
                $output = null;
            }
        }

        if (empty($output)) {
            $output = Image\GdImage::blankImage(1, 1);
        }

        $cache->set($imageRequestId, $output);

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

    protected function autoImageType()
    {
        if (strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false) {
            return 'webp';
        }

        return 'jpeg';
    }
}
