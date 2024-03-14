<?php
namespace src\Controller;

use src\Entity\PlayerWidget;


use src\Collection\SkillCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Repository\SkillRepository;
use src\Utils\CardUtils;
use src\Utils\HtmlUtils;
use src\Utils\TableUtils;

class PlayerWidgetController extends UtilitiesController
{
    private PlayerWidget $playerWidget;

    public function __construct(PlayerWidget $playerWidget=null)
    {
        $this->playerWidget = $playerWidget ?? new PlayerWidget();
    }

    public function displayCard(): string
    {
        if ($this->playerWidget->getField(FieldConstant::ACTIVE)==0) {
            return '';
        }
        $widget = $this->playerWidget->getWidget();

        if ($widget==null || $widget->getField(FieldConstant::ACTIVE)==0) {
            return '';
        }
        $slug = $widget->getField(FieldConstant::SLUG);
        $width = $this->playerWidget->getField(FieldConstant::WIDTH);

        $card = new CardUtils();
        if ($slug=='weather') {
            $attributes = [
                ConstantConstant::CST_CLASS => 'wicon icon-xl',
                'data-icon' => 1,
            ];
            $strIcon = HtmlUtils::getBalise('i', '', $attributes);
            $strTemp = HtmlUtils::getBalise('h2', '11º C');
            $body = HtmlUtils::getDiv($strIcon.$strTemp, [ConstantConstant::CST_CLASS=>'weather-panel pn rounded']);
            //
            $card->addClass('bg-transparent border-0')
                ->setHeader([ConstantConstant::CST_CLASS=>' d-none'])
                ->setBody([ConstantConstant::CST_CONTENT=>$body, ConstantConstant::CST_CLASS=>' p-0 m-0'])
                ->setFooter([ConstantConstant::CST_CLASS=>' d-none']);
        } else {
            $card->addClass('bg-'.$this->playerWidget->getField(FieldConstant::COLOR))
                ->setHeader([ConstantConstant::CST_CONTENT=>'Header'])
                ->setBody([ConstantConstant::CST_CONTENT=>'Body'])
                ->setFooter([ConstantConstant::CST_CONTENT=>'Footer']);
        }
        return '<div class="col-md-'.(4*$width).' col-sm-'.(4*$width).' mb-1">'.$card->display().'</div>';

        
    }
}
