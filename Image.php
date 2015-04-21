<?php

namespace Octo\Media;

use Octo\File\Model\File;

class Image
{
    /**
     * @var \Octo\File\Model\File
     */
    public $file;

    protected $tag = false;
    protected $width = 1000;
    protected $height = 'auto';
    protected $format = 'png';

    public function __construct(File $file)
    {
        $this->file = $file;
    }

    public function tag($width = 1000, $height = 'auto', $format = 'png')
    {
        $this->width = $width;
        $this->height = $height;
        $this->format = $format;
        $this->tag = true;

        return $this;
    }

    public function url($width = 1000, $height = 'auto', $format = 'png')
    {
        $this->width = $width;
        $this->height = $height;
        $this->format = $format;
        $this->tag = false;

        return $this;
    }

    public function __toString()
    {
        if ($this->tag) {
            return $this->outputTag();
        } else {
            return $this->outputUrl();
        }
    }

    protected function outputTag()
    {
        return '<img class="img-responsive" src="' . $this->outputUrl() . '">';
    }

    protected function outputUrl()
    {
        return '/media/render/' . $this->file->getId() . '/' . $this->width . '/' . $this->height . '/' . $this->format;
    }

}