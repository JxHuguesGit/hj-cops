<?php
namespace src\Controller;

use src\Collection\MailDataCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Constant\IconConstant;
use src\Constant\TemplateConstant;
use src\Entity\Player;
use src\Exception\TemplateException;
use src\Repository\MailDataRepository;
use src\Utils\HtmlUtils;
use src\Utils\SessionUtils;
use src\Utils\UrlUtils;

class UtilitiesController
{
    protected array $arrParams=[];
    protected bool $isLogged;
    protected string $title;
    protected Player $player;
    protected string $breadCrumbsContent = '';

    public function __construct(array $arrUri=[])
    {
        $this->isLogged = SessionUtils::isLogged();
        $this->player = SessionUtils::getPlayer() ?? new Player();

        if (isset($arrUri[2]) && !empty($arrUri[2])) {
            if (strpos($arrUri[2], '?')!==false) {
                $params = substr($arrUri[2], strpos($arrUri[2], '?')+1);
            } else {
                $params = $arrUri[2];
            }
            if (isset($arrUri[3]) && substr($arrUri[3], 0, 12)=='admin_manage') {
                $params .= '/'.$arrUri[3];
            }
            $arrParams = explode('&', $params);
            while (!empty($arrParams)) {
                $param = array_shift($arrParams);
                list($key, $value) = explode('=', $param);
                $this->arrParams[str_replace('amp;', '', $key)] = $value;
            }
        }
    }

    public function getArrParams(string $key): mixed
    {
        return $this->arrParams[$key] ?? '';
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setParams(array $params=[]): self
    {
        $this->arrParams = $params;
        return $this;
    }

    public function setBreadCrumbsContent(): string
    {
        return HtmlUtils::getButton(
            HtmlUtils::getLink(
                HtmlUtils::getIcon(IconConstant::I_DESKTOP),
                UrlUtils::getAdminUrl(),
                'text-white',
            ),
            [ConstantConstant::CST_CLASS=>'btn-secondary']
        );
    }







    public static function getAdminController(array $arrUri): mixed
    {
        $controller = new UtilitiesController($arrUri);
        if (substr($controller->getArrParams(ConstantConstant::CST_ONGLET), 0, 4)=='mail' ||
			$controller->getArrParams(ConstantConstant::CST_ONGLET)=='admin'
		) {
            $controller = new PageController($arrUri);
        } else {
            $controller = new HomePageController($arrUri);
        }
        return $controller;
    }
    
    public function getRender(string $urlTemplate, array $args=[]): string
    {
        if (file_exists(PLUGIN_PATH.$urlTemplate)) {
            return vsprintf(file_get_contents(PLUGIN_PATH.$urlTemplate), $args);
        } else {
            throw new TemplateException($urlTemplate);
        }
    }

    public function getContentFooter()
    {
        if ($this->isLogged) {
            return $this->getRender(TemplateConstant::TPL_FOOTER);
        } else {
            return '';
        }
    }

    public function getContentHeader()
    {
        if ($this->isLogged) {
            if ($this->getPlayer()->getField(FieldConstant::ID)==ConstantConstant::CST_ID_GUEST) {
                $strMask   = 'mask-4';
                $menuClass = 'hidden';
                $strNotifIcons = '';
            } else {
                $strMask   = 'mask-4';
                $menuClass = '';
                $strNotifIcons = '';

                ////////////////////////////////////////////////////////////////
                // Notifications
                $searchAttributes = [
                    FieldConstant::READ=>0,
                    FieldConstant::FOLDERID=>ConstantConstant::CST_ALERT
                ];
                $mailNotifs = $this->getPlayer()->getMailData($searchAttributes);

                $strIcon = HtmlUtils::getIcon(IconConstant::I_BELL);
                if ($mailNotifs->valid()) {
                    $strIcon .= HtmlUtils::getSpan(
                        $mailNotifs->length(),
                        [ConstantConstant::CST_CLASS=>'badge bg-teal']
                    );
                }
                $strLink = HtmlUtils::getLink($strIcon, UrlUtils::getPublicUrl(ConstantConstant::CST_NOTIFICATION));
                $strBtn  = HtmlUtils::getButton($strLink, [ConstantConstant::CST_CLASS=>'text-white']);
                $strNotifIcons .= HtmlUtils::getLi(
                    $strBtn,
                    [ConstantConstant::CST_CLASS=>'nav-item', 'id'=>'header_notification_bar']
                );
                ////////////////////////////////////////////////////////////////
            }

            $attributes = [
                UrlUtils::getAssetUrl('images'),
                $this->getPlayer()->getFullName(),
                $strMask,
                $menuClass,
                $strNotifIcons,
            ];
            return $this->getRender(TemplateConstant::TPL_HEADER, $attributes);
        } else {
            return '';
        }
    }

    public function getClassLogged(): string
    {
        return $this->isLogged ? '' : 'notlogged';
    }

}
