<?php
/**
 * Created by PhpStorm.
 * User: programmer
 * Date: 09/05/2020
 * Time: 19:35
 */

namespace App\services;

use DI\Container;
use ZipArchive;

/**
 * Class UnzipService
 * Unpacks zip archive or returns array with single file if this file is not archive
 * @TODO add security check for unzipped files
 * @package App\services
 */
class UnzipService
{
    private $filepath;
    private $messages;

    public function __construct(Container $container)
    {
        $this->messages = $container->get('flash');
    }

    /**
     * Sets path to file to handle
     *
     * @param $filepath - path to file
     * @return $this
     */
    public function setFile($filepath)
    {
        $this->filepath = $filepath;
        return $this;
    }

    /**
     * If file is zip-file tries to unzip it and returns array of files that were compressed within it.
     * Else returns array with single file to upload.
     *
     * @return array
     */
    public function unzip()
    {
        $files = [];
        if (file_exists($this->filepath)) {
            $pathinfo = pathinfo(realpath($this->filepath));

            if ($pathinfo['extension'] == 'zip') {
                $zip = new ZipArchive;
                $res = $zip->open($this->filepath);
                if ($res === TRUE) {
                    $zip->extractTo($pathinfo['dirname']);
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $files[] = $zip->getNameIndex($i);
                    }
                    $zip->close();
                } else {

                    $message = 'File <strong>' . $this->filepath. '</strong> could not be unzipped and will be uploaded as is.';
                    $this->messages->sendFlashMessage($message, 'warning');

                    $files[] = $pathinfo['basename'];
                }
            }
            else {
                $files[] = $pathinfo['basename'];
            }
        }
        else {
            $message = 'File <strong>' . $this->filepath. '</strong> does not exists on the server';
            $this->messages->sendFlashMessage($message, 'danger');
        }
        return $files;
    }
}