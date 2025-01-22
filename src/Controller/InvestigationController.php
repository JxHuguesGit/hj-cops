<?php
namespace src\Controller;

use src\Collection\InvestigationCollection;
use src\Constant\ConstantConstant;
use src\Constant\CssConstant;
use src\Constant\FieldConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\Investigation;
use src\Form\InvestigationForm;
use src\Repository\InvestigationRepository;
use src\Utils\HtmlUtils;
use src\Utils\SessionUtils;
use src\Utils\TableUtils;
use src\Utils\UrlUtils;

class InvestigationController extends UtilitiesController
{
    private Investigation $investigation;

    public function __construct(array $arrUri=[])
    {
        parent::__construct($arrUri);
        $this->title = LabelConstant::LBL_ENQUETES;
    }

    public function setInvestigation(Investigation $investigation): self
    {
        $this->investigation = $investigation;
        return $this;
    }

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

    public function addBodyRow(TableUtils &$table, array $arrParams=[]): void
    {
        ///////////////////////////////////////:
        $id = $this->investigation->getField(FieldConstant::ID);
        $linkAttributes = [
            ConstantConstant::CST_PAGE => 'edit',
            ConstantConstant::CST_ID => $id,
        ];
        $strId     = HtmlUtils::getLink(
            $id,
            UrlUtils::getPublicUrl(ConstantConstant::CST_INVESTIGATION, $linkAttributes)
        ) ;
        ///////////////////////////////////////:

        ///////////////////////////////////////:
        $firstDetective = $this->investigation->getFirstDetective();
        if ($firstDetective==null) {
            $strFirstDetective = 'Non renseigné';
        } else {
            $strFirstDetective = $firstDetective->getFullName();
        }
        ///////////////////////////////////////:
        
        ///////////////////////////////////////:
        $districtAttorney = $this->investigation->getDistrictAttorney();
        if ($districtAttorney==null) {
            $strDistrictAttorney = 'Non renseigné';
        } else {
            $strDistrictAttorney = $districtAttorney->getNameAndOccupation(false);
        }
        ///////////////////////////////////////:

        $strName = $this->investigation->getField(FieldConstant::NAME);

        $table->addBodyRow($arrParams)
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$strId])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$strName])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$strFirstDetective])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$strDistrictAttorney]);
    }

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


}
