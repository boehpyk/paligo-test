<?php
/**
 * Created by PhpStorm.
 * User: programmer
 * Date: 09/05/2020
 * Time: 21:19
 */

namespace App\services;


interface UploadInterface
{
    /**
     * Sets credentials for logging into service from User service or another source.
     */
    public function getSettings(): void ;

    /**
     * Uploads file to service
     *
     * @param array $files
     */
    public function upload(array $files): void ;
}