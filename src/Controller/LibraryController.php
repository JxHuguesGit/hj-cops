<?php
namespace src\Controller;

use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Controller\CourseController;
use src\Controller\SkillController;
use src\Utils\HtmlUtils;
use src\Utils\TableUtils;

class LibraryController extends UtilitiesController
{
    public function __construct(array $arrUri=[])
    {
        parent::__construct($arrUri);
        $this->title = LabelConstant::LBL_LIBRARY;
    }

    public function getContentPage(): string
    {
        $attributes = [];
        if (!isset($this->arrParams[ConstantConstant::CST_PAGE])) {
            $returned = $this->getRender(TemplateConstant::TPL_LIBRARY_PANEL, $attributes);
        } else {
            $controller = null;
            switch ($this->arrParams[ConstantConstant::CST_PAGE]) {
                case ConstantConstant::CST_SKILLS :
                    $controller = new SkillController();
                    break;
                case ConstantConstant::CST_COURSES :
                    $controller = new CourseController();
                    break;
                case ConstantConstant::CST_PLAYERAID :
                    $returned = $this->getPlayerAidPage();
                    break;
                default :
                    $returned = $this->getRender(TemplateConstant::TPL_LIBRARY_PANEL, $attributes);
            }

            if ($controller!=null) {
                $returned = $controller->getContentPage($this->arrParams);
            }
        }
        return $returned;
    }

    public function getPlayerAidPage(): string
    {
        $strListFiles = '';

        // create a handler for the directory
        $urlBase = '/wp-content/plugins/hj-cops/assets/';
        $directory = $_SERVER['DOCUMENT_ROOT'].$urlBase.'pdf/';
        $handler = opendir($directory);

        // open directory and walk through the filenames
        while ($file = readdir($handler)) {

            // if file isn't this directory or its parent, add it to the results
            if ($file != "." && $file != ".." && preg_match('#^\[ADJ]_COPS_(.*)\.(pdf)#', $file, $matches)) {
                $title = str_replace('_', ' ', $matches[1]);
                $ext = $matches[2];
                $imgSrc = $urlBase.'images/svg/'.$ext.'.svg';
                $strImg = HtmlUtils::getBalise('img', '', ['alt'=>'icon', 'src'=>$imgSrc]);
                $strFib = HtmlUtils::getDiv($strImg, ['class'=>'file-img-box']);
                $strIcon = HtmlUtils::getIcon('download');
                $href = $urlBase.$ext.'/'.$file;
                $strA = HtmlUtils::getLink($strIcon, $href, 'file-download');
                $strH5 = HtmlUtils::getBalise('h5', $title, ['class'=>'mb-0 text-overflow']);
                $size = filesize($_SERVER['DOCUMENT_ROOT'].$href);
                if ($size<1000) {
                    $strSize = $size.'o';
                } elseif ($size<1000000) {
                    $strSize = round($size/1000, 2).'ko';
                } elseif ($size<1000000000) {
                    $strSize = round($size/1000000, 2).'Mo';
                } else {
                    $strSize = round($size/1000000000, 2).'Go';
                }
                $strP = HtmlUtils::getBalise('p', '<small>'.$strSize.'</small>', ['class'=>'mb-0']);
                $strTitle = HtmlUtils::getDiv($strH5.$strP, ['class'=>'file-man-title']);

                $content = HtmlUtils::getDiv($strFib.$strA.$strTitle, ['class'=>'file-man-box']);
                $strListFiles .= HtmlUtils::getDiv($content, ['class'=>'col-lg-3 col-xl-2']);
            }
        }

        $attributes = [$strListFiles];
        return $this->getRender(TemplateConstant::TPL_ADJ_PANEL, $attributes);
    }

}
