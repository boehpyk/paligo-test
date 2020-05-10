<?php
/**
 * Created by PhpStorm.
 * User: programmer
 * Date: 09/05/2020
 * Time: 21:22
 */

namespace App\services;

use App\services\User;
use App\services\MessagesService;
use DI\Container;
use Httpful\Http;

class GithubUpload implements UploadInterface
{
    private $settings;
    private $messages;

    public function __construct(Container $container)
    {
        $this->messages = $container->get('messages');
    }

    /**
     * Gets login, password, branch and all needed credentials.
     * In this case they are hardcoded
     *
     */
    public function getSettings(): void
    {
        $this->settings = [
            'userName'      => 'boehpyk',
            'password'      => '',
            'repoName'      => '',
            'branchname'    => 'master',
            'folder'        => ''
        ];
    }

    /**
     * Function uploads bunch of files into GitHub account
     *
     * @param array $files - array of filepaths to upload
     */
    public function upload(array $files): void
    {
        $this->getSettings();

        foreach ($files as $file) {
            $sourceFileInfo = pathinfo($file);
            $sourceFile = file_get_contents($file);
            $sourceFile = base64_encode($sourceFile);

            $url = sprintf(
                "https://api.github.com/repos/%s/%s/contents/%s",
                $this->settings['userName'],
                $this->settings['repoName'],
//                $this->settings['folder'],
                $sourceFileInfo['basename']
            );
            $base64 = base64_encode($this->settings['userName'] . ':' . $this->settings['password']);
            $user = User::init()->getData();
            $commitMessage = $user->real_name . ' has pushed test file ' . strtoupper($sourceFileInfo['extension']) . ' from paligo';
            try {
                $response = \Httpful\Request::put($url)
                    ->addHeader('Authorization', "Basic $base64")
                    ->addHeaders(
                        array(
                            'Accept-Encoding' => 'gzip, deflate',
                            'Host' => 'api.github.com',
                            'Cache-Control' => 'no-cache',
                            'Accept' => '*/*',
                        )
                    )
                    ->body('{
                                "message": "' . $commitMessage . '",
                                "content": "' . $sourceFile . '",
                                "branch":"' . $this->settings['branchname'] . '"
                                }')
                    ->send();
                if ($response->code == 201 || $response->code == 200) {
                    $message = "Paligo has successfully pushed <strong>{$sourceFileInfo['basename']}</strong> into your Github account.";
                    $this->messages->sendFlashMessage($message);
                } else {
                    $message = "Paligo was unable to push <strong>{$sourceFileInfo['basename']}</strong> into your Github account. Please check your Github integration settings.";
                    $this->messages->sendFlashMessage($message, 'danger');
                }
            } catch (\Exception $e) {
                $message = "Paligo was unable to push <strong>{$sourceFileInfo['basename']}</strong> into your Github account. Please check your Github integration settings.";
                $this->messages->sendFlashMessage($message, 'danger');
                $this->messages->sendErrorMessageToAdmin($e->getMessage());
            }
        }
    }


}