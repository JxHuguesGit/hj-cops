<?php
namespace src\Utils;

use src\Constant\ConstantConstant;

class FilterUtils
{
    private string $label;
    private string $field;
    private array $values;
    private int $col;
    private string $selected;
    private string $url;

    public function __construct(array $arrData=[])
    {
        /////////////////////////////////////////////////
        // Paramètres optionnels
        $this->col = $arrData[ConstantConstant::CST_COL] ?? 0;
        $this->selected = $arrData[ConstantConstant::CST_SELECTED] ?? -1;
        /////////////////////////////////////////////////

        /////////////////////////////////////////////////
        // Paramètres obligatoires
        $this->label = $arrData[ConstantConstant::CST_LABEL] ?? '';
        $this->field = $arrData[ConstantConstant::CST_FIELD] ?? '';
        $this->values = $arrData[ConstantConstant::CST_VALUES] ?? '';
        /////////////////////////////////////////////////

        /////////////////////////////////////////////////
        // Construction de url
        $this->url = remove_query_arg('filter'.$this->field);
        /////////////////////////////////////////////////
    }

    public function getFilterBlock(): string
    {
        $strLabel = $this->selected!='' ? ucwords($this->values[$this->selected]) : $this->label;
        $strBtn = HtmlUtils::getButton(
            $strLabel,
            [ConstantConstant::CST_CLASS=>'dropdown-toggle', 'data-bs-toggle'=>'dropdown']
        );

        $ulContent = '';
        foreach($this->values as $key => $value) {
            $aContent = $value=='' ? '-' : ucwords($value);
            $href = add_query_arg('filter'.$this->field, $key, $this->url);
            $liContent = HtmlUtils::getLink($aContent, $href, 'dropdown-item');
            $ulContent .= HtmlUtils::getBalise('li', $liContent);
       }
        $strUl = HtmlUtils::getBalise('ul', $ulContent, [ConstantConstant::CST_CLASS=>'dropdown-menu']);

        return HtmlUtils::getDiv($strBtn.$strUl, [ConstantConstant::CST_CLASS=>'input-group']);
    }

}
