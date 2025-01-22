<?php
namespace src\Controller;

use src\Collection\ChronologieCollection;
use src\Constant\ConstantConstant;
use src\Constant\CssConstant;
use src\Constant\FieldConstant;
use src\Constant\IconConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\Chronologie;
use src\Form\ChronologieForm;
use src\Repository\ChronologieRepository;
use src\Utils\CardUtils;
use src\Utils\DateUtils;
use src\Utils\HtmlUtils;
use src\Utils\SessionUtils;
use src\Utils\TableUtils;
use src\Utils\UrlUtils;

class ChronologieController extends UtilitiesController
{
    private Chronologie $chronologie;

    public function __construct(array $arrUri=[])
    {
        parent::__construct($arrUri);
        $this->title = '';//LabelConstant::LBL_ENQUETES;
    }

    public function setChronologie(Chronologie $chronologie): self
    {
        $this->chronologie = $chronologie;
        return $this;
    }
/*

    public static function processForm(string &$msgProcessError): void
    {
        $investigation = new Investigation($_POST);
        $investigation->getForm()->controlForm($msgProcessError);
    }

    public function getContentPage(): string
    {
        $msgProcessError = '';
        $formName = SessionUtils::fromPost(ConstantConstant::CST_FORMNAME);
        if ($formName=='updateInvestigation') {
            InvestigationController::processForm($msgProcessError);
        }

        if (!isset($this->arrParams[ConstantConstant::CST_PAGE])) {
            // Sur la vue globale, pas de page en particulier
            $returned =  $this->getListing();
        } else {
            $page = $this->arrParams[ConstantConstant::CST_PAGE];
            if ($page==ConstantConstant::CST_CREATE) {
                $investigation = new Investigation();
                $investigation->initAttributes();
                $investigationForm = new InvestigationForm($investigation);
                $returned = $investigationForm
                    ->setContentHeader("Ouverture d'une nouvelle enquête")
                    ->buildForm()
                    ->getFormContent();
            } elseif ($page==ConstantConstant::CST_EDIT) {
                $id = SessionUtils::fromGet(FieldConstant::ID);
                $repository = new InvestigationRepository(new InvestigationCollection());
                $investigation = $repository->find($id);
                if ($investigation==null) {
                    $returned = $this->getListing();
                } else {
                    $investigationForm = new InvestigationForm($investigation);
                    $returned = $investigationForm
                        ->setContentHeader("Modification d'une enquête")
                        ->buildForm()
                        ->getFormContent();
                }
            } else {
                $returned = $this->getListing();
            }
        }

        return $returned;
    }
*/
    public function addBodyRow(TableUtils &$table, array $arrParams=[]): void
    {
        ///////////////////////////////////////
        $dateEvent = $this->chronologie->getField(FieldConstant::DATE_EVENT);
        list($y, $m, $d) = explode('-', $dateEvent);
        $strDate = DateUtils::getStrDate(($d=='00'?'M y':'d M y'), [$d, $m, $y, 0, 0, 0]);
        $hourEvent = $this->chronologie->getField(FieldConstant::HOUR_EVENT);
        list($h, $i,) = explode(':', $hourEvent);
        $strHour = $h.'h'.$i;
        $strDateHour = $strDate.' '.$strHour;
        ///////////////////////////////////////

        $chronoEvent = $this->chronologie->getField(FieldConstant::CHRONO_EVENT);

        ///////////////////////////////////////
        $confirmUrl = UrlUtils::getPublicUrl(ConstantConstant::CST_INVESTIGATION, [
            ConstantConstant::CST_PAGE       => ConstantConstant::CST_EDIT,
            ConstantConstant::CST_ID         => $this->chronologie->getField(FieldConstant::INVESTIGATIONID),
            ConstantConstant::CST_ACTIVE_TAB => 'timing',
            ConstantConstant::CST_CHRONO_ID  => $this->chronologie->getField(FieldConstant::ID),
            ConstantConstant::CST_ACTION     => ConstantConstant::CST_DELETE,
            ConstantConstant::CST_CONFIRM    => 1,
        ]);

        $aIcon = HtmlUtils::getIcon(IconConstant::I_TRASHALT);
        $actions  = HtmlUtils::getButton(
            $aIcon,
            [
                ConstantConstant::CST_CLASS => CssConstant::CSS_BG_SECONDARY.' '.CssConstant::CSS_TEXT_WHITE.' ajaxAction',
                'data-trigger' => 'click',
                'data-ajax' => 'openConfirmModal',
                'data-title' => "Suppresion d'un événement de chronologie",
                'data-message' => "Voulez-vous vraiment supprimer cet événement de chronologie ?<p>".$this->chronologie->getField(FieldConstant::CHRONO_EVENT)."</p>",
                'data-href' => $confirmUrl,
            ]
        );
        ///////////////////////////////////////


        $table->addBodyRow($arrParams)
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$strDateHour])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$chronoEvent])
            ->addBodyCell([
                ConstantConstant::CST_CONTENT=>$actions,
                ConstantConstant::CST_ATTRIBUTES=>[
                    ConstantConstant::CST_CLASS=>CssConstant::CSS_TEXT_END
                ]
            ]);
    }
/*
    private function getListing(): string
    {
        $repository = new InvestigationRepository(new InvestigationCollection());
        $investigations = $repository->findAll();
        
        if ($investigations->length()==0) {
            $attributes = ['<p>Aucune enquête en cours.</p>'];
        } else {
            $table = new TableUtils();
            $table->setTable([ConstantConstant::CST_CLASS => CssConstant::CSS_TABLE_SM.' '.CssConstant::CSS_TABLE_STRIPED])
                ->addHeaderRow()
                ->addHeaderCell([ConstantConstant::CST_CONTENT=>'#'])
                ->addHeaderCell([ConstantConstant::CST_CONTENT=>LabelConstant::LBL_NAME])
                ->addHeaderCell([ConstantConstant::CST_CONTENT=>LabelConstant::LBL_FIRST_DETECTIVE])
                ->addHeaderCell([ConstantConstant::CST_CONTENT=>LabelConstant::LBL_DISTRICT_ATTORNEY])
                ->addBodyRows($investigations, 4);

            $attributes = [$table->display()];
        }
        
        return $this->getRender(TemplateConstant::TPL_INVESTIGATION_PANEL, $attributes);
    }
*/

}
