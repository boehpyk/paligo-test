<?php
/**
 * Created by PhpStorm.
 * User: programmer
 * Date: 09/05/2020
 * Time: 17:23
 */

namespace App\controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use DI\Container;

class IndexController
{
    private $request;
    private $response;
    private $twig;
    private $unzipper;
    private $uploader;
    private $flash;
    private $messages;

    /**
     * Path to files.
     * @TODO - move to config
     */
    const FILEPATH = __DIR__ . '/../../data';

    /**
     * IndexController constructor.
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param Container $container - application container
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function __construct(ServerRequestInterface $request, ResponseInterface $response, Container $container)
    {
        $this->request      = $request;
        $this->response     = $response;
        $this->twig         = $container->get('twig');
        $this->unzipper     = $container->get('unzip');
        $this->uploader     = $container->get('upload');
        $this->flash        = $container->get('flash');
        $this->messages     = $container->get('messages');
    }

    /**
     * Shows initial form
     * Read /data directory and gets list of available files
     *
     * @return ResponseInterface
     */
    public function index()
    {
        $services = [
            'github',
            'bitbucket',
            'ftp'
        ];
        $files = [];

        if ($handle = opendir(self::FILEPATH)) {

            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $files[] = $entry;
                }
            }
            closedir($handle);
        }

        return $this->twig->render($this->response, 'index.html.twig', [
            'flashes'   => $this->flash->getMessages(),
            'services'  => $services,
            'files'     => $files
        ]);
    }

    /**
     * Uploads file to selected services according to selected form fields
     * 1. Unzip file if it's archive
     * 2. Defines services to upload
     * 3. Upload files to defined services
     *
     * @return ResponseInterface
     */
    public function upload()
    {
        $file       = $this->request->getParsedBody()['file'];
        $services   = $this->request->getParsedBody()['services'];

        $result = [];

        if (file_exists(self::FILEPATH . DIRECTORY_SEPARATOR . $file)) {
            $files = $this->unzipper->setFile(self::FILEPATH . DIRECTORY_SEPARATOR . $file)->unzip();
        }
        else {
            $message = 'File <strong>' . $file. '</strong> does not exists on the server';
            $this->messages->sendFlashMessage($message, 'danger');
        }

        if ($files !== null && count($files) > 0) {
            $this->uploader->setFiles($files)->setServices($services)->upload();
        }


        return $this->response
            ->withHeader('Location', '/')
            ->withStatus(302);
    }
}