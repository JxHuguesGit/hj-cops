<?php
namespace src\Controller;

use src\Constant\ConstantConstant;
use src\Constant\TemplateConstant;

class AdminPageController extends PageController
{
    protected $contentHeader;
    protected $tabsBar;

    public function getAdminContentPage(): string
    {
        switch ($this->arrParams[ConstantConstant::CST_ONGLET]) {
            case ConstantConstant::CST_RANDOMGUY :
                $controller = new AdminRandomGuyPageController();
            break;
            case ConstantConstant::CST_CODE :
                $controller = new AdminCodePageController();
            break;
            case ConstantConstant::CST_BDD :
            default :
                $controller = new AdminBddPageController();
            break;
        }
        $controller->setParams($this->arrParams);
        return $controller->getAdminContentPage();
    }

}
