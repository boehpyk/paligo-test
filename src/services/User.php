<?php
/**
 * Created by PhpStorm.
 * User: programmer
 * Date: 10/05/2020
 * Time: 16:33
 */

namespace App\services;

/**
 * Mock class for User object. It's used into upload services
 *
 * Class User
 * @package App\services
 */
class User
{
    private $user = [];

    /**
     * Init method. It's static because of compatibility to old code
     *
     * @return User
     */
    public static function init()
    {
        $obj = new self();

        $obj->user = (object)[
            'nickname'  => 'boehpyk',
            'real_name' => 'Igor Bulygin',
            'email'     => 'boehpyk@gmail.com'
        ];
        return $obj;
    }

    public function getData()
    {
        return $this->user;
    }

}