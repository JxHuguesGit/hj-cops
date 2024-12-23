<?php
namespace src\Controller;

use src\Collection\SkillCollection;
use src\Constant\ConstantConstant;
use src\Constant\CssConstant;
use src\Constant\FieldConstant;
use src\Constant\IconConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\Skill;
use src\Repository\SkillRepository;
use src\Utils\HtmlUtils;
use src\Utils\TableUtils;
use src\Utils\UrlUtils;

class SkillController extends UtilitiesController
{
    private Skill $skill;

    public function __construct(Skill $skill=null)
    {
        $this->skill = $skill ?? new Skill();
    }

    public function getContentPage($arrParams): string
    {
        $this->arrParams = $arrParams;
        
        $repository = new SkillRepository(new SkillCollection());
        $skills = $repository->findBy([FieldConstant::SKILLID=>0], [FieldConstant::NAME=>ConstantConstant::CST_ASC]);

        $table = new TableUtils();
        $table->setTable([ConstantConstant::CST_CLASS=>CssConstant::CSS_TABLE_SM.' '.CssConstant::CSS_TABLE_STRIPED]);
        $table->setPaginate([
            ConstantConstant::PAGE_OBJS => $skills,
            ConstantConstant::CST_CURPAGE => $this->arrParams[ConstantConstant::CST_CURPAGE] ?? 1,
            ConstantConstant::CST_URL => UrlUtils::getPublicUrl(
                ConstantConstant::CST_LIBRARY,
                [ConstantConstant::CST_PAGE=>ConstantConstant::CST_SKILLS]
            ),
        ]);

        $table->addHeaderRow()
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'#'])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'Nom'])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'Adrénaline'])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'Caractéristique']);

        $table->addBodyRows($skills, 4);

        $viewCard = '';
        if (isset($this->arrParams[ConstantConstant::CST_ACTION]) &&
            $this->arrParams[ConstantConstant::CST_ACTION]==ConstantConstant::CST_VIEW) {
            $repository = new SkillRepository(new SkillCollection());
            $skill = $repository->find($this->arrParams[ConstantConstant::CST_ID]);
            if ($skill!=null) {
                $viewCard = $skill->getController()->getCard();
            }
        }

        $attributes = [
            LabelConstant::LBL_SKILLS,
            $table->display(),
            $viewCard,
        ];
        return $this->getRender(TemplateConstant::TPL_LIBRARY_SUBPANEL, $attributes);
    }

    public function getLi(int $score=-1): string
    {
        return HtmlUtils::getLi(
            HtmlUtils::getLink(
                $this->skill->getField(FieldConstant::NAME),
                UrlUtils::getPublicUrl(
                    ConstantConstant::CST_PROFILE,
                    [ConstantConstant::CST_ACTION=>'addskill', FieldConstant::SKILLID=>$this->skill->getField(FieldConstant::ID)]
                ),
                'dropdown-item ajaxAction',
                [
                    'data-speclevel'    => $this->skill->getField(FieldConstant::SPECLEVEL),
                    'data-skillid'      => $this->skill->getField(FieldConstant::ID),
                    'data-parentid'     => $this->skill->getField(FieldConstant::SKILLID),
                    'data-score'        => $score,
                    'data-trigger'      => 'click',
                    'data-ajax'         => 'skillCreation',
                ]
            )
        );
    }

    public function getFdpDiv(int $score): string
    {
        if ($this->skill->getField(FieldConstant::SKILLID)==0) {
            // La compétence n'a pas de parent
            $firstLabel = $this->skill->getField(FieldConstant::NAME);
            $specLevel = $this->skill->getField(FieldConstant::SPECLEVEL);
            $secondLabel = '';
        } else {
            // La compétence a un parent
            $skillParent = $this->skill->getParentSkill();
            $firstLabel = $skillParent->getField(FieldConstant::NAME);
            $secondLabel = $this->skill->getField(FieldConstant::NAME);
            $specLevel = $this->skill->getField(FieldConstant::SPECLEVEL);
        }

        $attributes = [
            ConstantConstant::CST_CLASS    => 'form-control col-2',
            'aria-label'                   => 'Score',
            'aria-describedby'             => 'Score',
            ConstantConstant::CST_READONLY => ConstantConstant::CST_READONLY,
            ConstantConstant::CST_VALUE    => $score,
        ];
        $divContent  = HtmlUtils::getSpan($firstLabel, [ConstantConstant::CST_CLASS=>"input-group-text col-6"]);
        $divContent .= HtmlUtils::getSpan($secondLabel, [ConstantConstant::CST_CLASS=>"input-group-text col-3"]);
        $divContent .= HtmlUtils::getInput($attributes);
        if ($score>2 && $score>$specLevel+1) {
            $aContent = HtmlUtils::getButton(
                HtmlUtils::getIcon(IconConstant::I_CARETDOWN),
                [ConstantConstant::CST_CLASS=>'py-0 border-0']
            );
            $linkAttributes = [
                ConstantConstant::CST_ACTION => 'improveskill',
                FieldConstant::SKILLID       => $this->skill->getField(FieldConstant::ID)
            ];
            $spanContent = HtmlUtils::getLink(
                $aContent,
                UrlUtils::getPublicUrl(ConstantConstant::CST_PROFILE, $linkAttributes)
            );
        } else {
            $spanContent = '';
        }
        $divContent .= HtmlUtils::getSpan($spanContent, [ConstantConstant::CST_CLASS=>"input-group-text col-1"]);
        return HtmlUtils::getDiv($divContent, [ConstantConstant::CST_CLASS=>"input-group input-group-sm mb-1"]);
    }

    public function addBodyRow(TableUtils &$table): void
    {
        $id = $this->skill->getField(FieldConstant::ID);
        $strLink = HtmlUtils::getLink(
            $this->skill->getField(FieldConstant::NAME),
            UrlUtils::getPublicUrl(
                ConstantConstant::CST_LIBRARY,
                [
                    ConstantConstant::CST_PAGE   => ConstantConstant::CST_SKILLS,
                    ConstantConstant::CST_ACTION => ConstantConstant::CST_VIEW,
                    ConstantConstant::CST_ID     => $id
                ]
            )
        );
        $bgColor = 'btn btn-sm disabled btn-';
        if ($this->skill->getField(FieldConstant::PADUSABLE)) {
            $strIcon = HtmlUtils::getIcon(IconConstant::I_CHECK);
            $bgColor .= 'success';
        } else {
            $strIcon = HtmlUtils::getIcon(IconConstant::I_TIMES);
            $bgColor .= 'danger';
        }
        $strSpan = HtmlUtils::getSpan($strIcon, [ConstantConstant::CST_CLASS=>$bgColor]);

        $table->addBodyRow()
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$id])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$strLink])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$strSpan])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$this->skill->getField(FieldConstant::DEFAULTABILITY)]);
    }

    public function getCard(): string
    {
        $adrenaline = $this->skill->getField(FieldConstant::PADUSABLE);
        $specLevel = $this->skill->getField(FieldConstant::SPECLEVEL);
        $strSpecs = '';
        if ($specLevel!=0) {
            $repository = new SkillRepository(new SkillCollection());
            $skills = $repository->findBy([FieldConstant::SKILLID=>$this->skill->getField(FieldConstant::ID)]);
            while ($skills->valid()) {
                $skill = $skills->current();
                $strSpecs .= $skill->getField(FieldConstant::NAME).", ";
                $skills->next();
            }
            $strSpecs = substr($strSpecs, 0, -2).'.';
        }

        $attributes = [
            $this->skill->getField(FieldConstant::NAME),
            $this->skill->getField(FieldConstant::DESCRIPTION),
            $adrenaline==0 ? 'bg-danger' : 'bg-success',
            $adrenaline==0 ? 'Non' : 'Oui',
            $this->skill->getField(FieldConstant::REFERENCE),
            $this->skill->getField(FieldConstant::DEFAULTABILITY),
            $specLevel==0 ? 'bg-danger' : 'bg-success',
            $specLevel==0 ? 'Non' : $specLevel.'+',
            $specLevel==0 ? 'hidden' : '',
            $specLevel==0 ? '' : $strSpecs,
            $this->skill->getField(FieldConstant::USES),
        ];
        return $this->getRender(TemplateConstant::TPL_SKILL_FORM, $attributes);
    }
}
