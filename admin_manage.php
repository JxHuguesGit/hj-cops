        <!-- Bootstrap style -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css"
            integrity="sha512-b2QcS5SsA8tZodcDtGRELiGv5SaKSk1vDHDaQRda0htPYWZ6046lr3kJ5bAAQdpV2mmA/4v0wQF9MyU6/pDIAg=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <!-- Font Awesome Icons -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet"
            integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
<?php
use src\Controller\UtilitiesController;
use src\Utils\SessionUtils;

if (strpos(PLUGIN_PATH, 'wamp64')!==false) {
    define('COPS_SITE_URL', 'http://localhost/');
} else {
    define('COPS_SITE_URL', 'https://cops.jhugues.fr/');
}
define('PLUGINS_COPS', COPS_SITE_URL.'wp-content/plugins/hj-cops/');
date_default_timezone_set('Europe/Paris');

class CopsiteAdmin
{
    public static function display(): void
    {
        /////////////////////////////////////////
        // Analyse de l'url
        $uri = SessionUtils::fromServer('REQUEST_URI');
        $arrUri = explode('/', $uri);

        $controller = UtilitiesController::getAdminController($arrUri);

        echo $controller->getAdminContentPage();
    }
}
CopsiteAdmin::display();
?>
