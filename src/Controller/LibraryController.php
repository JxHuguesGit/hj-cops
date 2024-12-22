<?php
namespace src\Controller;

use src\Collection\PlayerAidCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Constant\IconConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Controller\AcronymController;
use src\Controller\CourseController;
use src\Controller\SkillController;
use src\Repository\PlayerAidRepository;
use src\Utils\FileUtils;
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
        // Définition du contenu accessible dans la bibliothèque
        $arrCards = [
            ConstantConstant::CST_ACRONYMS => [
                ConstantConstant::CST_LABEL=>LabelConstant::LBL_ACRONYMS,
                ConstantConstant::CST_DESCRIPTION=>'Tous les acronymes du jeu.',
                ConstantConstant::CST_FILE=>'[ADJ]_COPS_Acronymes.pdf'],
            ConstantConstant::CST_PLAYERAID => [
                ConstantConstant::CST_LABEL=>LabelConstant::LBL_GAME_AIDS,
                ConstantConstant::CST_DESCRIPTION=>'Diverses aides de jeu, fluff et ingame.'],
            ConstantConstant::CST_COPS => [
                ConstantConstant::CST_LABEL=>LabelConstant::LBL_COPS,
                ConstantConstant::CST_DESCRIPTION=>'Liste des COPS en service.'],
            ConstantConstant::CST_SKILLS => [
                ConstantConstant::CST_LABEL=>LabelConstant::LBL_SKILLS,
                ConstantConstant::CST_DESCRIPTION=>'Liste et descriptions des compétences.',
                ConstantConstant::CST_FILE=>'[ADJ]_COPS_Compétences.pdf'],
            ConstantConstant::CST_COURSES => [
                ConstantConstant::CST_LABEL=>LabelConstant::LBL_COURSES,
                ConstantConstant::CST_DESCRIPTION=>'Liste et descriptions des stages.',
                ConstantConstant::CST_FILE=>'[ADJ]_COPS_Stages.pdf'],
            ];

        // Si on ne regarde pas une page en particulier
        // ou si la page souhaitée n'existe pas
        if (!isset($this->arrParams[ConstantConstant::CST_PAGE]) ||
            !in_array($this->arrParams[ConstantConstant::CST_PAGE], array_keys($arrCards))
        ) {
            // On affiche la liste des pages disponibles dans la bibliothèque
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
                            $data[ConstantConstant::CST_LABEL],
                            $data[ConstantConstant::CST_DESCRIPTION],
                            !isset($data[ConstantConstant::CST_FILE]) ? 'd-none' : '',
                            $data[ConstantConstant::CST_FILE] ?? '',
                    ];
                    $cardRow .= $this->getRender(TemplateConstant::TPL_LIBRARY_CARD, $cardAttributes);
            }
            $cardBody .= HtmlUtils::getDiv($cardRow, [ConstantConstant::CST_CLASS=>'row mb-3']);
            $returned = $this->getRender(TemplateConstant::TPL_LIBRARY_PANEL, [$cardBody]);
        } else {
            // Sinon, on récupère le controller associé à la page souhaitée.
            $controller = null;
            switch ($this->arrParams[ConstantConstant::CST_PAGE]) {
                case ConstantConstant::CST_ACRONYMS :
                    $controller = new AcronymController();
                    $returned = $controller->getContentPage($this->arrParams);
                    break;
                case ConstantConstant::CST_COPS :
                    $controller = new CopsController();
                    $returned = $controller->getContentPage($this->arrParams);
                    break;
                case ConstantConstant::CST_COURSES :
                    $controller = new CourseController();
                    $returned = $controller->getContentPage($this->arrParams);
                    break;
                case ConstantConstant::CST_PLAYERAID :
                    $returned = $this->getPlayerAidPage();
                    break;
                case ConstantConstant::CST_SKILLS :
                    $controller = new SkillController();
                    $returned = $controller->getContentPage($this->arrParams);
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

        $repository = new PlayerAidRepository(new PlayerAidCollection());
        $playerAids = $repository->findAll();

        // create a handler for the directory
        $urlBase = '/wp-content/plugins/hj-cops/assets/';
        $directory = $_SERVER['DOCUMENT_ROOT'].$urlBase.'pdf/';

        while ($playerAids->valid()) {
            $playerAid = $playerAids->current();
            $urlFile = $directory.$playerAid->getField(FieldConstant::CODE).'.pdf';
            $file = new FileUtils($urlFile);
            $divContent = $file->setLabel($playerAid->getField(FieldConstant::NAME))->getPlayerAidCard();
            $strListFiles .= HtmlUtils::getDiv($divContent, [ConstantConstant::CST_CLASS=>'col-lg-3 col-xl-2']);
            $playerAids->next();
        }

        $attributes = [$strListFiles];
        return $this->getRender(TemplateConstant::TPL_ADJ_PANEL, $attributes);
    }

}
