<?php
/**
 * Created by PhpStorm.
 * User: programmer
 * Date: 10/05/2020
 * Time: 17:02
 */

namespace App\services;


use DI\Container;

/**
 * Service handle various messages.
 * It sends messages to user, using session flash messages and reports errors to administrator as well.
 *
 * Class MessagesService
 * @package App\services
 */
class MessagesService
{
    private $flash;

    public function __construct(Container $container)
    {
        $this->flash = $container->get('flash');
    }

    /**
     * Sends messages to user using session flash messages.
     * There are 2 types of messages, depending on the type the message's view is different
     *
     * @param $message
     * @param string $type
     */
    public function sendFlashMessage(string $message, string $type = 'success'): void
    {
        $this->flash->addMessage($type, $message);
    }

    /**
     * Sends error messages to admin if server returned high-level error (500 for instance)
     *
     * @param string $message
     */
    public function sendErrorMessageToAdmin(string $message): void
    {
        // @TODO implement sending e-mail or write to log
        echo $message;
        die('error!');
    }

}