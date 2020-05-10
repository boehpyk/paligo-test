<?php
/**
 * Created by PhpStorm.
 * User: programmer
 * Date: 09/05/2020
 * Time: 19:07
 */

namespace App\services;


use DI\Container;

/**
 * Defines files to upload, services to upload and invokes corresponding services
 *
 * Class FileUploadService
 * @package App\services
 *
 * @TODO move FILEPATH to config
 */
class FileUploadService
{
    const FILEPATH = __DIR__ . '/../../data';

    private $files = [];
    private $services = [];
    private $container;
    private $messages;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->messages = $container->get('messages');
    }

    /**
     * set files to upload and adds full path for them
     *
     * @param array $files
     * @return $this
     */
    public function setFiles(array $files)
    {
        foreach ($files as $file) {
            $this->files[] = self::FILEPATH . DIRECTORY_SEPARATOR . $file;
        }
        return $this;
    }

    /**
     * Checks if exists service where the files need to be uploaded and if it exists instantiates it
     *
     * @param array $services - list of strings defining services to upload
     * @return $this
     */
    public function setServices(array $services)
    {
        foreach ($services as $service) {
            $classname = "App\\services\\" . ucfirst($service) . 'Upload';
            if (class_exists($classname)) {
                $this->services[] = new $classname($this->container);
            }
            else {
                $message = 'Service <strong>' . $service. '</strong> is unavailable at the moment';
                $this->messages->sendFlashMessage($message, 'danger');
            }
        }
        return $this;
    }

    /**
     * Invokes services methods for uploading
     *
     */
    public function upload()
    {
        $result = [];
        foreach ($this->services as $service) {
            $result[] = $service->upload($this->files);
        }
    }
}