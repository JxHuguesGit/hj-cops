<?php
namespace src\Utils;

use src\Constant\ConstantConstant;
use src\Utils\HtmlUtils;

class CardUtils
{
    protected array $attributes;
    protected string $cardClass;

    public function __construct()
    {
        $this->cardClass = 'card';
        $this->attributes = [
            'header' => [
                ConstantConstant::CST_CLASS => 'card-header',
                'content' => '',
            ],
            'body' => [
                ConstantConstant::CST_CLASS => 'card-body',
                'content' => '',
            ],
            'footer' => [
                ConstantConstant::CST_CLASS => 'card-footer',
                'content' => '',
            ],

        ];
    }

    public function addClass(string $strClass): self
    {
        $this->cardClass .= ' '.$strClass;
        return $this;
    }

    public function setHeader(array $attributes): self
    {
        if (isset($attributes['content'])) {
            $this->attributes['header']['content'] .= $attributes['content'];
        }
        if (isset($attributes[ConstantConstant::CST_CLASS])) {
            $this->attributes['header'][ConstantConstant::CST_CLASS] .= $attributes[ConstantConstant::CST_CLASS];
        }

        return $this;
    }

    public function setBody(array $attributes): self
    {
        if (isset($attributes['content'])) {
            $this->attributes['body']['content'] .= $attributes['content'];
        }
        if (isset($attributes[ConstantConstant::CST_CLASS])) {
            $this->attributes['body'][ConstantConstant::CST_CLASS] .= $attributes[ConstantConstant::CST_CLASS];
        }

        return $this;
    }

    public function setFooter(array $attributes): self
    {
        if (isset($attributes['content'])) {
            $this->attributes['footer']['content'] .= $attributes['content'];
        }
        if (isset($attributes[ConstantConstant::CST_CLASS])) {
            $this->attributes['footer'][ConstantConstant::CST_CLASS] .= $attributes[ConstantConstant::CST_CLASS];
        }

        return $this;
    }

    public function display(): string
    {
        $header = HtmlUtils::getDiv(
            $this->attributes['header']['content'],
            [ConstantConstant::CST_CLASS=>$this->attributes['header'][ConstantConstant::CST_CLASS]]
        );
        $body = HtmlUtils::getDiv(
            $this->attributes['body']['content'],
            [ConstantConstant::CST_CLASS=>$this->attributes['body'][ConstantConstant::CST_CLASS]]
        );
        $footer = HtmlUtils::getDiv(
            $this->attributes['footer']['content'],
            [ConstantConstant::CST_CLASS=>$this->attributes['footer'][ConstantConstant::CST_CLASS]]
        );

        return HtmlUtils::getDiv($header.$body.$footer, [ConstantConstant::CST_CLASS=>$this->cardClass]);
    }
}
