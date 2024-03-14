<?php
use src\Collection\PlayerCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Controller\HomeController;
use src\Controller\LibraryController;
use src\Controller\MailPageController;
use src\Controller\ProfileController;
use src\Controller\SettingsController;
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
            $controller = new HomeController();
        } else {
            /////////////////////////////////////////
            // Analyse de l'url
            $uri = SessionUtils::fromServer('REQUEST_URI');
            $arrUri = explode('/', $uri);
            $slug = $arrUri[1] ?? ConstantConstant::CST_HOME;

            switch ($slug) {
                case ConstantConstant::CST_SETTINGS :
                    $controller = new SettingsController();
                break;
                case ConstantConstant::CST_PROFILE :
                    $controller = new ProfileController($arrUri);
                break;
                case ConstantConstant::CST_LIBRARY :
                    $controller = new LibraryController($arrUri);
                break;
                case ConstantConstant::CST_NOTIFICATION :
                case ConstantConstant::CST_TRASH :
                        $controller = new MailPageController($arrUri, $slug);
                break;
                default :
                    $controller = new HomeController();
                break;
            }
            /////////////////////////////////////////
        }

        $errorPanel = '';
        if ($msgProcessError!='') {
            $errorPanel = $controller->getRender(TemplateConstant::TPL_SECTION_ERROR, [$msgProcessError]);
        }

        $attributes = [
            $controller->getTitle(),
            PLUGINS_COPS,
            $controller->getContentHeader(),
            $controller->getContentPage(),
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

            // new = confirm ?
            if ($newPassword==$confirmPassword && md5($oldPassword)==$player->getField(FieldConstant::PASSWORD)) {
                $player->setField(FieldConstant::PASSWORD, md5($newPassword));
                $player->update();
            } else {
                $msgProcessError = LabelConstant::LBL_ERR_MDPCHG;
            }

        } elseif ($formName=='dashboardSettings') {
            WidgetController::processForm();
        } else {
            $logname = SessionUtils::fromPost(FieldConstant::LOGNAME);
            $password = md5(SessionUtils::fromPost(FieldConstant::PASSWORD));
    
            $repository = new PlayerRepository(new PlayerCollection());
            if (strtolower($logname)=='guest') {
                $criteria = [
                    FieldConstant::LOGNAME => $logname,
                ];
            } else {
                $criteria = [
                    FieldConstant::LOGNAME => $logname,
                    FieldConstant::PASSWORD => $password,
                ];
            }
            $playerCollection = $repository->findBy($criteria);
        
            if ($playerCollection->length()==1) {
                $obj = $playerCollection->current();
                SessionUtils::setSession('copsSessionId', $obj->getField(FieldConstant::ID));
            } else {
                $msgProcessError = LabelConstant::LBL_ERR_LOGIN;
            }
    }
    }

}
CopsiteBase::display();
