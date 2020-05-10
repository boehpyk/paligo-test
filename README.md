# Simple outline of files uploading service for paligo.net

**Installation**

1. clone this repository
2. Run `composer install`
3. Put files to upload into /data folder
4. Edit account settings for particular service (e.g. src/services/GithubUpload.php)
5. Go to project directory and start PHP server in /public/ folder. `php -S localhost:8000 -t public/`
6. Go to URL `localhost:8000`

