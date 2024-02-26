<?php
namespace src\Utils;

use src\Constant\ConstantConstant;

class HtmlUtils
{
    public static function getBalise(string $balise, string $label='', array $attributes=[]): string
    {
        if ($balise=='input') {
            return '<'.$balise.static::getExtraAttributesString($attributes).'/>';
        } else {
            return '<'.$balise.static::getExtraAttributesString($attributes).'>'.$label.'</'.$balise.'>';
        }
    }

    public static function getExtraAttributesString(array $attributes): string
    {
        $extraAttributes = '';
        // Si la liste des attributs n'est pas vide
        if (!empty($attributes)) {
            foreach ($attributes as $key => $value) {
                // Si l'attribut est un tableau
                if (is_array($value)) {
                    foreach ($value as $subkey => $subvalue) {
                        // On construit sur le modèle key-subkey="value"
                        $extraAttributes .= ' '.$key.'-'.$subkey.'="'.$subvalue.'"';
                    }
                } else {
                    // On construit sur le modèle key="value"
                    $extraAttributes .= ' '.$key.'="'.$value.'"';
                }
            }
        }
        return $extraAttributes;
    }



    public static function getButton(string $label, array $extraAttributes=[]): string
    {
        // Les attributs par défaut d'un bouton.
        $defaultAttributes = [
            ConstantConstant::CST_TYPE => 'button',
            ConstantConstant::CST_CLASS => 'btn btn-default btn-sm'
        ];
        if (isset($extraAttributes[ConstantConstant::CST_CLASS])) {
            $defaultAttributes[ConstantConstant::CST_CLASS] .= ' '.$extraAttributes[ConstantConstant::CST_CLASS];
            unset($extraAttributes[ConstantConstant::CST_CLASS]);
        }
        $attributes = array_merge($defaultAttributes, $extraAttributes);
        return static::getBalise('button', $label, $attributes);
    }

    public static function getDiv(string $content, array $extraAttributes=[]): string
    {
        return static::getBalise('div', $content, $extraAttributes);
    }

    public static function getIcon(string $icon): string
    {
        $strClass = 'fa-solid fa-'.$icon;
        return static::getBalise('i', '', [ConstantConstant::CST_CLASS=>$strClass]);
    }

    public static function getLi(string $content, array $extraAttributes=[]): string
    {
        return static::getBalise('li', $content, $extraAttributes);
    }

    public static function getLink(string $label, string $href, string $classe='', array $extraAttributes=[]): string
    {
        $attributes = array_merge(
            [ConstantConstant::CST_HREF => $href, ConstantConstant::CST_CLASS => $classe],
            $extraAttributes
        );
        return static::getBalise('a', $label, $attributes);
    }

    public static function getSpan(string $content, array $extraAttributes=[]): string
    {
        return static::getBalise('span', $content, $extraAttributes);
    }

    public static function getInput(array $extraAttributes=[]): string
    {
        $attributes = array_merge(
            [ConstantConstant::CST_TYPE => 'text'],
            $extraAttributes
        );
        return static::getBalise('input', '', $attributes);
    }
}