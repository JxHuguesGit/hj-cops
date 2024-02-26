<?php
namespace src\Controller;

use src\Collection\PlayerSkillCollection;
use src\Collection\SkillCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\PlayerSkill;
use src\Repository\PlayerSkillRepository;
use src\Repository\SkillRepository;

class ProfileController extends UtilitiesController
{
    public function __construct(array $arrUri=[])
    {
        parent::__construct($arrUri);
        $this->title = LabelConstant::LBL_PROFILE;
    }

    private function addSkill(): void
    {
        // Pour le cas où on ajoute une compétence.
        $repository = new PlayerSkillRepository(new PlayerSkillCollection());
        $sRepository = new SkillRepository(new SkillCollection());
        // On doit ajouter la compétence $this->arrParams['skillId'] au personnage courant.
        // TODO : Envisager de pouvoir ajouter à n'importe quel personnage ?
        $attributes = [
            FieldConstant::PLAYERID=>$this->player->getField(FieldConstant::ID),
            FieldConstant::SKILLID=>$this->arrParams[FieldConstant::SKILLID],
        ];
        $playerSkill = $repository->findOneBy($attributes);

        if ($playerSkill==null) {
            // On récupère le Skill de skillId
            $skill = $sRepository->find($this->arrParams[FieldConstant::SKILLID]);
            if ($skill->getField(FieldConstant::SKILLID)==0) {
                // Si skillId vaut 0 alors score vaut 9
                $score = 9;
            } else {
                // Sinon on récupère le Skill de skillId
                $skillParent = $sRepository->find($skill->getField(FieldConstant::SKILLID));
                // score vaut specLevel du Skill de skillId
                $score = $skillParent->getField(FieldConstant::SPECLEVEL);
            }
            $attributes[FieldConstant::SCORE] = $score;
            $playerSkill = new PlayerSkill($attributes);
            $repository->insert($playerSkill);
        }
    }

    private function improveSkill(): void
    {
        // Pour le cas où on améliore une compétence.
        $repository = new PlayerSkillRepository(new PlayerSkillCollection());
        // On doit améliorer la compétence $this->arrParams['skillId'] au personnage courant.
        $attributes = [
            FieldConstant::PLAYERID=>$this->player->getField(FieldConstant::ID),
            FieldConstant::SKILLID=>$this->arrParams[FieldConstant::SKILLID],
        ];
        $playerSkill = $repository->findOneBy($attributes);
        if ($playerSkill!=null) {
            // TODO : faudrait vérifier que la compétence peut bien être améliorée.
            $score = $playerSkill->getField(FieldConstant::SCORE);
            $playerSkill->setField(FieldConstant::SCORE, $score-1);
            $repository->update($playerSkill);
        }
    }

    public function getContentPage(string $msgProcessError): string
    {
        if (isset($this->arrParams[ConstantConstant::CST_ACTION])) {
            // Pour le cas où une action est en cours.
            if ($this->arrParams[ConstantConstant::CST_ACTION]=='addskill') {
                $this->addSkill();
            } elseif ($this->arrParams[ConstantConstant::CST_ACTION]=='improveskill') {
                $this->improveSkill();
            } else {
                // Gérer le cas où la valeur dans acton n'est pas une attendue.
            }
        }

        $attributes = [
            $this->getCaracteristiquesCard(),
            $this->getCompetencesCard(),
        ];
        return $this->getRender(TemplateConstant::TPL_PROFILE_CARDS, $attributes);
    }

    public function getCompetencesCard(): string
    {
        /////////////////////////////////////////////
        // Récupération des compétences du personnage courant.
        $arrIds = [];
        $repository = new PlayerSkillRepository(new PlayerSkillCollection());
        $attributes = [FieldConstant::PLAYERID=>$this->player->getField(FieldConstant::ID)];
        $playerSkills = $repository->findByAndOrdered($attributes);
        $strLiSkillsCol1 = '';
        $strLiSkillsCol2 = '';
        $nbSkills = $playerSkills->length()+2;
        $cpt = 0;
        while ($playerSkills->valid()) {
            $playerSkill = $playerSkills->current();
            $skill = $playerSkill->getSkill();
            array_push($arrIds, $skill->getField(FieldConstant::ID));
            if ($cpt<=ceil($nbSkills/2)) {
                $strLiSkillsCol1 .= $skill->getController()->getFdpDiv($playerSkill->getField(FieldConstant::SCORE));
            } else {
                $strLiSkillsCol2 .= $skill->getController()->getFdpDiv($playerSkill->getField(FieldConstant::SCORE));
            }
            $playerSkills->next();
            ++$cpt;
        }
        /////////////////////////////////////////////

        /////////////////////////////////////////////
        // Récupération des compétences.
        $repository = new SkillRepository(new SkillCollection());
        $skills = $repository->findBy([], [FieldConstant::NAME=>'asc']);
        $strLiSkills = '';
        $strLisSpecs = '';
        $this->dispatchSkills($strLiSkills, $strLisSpecs, $skills, $arrIds, $playerSkills);
        /////////////////////////////////////////////

        $attributes = [
            $strLiSkillsCol1,// Liste des compétences du personnage courant, colonne n°1
            $strLiSkillsCol2,// Liste des compétences du personnage courant, colonne n°2
            $strLiSkills,// Liste des compétences sans parent que le personnage n'a pas encore et qui sont disponibles.
            $strLisSpecs,// Liste des spécialisations de compétences à 9+ que le personnage n'a pas encore et qui sont disponibles.
        ];
        //                                    <li><a class="dropdown-item" href="#">Action</a></li>

        return $this->getRender(TemplateConstant::TPL_PROFILE_SKILL_CARD, $attributes);
    }

    private function dispatchSkills(
        string &$strLiSkills,
        string &$strLisSpecs,
        array $skills,
        array $arrIds,
        PlayerSkillCollection $playerSkills
    ): void
    {
        while ($skills->valid()) {
            $skill = $skills->current();
            if (!in_array($skill->getField(FieldConstant::ID), $arrIds)) {
                $this->assignSkill($strLiSkills, $strLisSpecs, $skill);
            } else {
                $playerSkills->rewind();
                while ($playerSkills->valid()) {
                    $playerSkill = $playerSkills->current();
                    $score = $playerSkill->getField(FieldConstant::SCORE);
                    if (
                        $playerSkill->getField(FieldConstant::SKILLID)==$skill->getField(FieldConstant::ID) &&
                        $score==$skill->getField(FieldConstant::SPECLEVEL)+1
                    ) {
                        $this->assignSkill($strLiSkills, $strLisSpecs, $skill, $score);
                    }
                    $playerSkills->next();
                }
            }
            $skills->next();
        }
    }

    private function assignSkill(string &$strLiSkills, string &$strLisSpecs, Skill $skill, int $score=-1): void
    {
        if ($skill->getField(FieldConstant::SKILLID)==0) {
            // Récupération des compétences sans parent.
            $strLiSkills .= $skill->getController()->getLi($score);
        } else {
            // Récupération des compétences avec parent.
            $strLisSpecs .= $skill->getController()->getLi($score);
        }
    }

    public function getCaracteristiquesCard(): string
    {
        $attributes = [
            $this->player->getField(FieldConstant::ATTRCARRURE),
            $this->player->getField(FieldConstant::ATTRCHARME),
            $this->player->getField(FieldConstant::ATTRCOORDINATION),
            $this->player->getField(FieldConstant::ATTREDUCATION),
            $this->player->getField(FieldConstant::ATTRPERCEPTION),
            $this->player->getField(FieldConstant::ATTRREFLEXES),
            $this->player->getField(FieldConstant::ATTRSANGFROID),
            3-$this->player->getField(FieldConstant::ATTRREFLEXES),
            $this->player->getField(FieldConstant::HPMAX),
            $this->player->getField(FieldConstant::HPCUR),
            $this->player->getField(FieldConstant::ADMAX),
            $this->player->getField(FieldConstant::ADCUR),
            $this->player->getField(FieldConstant::ANMAX),
            $this->player->getField(FieldConstant::ANCUR),
        ];
        return $this->getRender(TemplateConstant::TPL_PROFILE_CARAC_CARD, $attributes);
    }
}
