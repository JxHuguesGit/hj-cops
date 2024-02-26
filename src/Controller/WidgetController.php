<?php
namespace src\Controller;

use src\Collection\WidgetCollection;
use src\Collection\SkillCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\PlayerWidget;
use src\Entity\Widget;
use src\Repository\WidgetRepository;
use src\Repository\SkillRepository;
use src\Utils\CardUtils;
use src\Utils\HtmlUtils;
use src\Utils\SessionUtils;
use src\Utils\TableUtils;

class WidgetController extends UtilitiesController
{
    private Widget $widget;

    public function __construct(Widget $widget=null)
    {
        $this->widget = $widget ?? new Widget();
    }


    public function addBodyRow(TableUtils &$table, array $arrParams=[]): void
    {
        $player = SessionUtils::getPlayer();
        $playerWidget = $player->getWidget($this->widget->getField(FieldConstant::ID));
        $slug = $this->widget->getField(FieldConstant::SLUG);

        $attributes = [
            ConstantConstant::CST_CLASS => 'form-check-input',
            ConstantConstant::CST_TYPE => 'checkbox',
            'role' => 'switch',
            ConstantConstant::CST_ID => $slug.'Panel',
            ConstantConstant::CST_NAME => $slug.'Panel',
        ];
        if ($playerWidget!=null && $playerWidget->getField(FieldConstant::ACTIVE)==1) {
            $attributes[ConstantConstant::CST_CHECKED] = ConstantConstant::CST_CHECKED;
        }
        $strInput = HtmlUtils::getInput($attributes);
        $strDivInput = HtmlUtils::getDiv($strInput, [ConstantConstant::CST_CLASS=>'form-check form-switch']);

        $strDivs = '';
        $width = $playerWidget!=null ? $playerWidget->getField(FieldConstant::WIDTH) : 1;
        for ($i=1; $i<=3; $i++) {
            $attributes = [
                ConstantConstant::CST_CLASS => 'form-check-input',
                ConstantConstant::CST_TYPE => 'radio',
                ConstantConstant::CST_ID => $slug.'Width'.$i,
                ConstantConstant::CST_NAME => $slug.'Width',
                ConstantConstant::CST_VALUE => $i,
            ];
            if ($i==$width) {
                $attributes[ConstantConstant::CST_CHECKED] = ConstantConstant::CST_CHECKED;
            }
            $strInput = HtmlUtils::getInput($attributes);

            $strLabel = HtmlUtils::getSpan(
                $i,
                [ConstantConstant::CST_CLASS => 'form-check-label', 'for'=>$slug.'Width'.$i]
            );
            $strDivs .= HtmlUtils::getDiv(
                $strInput.$strLabel,
                [ConstantConstant::CST_CLASS=>'form-check form-check-inline']
            );
        }

        $table->addBodyRow()
            ->addBodyCell(['content'=>$strDivInput])
            ->addBodyCell(['content'=>$this->widget->getField(FieldConstant::NAME)])
            ->addBodyCell(['content'=>$strDivs]);

    }

    public function getCard(bool $hideCateg=false): string
    {
        return '';
    }

    public function getForm(bool $hideCateg=false): string
    {
        return '';
    }

    public static function processForm(): void
    {
        $player = SessionUtils::getPlayer();

        $repository = new WidgetRepository(new WidgetCollection());
        $widgets = $repository->findBy([FieldConstant::ACTIVE=>1]);

        while ($widgets->valid()) {
            $widget = $widgets->current();
            $playerWidget = $player->getWidget($widget->getField(FieldConstant::ID));

            $slug = $widget->getField(FieldConstant::SLUG);
            $active = SessionUtils::fromPost($slug.'Panel', 'off')=='on' ? 1 : 0;
            $width = SessionUtils::fromPost($slug.'Width', 1);

            if ($playerWidget==null) {
                $playerWidget = new PlayerWidget([
                    FieldConstant::PLAYERID => $player->getField(FieldConstant::ID),
                    FieldConstant::WIDGETID => $widget->getField(FieldConstant::ID),
                    FieldConstant::ACTIVE => $active,
                    FieldConstant::WIDTH => $width,
                    FieldConstant::COLOR => '',
                    FieldConstant::POS => 1,
                ]);
                $playerWidget->insert();
            } else {
                $playerWidget->setField(FieldConstant::ACTIVE, $active);
                $playerWidget->setField(FieldConstant::WIDTH, $width);
                $playerWidget->update();
            }

            $widgets->next();
        }

    }
}
