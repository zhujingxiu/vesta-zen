<?php


namespace App\Admin\Extensions;

use Encore\Admin\Form\Field\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class XFile extends File
{
    protected $view = 'admin.extensions.xfile';

    protected function uploadAndDeleteOriginal(UploadedFile $file)
    {
        $this->renameFile($file);

        $path = null;

        //dd('uploadAndDeleteOriginal',$this->getDateDirectory(),$this->name);
        if (!is_null($this->storagePermission)) {
            $path = $this->storage->putFileAs($this->getDateDirectory(), $file, $this->name, $this->storagePermission);
        } else {
            $path = $this->storage->putFileAs($this->getDateDirectory(), $file, $this->name);
        }

        $this->destroy();

        return $path;
    }

    protected function renameFile($file)
    {
        $this->name = $this->generateUniqueName($file);
    }

    protected function getDateDirectory()
    {
        $dir = rtrim($this->getDirectory(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . date('ymd');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        return $dir;
    }
}