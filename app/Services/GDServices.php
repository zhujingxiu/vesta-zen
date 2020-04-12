<?php

namespace App\Services;

class GDServices extends GDImage
{
}


class GDImage
{
    private $baseImage;

    private $image, $width, $height, $type;
    private $imagePath;

    public function __construct($path)
    {
        $this->imagePath = $path;
        $this->image = imagecreatefromstring(file_get_contents($path));
        list($this->width, $this->height, $this->type) = getimagesize($this->imagePath);
    }

    public function image()
    {
        return $this->image;
    }

    public function compressSize($quality = 50)
    {
        $new_image = imagecreatetruecolor($this->width, $this->height);
        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $this->width, $this->height, $this->width, $this->height);
        imagejpeg($new_image, $this->imagePath, $quality);
        $this->image = $new_image;
        return $this;
    }

    public function resize($size = 2,$quality = 50)
    {
        $width = intval($this->width/$size);
        $height = intval($this->height/$size);
        $new_image = imagecreatetruecolor($width, $height);
        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
        imagejpeg($new_image, $this->imagePath, $quality);
        $this->image = $new_image;
        return $this;
    }


    /**
     * 设置背景图
     * @param $baseImage
     * by zh
     */
    public function setBaseImage($baseImage)
    {
        $this->baseImage = $baseImage;
    }

    /**
     * 按比例缩放
     * @param $w_proportion 0.8
     * @param $h_proportion 0.8
     * @return $this
     * by zh
     */
    public function scaleZoom($w_proportion, $h_proportion)
    {
        $new_w = $this->width * $w_proportion;
        $new_h = $this->height * $h_proportion;

        return $this->autoZoom($new_w, $new_h);
    }

    /**
     * 自由缩放
     * @param $new_w
     * @param $new_h
     * @return $this
     * by zh
     */
    public function autoZoom($new_w, $new_h)
    {
        $new_image = imagecreatetruecolor($new_w, $new_h);
        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $new_w, $new_h, $this->width, $this->height);

        // 缩放过的图片
        $this->image = $new_image;
        $this->width = $new_w;
        $this->height = $new_h;

        return $this;
    }

    public function circle()
    {
        $newpic = imagecreatetruecolor($this->width, $this->height);
        imagealphablending($newpic, false);
        $transparent = imagecolorallocatealpha($newpic, 0, 0, 0, 127);
        $radius = $this->width / 2;
        for ($x = 0; $x < $this->width; $x++) {
            for ($y = 0; $y < $this->height; $y++) {
                $c = imagecolorat($this->image, $x, $y);
                $_x = $x - $this->width / 2;
                $_y = $y - $this->height / 2;
                if ((($_x * $_x) + ($_y * $_y)) < ($radius * $radius)) {
                    imagesetpixel($newpic, $x, $y, $c);
                } else {
                    imagesetpixel($newpic, $x, $y, $transparent);
                }
            }
        }
        $this->image = $newpic;
        imagesavealpha($this->image, true);
    }

    /**
     * 合并图片(支持透明图)
     * @param $x
     * @param $y
     * @param int $opacity
     * by zh
     */
    public function merge($x, $y, $opacity = 0)
    {
        // 创建一个空白画像
        $cut = imagecreatetruecolor($this->width, $this->height);
        // 把空白画像 映射到背景图
        imagecopy($cut, $this->baseImage, 0, 0, $x, $y, $this->width, $this->height);

        $opacity = 100 - $opacity;

        // 把透明图映射到 空白画像
        imagecopy($cut, $this->image, 0, 0, 0, 0, $this->width, $this->height);

        // 合并
        imagecopymerge($this->baseImage, $cut, $x, $y, 0, 0, $this->width, $this->height, $opacity);
    }

    /**
     * 添加图片
     * @param $file
     * @param $callback
     * @return $this|array
     * by zh
     */
    public function appendImage($file, $callback)
    {
        try{
            $image = new GDImage($file);
            $image->setBaseImage($this->image);

            $callback($image);
        }catch (\Exception $e){

        };


        return $this;
    }

    /**
     * 添加文字
     * @param $content
     * @param $size
     * @param $callback
     * @param null $font
     * @return $this|array
     * by zh
     */
    public function appendFont($content, $size, $callback, $font = null)
    {
        $font = new GDFont($this->image, $content, $size, $font);
        // 设置默认字体
        $font->color(255, 255, 255);
        $callback($font);

        return $this;
    }

    public function generate($path)
    {
        switch ($this->type) {
            case 1://GIF
                return imagegif($this->image, $path);
            case 2://JPG;
                return imagejpeg($this->image, $path);
            case 3://PNG
                return imagepng($this->image, $path);
            default:
                return false;
                break;
        }
    }
}

class GDFont
{
    private $baseImage;

    private $size;
    private $color;
    private $content;
    private $font;

    public function __construct($baseImage, $content, $size, $font = null)
    {
        $this->baseImage = $baseImage;
        $this->content = $content;
        $this->size = $size;
        $font ? $this->font = $font : $this->font = base_path('public/font/PingFangMedium.ttf');
    }

    public function colLimit($limit)
    {
        //$col_limit = mb_split('/(?<!^)(?!$)/u',$this->content,$limit);
        $col_limit = $this->mb_chunk_split($this->content, $limit);
        $this->content = $col_limit;
    }

    public function color($red, $green, $blue)
    {
        $this->color = imagecolorallocate($this->baseImage, $red, $green, $blue);
    }

    public function merge($x, $y)
    {
        if (is_array($this->content)) {
            $margin = $this->size + 10; // 10 的间距
            $offset = 0;
            foreach ($this->content as $content) {
                imagettftext($this->baseImage, $this->size, 0, $x, $y + $offset, $this->color, $this->font, $content);
                $offset += $margin;
            }

        } else {
            imagettftext($this->baseImage, $this->size, 0, $x, $y, $this->color, $this->font, $this->content);
        }
    }

    /**
     * 分割字符串
     * @param $string 要分割的字符串
     * @param $length 指定的长度
     * @return array
     * by zh
     */
    function mb_chunk_split($string, $length)
    {
        $array = [];
        $strlen = mb_strlen($string, "utf-8");
        while ($strlen) {
            $array[] = mb_substr($string, 0, $length, "utf-8");
            $string = mb_substr($string, $length, $strlen, "utf-8");
            $strlen = mb_strlen($string, "utf-8");
        }
        return $array;
    }
}