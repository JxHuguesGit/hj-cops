<?php
namespace src\Controller;

use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Constant\IconConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Controller\AcronymController;
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
        $arrCards = [
            ConstantConstant::CST_SKILLS => [
                'label'=>'Compétences',
                'description'=>'Liste et description des compétences.',
                'file'=>'COPS_Compétences.pdf'],
            ConstantConstant::CST_COURSES => [
                'label'=>'Stages',
                'description'=>'Liste et description des stages.',
                'file'=>'COPS_Stages.pdf'],
            ConstantConstant::CST_PLAYERAID => [
                'label'=>'Aides de jeu',
                'description'=>'Diverses aides de jeu, fluff et ingame.',
                'file'=>''],
            ConstantConstant::CST_ACRONYMS => [
                'label'=>'Acronymes',
                'description'=>'Tous les acronymes du jeu.',
                'file'=>''],
        ];
        if (!isset($this->arrParams[ConstantConstant::CST_PAGE]) ||
            !in_array($this->arrParams[ConstantConstant::CST_PAGE], array_keys($arrCards))
        ) {
            $cardBody = '';
            $cardRow = '';
            $cpt = 0;
            foreach ($arrCards as $key => $data) {
                    if ($cpt%4==0 && $cpt!=0) {
                            $cardBody .= HtmlUtils::getDiv($cardRow, [ConstantConstant::CST_CLASS=>'row mb-3']);
                            $cardRow = '';
                    }
                    $cpt++;
                    $cardAttributes = [
                            $key,
                            $data['label'],
                            $data['description'],
                            $data['file']=='' ? 'd-none' : '',
                            $data['file'],
                    ];
                    $cardRow .= $this->getRender(TemplateConstant::TPL_LIBRARY_CARD, $cardAttributes);
            }
            $cardBody .= HtmlUtils::getDiv($cardRow, [ConstantConstant::CST_CLASS=>'row mb-3']);
            $returned = $this->getRender(TemplateConstant::TPL_LIBRARY_PANEL, [$cardBody]);
        } else {
            $controller = null;
            switch ($this->arrParams[ConstantConstant::CST_PAGE]) {
                case ConstantConstant::CST_SKILLS :
                    $controller = new SkillController();
                    $returned = $controller->getContentPage($this->arrParams);
                    break;
                case ConstantConstant::CST_COURSES :
                    $controller = new CourseController();
                    $returned = $controller->getContentPage($this->arrParams);
                    break;
                case ConstantConstant::CST_ACRONYMS :
                    $controller = new AcronymController();
                    $returned = $controller->getContentPage($this->arrParams);
                    break;
                case ConstantConstant::CST_PLAYERAID :
                    $returned = $this->getPlayerAidPage();
                    break;
                default :
                    $returned = '';
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
                $ext = $matches[2];
                $strFib = HtmlUtils::getDiv(
                    HtmlUtils::getBalise(
                        'img',
                        '',
                        ['alt'=>ConstantConstant::CST_ICON, 'src'=>$urlBase.'images/svg/'.$ext.'.svg']
                    ),
                    [ConstantConstant::CST_CLASS=>'file-img-box']
                );
                $href = $urlBase.$ext.'/'.$file;
                $strA = HtmlUtils::getLink(
                    HtmlUtils::getIcon(IconConstant::I_DOWNLOAD),
                    $href,
                    'file-download'
                );
                $strH5 = HtmlUtils::getBalise(
                    'h5',
                    str_replace('_', ' ', $matches[1]),
                    [ConstantConstant::CST_CLASS=>'mb-0 text-overflow']
                );
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
                $strP = HtmlUtils::getBalise(
                    'p',
                    '<small>'.$strSize.'</small>',
                    [ConstantConstant::CST_CLASS=>'mb-0']
                );
                $strTitle = HtmlUtils::getDiv(
                    $strH5.$strP,
                    [ConstantConstant::CST_CLASS=>'file-man-title']
                );

                $content = HtmlUtils::getDiv(
                    $strFib.$strA.$strTitle,
                    [ConstantConstant::CST_CLASS=>'file-man-box']
                );
                $strListFiles .= HtmlUtils::getDiv($content, [ConstantConstant::CST_CLASS=>'col-lg-3 col-xl-2']);
            }
        }

        $attributes = [$strListFiles];
        return $this->getRender(TemplateConstant::TPL_ADJ_PANEL, $attributes);
    }

}
