<?php
namespace src\Form;

use src\Constant\ConstantConstant;
use src\Constant\IconConstant;
use src\Controller\UtilitiesController;
use src\Utils\HtmlUtils;

class Form extends UtilitiesController
{
    private array $formRows = [];
    private int $nbRows = -1;

    public function addRow(): void
    {
        $this->nbRows++;
        $this->formRows[$this->nbRows] = '';
    }

    private function getSpan(string $id, string $label, array $extraAttributes=[]): string
    {
        $attributes = [
            ConstantConstant::CST_CLASS =>'input-group-text '.($extraAttributes[ConstantConstant::CST_CLASS]??'col-2'),
            ConstantConstant::CST_FOR => $id,
        ];
        return HtmlUtils::getSpan($label, $attributes);
    }

    private function initAttributes(string $id, string $name, string $label, $value): array
    {
        return [
            ConstantConstant::CST_CLASS => 'form-control',
            ConstantConstant::CST_NAME => $name,
            ConstantConstant::CST_ID => $id,
            'aria-label' => $label,
            'aria-describedby' => $label,
            ConstantConstant::CST_VALUE => $value,
        ];
    }

    public function addBtnDelete(string $href, array $extraAttributes=[]): void
    {
        $icon = HtmlUtils::getIcon(IconConstant::I_TRASHALT);
        $link = HtmlUtils::getLink($icon, $href, 'text-white');
        $btn = HtmlUtils::getButton($link);
        $attributes = [
            ConstantConstant::CST_CLASS => $extraAttributes[ConstantConstant::CST_CLASS] ?? 'col-8',
        ];
        $this->formRows[$this->nbRows] .= $this->getSpan('', $btn, $attributes);
    }

    public function addInput(string $id, string $name, string $label, $value, array $extraAttributes=[]): void
    {
        $attributes = $this->initAttributes($id, $name, $label, $value);

        if (isset($extraAttributes[ConstantConstant::CST_CLASS])) {
            $attributes[ConstantConstant::CST_CLASS] .= ' '.$attributes[ConstantConstant::CST_CLASS];
        }
        $attributes[ConstantConstant::CST_TYPE] = $extraAttributes[ConstantConstant::CST_TYPE] ?? 'text';

        if (isset($extraAttributes[ConstantConstant::CST_READONLY])) {
            $attributes[ConstantConstant::CST_READONLY] = ConstantConstant::CST_READONLY;
        }
        if (isset($extraAttributes['required'])) {
            $attributes['required'] = 'required';
        }

        $this->formRows[$this->nbRows] .= $this->getSpan($id, $label).HtmlUtils::getBalise('input', '', $attributes);
    }

    public function addFileInput(string $id, string $label, $value, array $extraAttributes=[]): void
    {
        $extraAttributes[ConstantConstant::CST_TYPE] = ConstantConstant::CST_FILE;
        $this->addInput($id, $id, $label, $value, $extraAttributes);
    }

    public function addTextarea(string $id, string $label, $value, array $extraAttributes=[]): void
    {
        $attributes = $this->initAttributes($id, $id, $label, $value);
        $attributes['rows'] = $extraAttributes['rows'] ?? '5';

        $this->formRows[$this->nbRows] .= $this->getSpan($id, $label).HtmlUtils::getTextarea($value, $attributes);
    }

    public function addSelect(string $id, string $name, string $label, array $enumCases, $value): void
    {
        $content = '';
        while (!empty($enumCases)) {
            $element = array_shift($enumCases);
            if (is_array($element)) {
                $elementValue = $element[ConstantConstant::CST_VALUE];
                $elementLabel = $element[ConstantConstant::CST_LABEL];
            } else {
                $elementValue = $element->value;
                $elementLabel = $element->label();
            }
            $optAttributes = [ConstantConstant::CST_VALUE=>$elementValue];
            if ($elementValue==$value) {
                $optAttributes[ConstantConstant::CST_SELECTED] = ConstantConstant::CST_SELECTED;
            }
            $content .= HtmlUtils::getBalise('option', $elementLabel, $optAttributes);
        }

        $attributes = $this->initAttributes($id, $name, $label, $value);
        unset($attributes[ConstantConstant::CST_VALUE]);
        $strSelect = HtmlUtils::getBalise('select', $content, $attributes);

        $this->formRows[$this->nbRows] .= $this->getSpan($id, $label).$strSelect;
    }

    // TODO : removed attribute - , array $extraAttributes=[]
    // Check why it was there and why it isn't needed anymore
    public function addDataList(string $id, string $label, array $collection, $value): void
    {
        $this->formRows[$this->nbRows] .= $this->getSpan($id, $label);

        $attributes = $this->initAttributes($id, $id, $label, $value);
        $attributes['list'] = 'datalistOptions'.$id;

        $this->formRows[$this->nbRows] .= HtmlUtils::getBalise('input', '', $attributes);

        $dataListContent = '';
        while (!empty($collection)) {
            $element = array_shift($collection);
            $dataListContent .= HtmlUtils::getBalise('option', '', [ConstantConstant::CST_VALUE=>$element]);
        }
        $this->formRows[$this->nbRows] .= HtmlUtils::getBalise(
            'datalist',
            $dataListContent,
            [ConstantConstant::CST_ID=>'datalistOptions'.$id]
        );
    }

    public function addHidden(string $id, string $name, $value, array $extraAttributes=[]): void
    {
        $attributes = $this->initAttributes($id, $name, '', $value);

        if (isset($extraAttributes[ConstantConstant::CST_CLASS])) {
            $attributes[ConstantConstant::CST_CLASS] .= ' '.$attributes[ConstantConstant::CST_CLASS];
        }
        $attributes[ConstantConstant::CST_TYPE] = 'hidden';

        $this->formRows[$this->nbRows] .= HtmlUtils::getBalise('input', '', $attributes);
    }

    public function addFiller(array $extraAttributes=[]): void
    {
        $attributes = [
            ConstantConstant::CST_CLASS => $extraAttributes[ConstantConstant::CST_CLASS] ?? 'col-8',
        ];
        $this->formRows[$this->nbRows] .= $this->getSpan('', '', $attributes);
    }

    public function getFormContent(): string
    {
        $strContent = '';
        while (!empty($this->formRows)) {
            $row = array_shift($this->formRows);
            $strContent .= HtmlUtils::getDiv($row, [ConstantConstant::CST_CLASS=>'input-group mb-3']);
        }
        return $strContent;
    }

}
