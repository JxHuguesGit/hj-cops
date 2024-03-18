<?php
namespace src\Controller;

use src\Constant\ConstantConstant;
use src\Utils\HtmlUtils;
use src\Utils\UrlUtils;

class PageController extends UtilitiesController
{
    protected $menu = [];
    protected $slug = '';

    public function __construct($arrUri=[], string $slug='')
    {
        parent::__construct($arrUri);
        $this->slug = $slug;
    }

    public function getAdminContentPage(): string
    {
        switch ($this->arrParams[ConstantConstant::CST_ONGLET]) {
            case 'admin' :
                $controller = new AdminPageController();
            break;
            case 'mail' :
                $controller = new MailPageController();
            break;
            case 'mailPlayer' :
                $controller = new MailPlayerPageController();
            break;
            case 'mailFolder' :
                $controller = new MailFolderPageController();
            break;
            default :
                $controller = new MailDataPageController();
            break;
        }
        $controller->setParams($this->arrParams);
        return $controller->getAdminContentPage();
    }

    public function getTabsBar(): string
    {
        /////////////////////////////////////////
        // Construction des onglets
        $strLis = '';
        foreach ($this->arrTabs as $slug => $arrData) {
            $urlAttributes = [ConstantConstant::CST_ONGLET=>$slug];
            $strIcon = '';

            if (!empty($arrData[ConstantConstant::CST_ICON])) {
                $strIcon = HtmlUtils::getIcon($arrData[ConstantConstant::CST_ICON]).' ';
            }
            $blnActive = $this->getArrParams(ConstantConstant::CST_ONGLET)==$slug;
            $strLink = HtmlUtils::getLink(
                $strIcon.$arrData[ConstantConstant::CST_LABEL],
                UrlUtils::getAdminUrl($urlAttributes),
                'nav-link text-white'
            );

            $strLis .= HtmlUtils::getBalise(
                'li',
                $strLink,
                [ConstantConstant::CST_CLASS=>'nav-item'.($blnActive ? ' bg-secondary' : ' bg-dark')]
            );
        }
        $attributes = [ConstantConstant::CST_CLASS=>implode(' ', ['nav', 'nav-pills', 'nav-fill'])];
        /////////////////////////////////////////

        return HtmlUtils::getBalise('ul', $strLis, $attributes);
    }
}
