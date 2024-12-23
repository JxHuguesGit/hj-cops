<?php
use src\Collection\PlayerCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Controller\CentralController;
use src\Controller\HomePageController;
use src\Controller\LibraryController;
use src\Controller\MailPageController;
use src\Controller\ProfilePageController;
use src\Controller\SettingsPageController;
use src\Controller\UtilitiesController;
use src\Controller\WidgetController;
use src\Repository\PlayerRepository;
use src\Utils\SessionUtils;

if (strpos(PLUGIN_PATH, 'wamp64')!==false) {
    define('COPS_SITE_URL', 'http://localhost/');
} else {
    define('COPS_SITE_URL', 'https://cops.jhugues.fr/');
}
define('PLUGINS_COPS', COPS_SITE_URL.'wp-content/plugins/hj-cops/');
define('IGNORE_CDN', true);
date_default_timezone_set('Europe/Paris');

class CopsiteBase implements ConstantConstant, LabelConstant, TemplateConstant
{
    public static function display(): void
    {
        $msgProcessError = '';
        if (isset($_POST) && !empty($_POST)) {
            // Est-on en train de se connecter ?
            static::processForm($msgProcessError);
        } elseif (SessionUtils::fromGet(ConstantConstant::CST_LOGOUT)==ConstantConstant::CST_LOGOUT) {
            // On est en train de se déconnecter.
            static::processDeconnection();
        }

        // Ensuite est-on connecté ?
        if (!SessionUtils::isLogged()) {
            $controller = new HomePageController();
        } else {
            /////////////////////////////////////////
            // Analyse de l'url
            $uri = SessionUtils::fromServer('REQUEST_URI');
            $arrUri = explode('/', $uri);
            $slug = $arrUri[1] ?? ConstantConstant::CST_HOME;

            switch ($slug) {
                case ConstantConstant::CST_SETTINGS :
                    $controller = new SettingsPageController();
                break;
                case ConstantConstant::CST_PROFILE :
                    $controller = new ProfilePageController($arrUri);
                break;
                case ConstantConstant::CST_LIBRARY :
                    $controller = new LibraryController($arrUri);
                break;
                case ConstantConstant::CST_CENTRAL :
                    $controller = new CentralController($arrUri);
                break;
                case ConstantConstant::CST_NOTIFICATION :
                case ConstantConstant::CST_TRASH :
                        $controller = new MailPageController($arrUri, $slug);
                break;
                default :
                    $controller = new HomePageController();
                break;
            }
            /////////////////////////////////////////
        }

        if (COPS_SITE_URL=='http://localhost/' && IGNORE_CDN) {
            $srcCssFilesTpl = $controller->getRender(TemplateConstant::TPL_LOCAL_CSS, [PLUGINS_COPS]);
            $srcJsFilesTpl = $controller->getRender(TemplateConstant::TPL_LOCAL_JS, [PLUGINS_COPS]);
        } else {
            $srcCssFilesTpl = $controller->getRender(TemplateConstant::TPL_WWW_CSS);
            $srcJsFilesTpl = $controller->getRender(TemplateConstant::TPL_WWW_JS);
        }

        $errorPanel = '';
        if ($msgProcessError!='') {
            $errorPanel = $controller->getRender(TemplateConstant::TPL_SECTION_ERROR, [$msgProcessError]);
        }

        $attributes = [
            $controller->getTitle(),
            $srcCssFilesTpl,
            $srcJsFilesTpl,
            PLUGINS_COPS,
            $controller->getContentHeader(),
            $controller->getContentPage($msgProcessError),
            $controller->getContentFooter(),
            $controller->getClassLogged(),
            $errorPanel,
        ];
        echo $controller->getRender(TemplateConstant::TPL_BASE, $attributes);
    }

    public static function getConnectionPanel(string $msgProcessError=''): string
    {
        $controller = new UtilitiesController();
        $attributes = [
            $msgProcessError=='' ? 'hidden' : '',
            $msgProcessError,
        ];
        return $controller->getRender(TemplateConstant::TPL_CONN_PANEL, $attributes);
    }

    public static function processDeconnection(): void
    {
        SessionUtils::unsetSession('copsSessionId');
    }
    
    public static function processForm(string &$msgProcessError=''): void
    {
        $formName = SessionUtils::fromPost(ConstantConstant::CST_FORMNAME);
        if ($formName==ConstantConstant::CST_CHANGEMDP) {
            $player = SessionUtils::getPlayer();
            $oldPassword = SessionUtils::fromPost(ConstantConstant::CST_OLDMDP);
            $newPassword = SessionUtils::fromPost(ConstantConstant::CST_NEWMDP);
            $confirmPassword = SessionUtils::fromPost(ConstantConstant::CST_CONFIRMMDP);

            if ($newPassword==$confirmPassword && password_verify($oldPassword, $player->getField(FieldConstant::PASSWORD))) {
                $player->setField(FieldConstant::PASSWORD, password_hash($newPassword, PASSWORD_BCRYPT));
                $player->update();
            } else {
                $msgProcessError = LabelConstant::LBL_ERR_MDPCHG;
            }

        } elseif ($formName=='dashboardSettings') {
            WidgetController::processForm();
        } else {
            $logname = SessionUtils::fromPost(FieldConstant::LOGNAME);
            $password = SessionUtils::fromPost(FieldConstant::PASSWORD);
    
            $repository = new PlayerRepository(new PlayerCollection());
            $criteria = [
                FieldConstant::LOGNAME => $logname,
            ];
            $playerCollection = $repository->findBy($criteria);

            if ($playerCollection->length()==1) {
                $obj = $playerCollection->current();
                if (password_verify($password, $obj->getField(FieldConstant::PASSWORD)) || strtolower($logname)=='guest') {
                    SessionUtils::setSession('copsSessionId', $obj->getField(FieldConstant::ID));
                } else {
                    $msgProcessError = LabelConstant::LBL_ERR_LOGIN;
                }
            } else {
                $msgProcessError = LabelConstant::LBL_ERR_LOGIN;
            }
    }
    }

}
CopsiteBase::display();
