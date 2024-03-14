<?php
namespace src\Controller;

use src\Collection\WidgetCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Repository\WidgetRepository;
use src\Utils\HtmlUtils;
use src\Utils\TableUtils;

class SettingsController extends UtilitiesController
{
    public function __construct()
    {
        parent::__construct();
        $this->title = LabelConstant::LBL_SETTINGS;
    }

    public function getContentPage(): string
    {
        $mdpCard = $this->getRender(TemplateConstant::TPL_NEW_PASSWORD_CARD);

        $repository = new WidgetRepository(new WidgetCollection());
        $widgets = $repository->findBy([FieldConstant::ACTIVE=>1]);

        $table = new TableUtils();
        $table->setTable([ConstantConstant::CST_CLASS=>'table-sm table-striped']);

        $table->addHeaderRow()
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'#'])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'Nom'])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'Largeur']);

        $table->addBodyRows($widgets, 3);

        $strInput = HtmlUtils::getInput([
            ConstantConstant::CST_TYPE=>'hidden',
            ConstantConstant::CST_NAME=>ConstantConstant::CST_FORMNAME,
            ConstantConstant::CST_VALUE=>'dashboardSettings'
        ]);
        $strButton = HtmlUtils::getBalise(
            'button',
            'Confirmer',
            [ConstantConstant::CST_CLASS=>'btn btn-secondary btn-sm', ConstantConstant::CST_TYPE=>'submit']
        );
        $strFootContent = HtmlUtils::getDiv(
            $strInput.$strButton,
            [ConstantConstant::CST_CLASS=>'d-flex justify-content-end']
        );

        $table->addFootRow()
            ->addFootCell(['attributes'=>['colspan'=>3], ConstantConstant::CST_CONTENT=>$strFootContent]);

        $attributes = [
            $table->display(),
        ];
        $settingsDashboard = $this->getRender(TemplateConstant::TPL_SETTINGS_DASH, $attributes);

        $attributes = [
            '4',
            $mdpCard,
            '8',
            $settingsDashboard,
        ];
        return $this->getRender(TemplateConstant::TPL_DASHBOARD_PANEL, $attributes);
    }
}
