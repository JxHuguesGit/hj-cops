<?php
namespace src\Utils;

use src\Constant\ConstantConstant;
use src\Constant\IconConstant;

class FileUtils
{
    private $fileName;
    private $label;

    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function getPlayerAidCard(): string
    {
        $urlBase = '/wp-content/plugins/hj-cops/assets/';
        $ext = 'pdf';

        $strFib = HtmlUtils::getDiv(
            HtmlUtils::getBalise(
                'img',
                '',
                ['alt'=>ConstantConstant::CST_ICON, 'src'=>$urlBase.'images/svg/'.$ext.'.svg']
            ),
            [ConstantConstant::CST_CLASS=>'file-img-box']
        );

        $strA = HtmlUtils::getLink(
            HtmlUtils::getIcon(IconConstant::I_DOWNLOAD),
            $this->fileName,
            'file-download'
        );

        $strH5 = HtmlUtils::getBalise(
            'h5',
            str_replace('_', ' ', $this->label),
            [ConstantConstant::CST_CLASS=>'mb-0 text-overflow']
        );
        $strP = HtmlUtils::getBalise(
            'p',
            '<small>'.$this->getStrSize().'</small>',
            [ConstantConstant::CST_CLASS=>'mb-0']
        );
        $strTitle = HtmlUtils::getDiv(
            $strH5.$strP,
            [ConstantConstant::CST_CLASS=>'file-man-title']
        );

        return HtmlUtils::getDiv(
            $strFib.$strA.$strTitle,
            [ConstantConstant::CST_CLASS=>'file-man-box']
        );

    }

    private function getStrSize(): string
    {
        $strSize = '';
        $size = filesize($this->fileName);
        if ($size<1000) {
            $strSize = $size.'o';
        } elseif ($size<1000000) {
            $strSize = round($size/1000, 2).'ko';
        } elseif ($size<1000000000) {
            $strSize = round($size/1000000, 2).'Mo';
        } else {
            $strSize = round($size/1000000000, 2).'Go';
        }
        return $strSize;
    }
}
