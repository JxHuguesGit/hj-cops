<?php
namespace src\Form;

use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Constant\LabelConstant;
use src\Entity\Player;
use src\Enum\RankEnum;
use src\Enum\SectionEnum;
use src\Utils\CardUtils;
use src\Utils\HtmlUtils;
use src\Utils\SessionUtils;

class PlayerForm extends Form
{
    public Player $player;

    public function __construct(Player $player=null)
    {
        $this->player = $player ?? new Player();
    }

    public function buildForm(): void
    {
        // Id, logname et mot de passe
        $this->addRow();
        $this->addInput(FieldConstant::ID, FieldConstant::ID, 'Id', $this->player->getField(FieldConstant::ID), [ConstantConstant::CST_READONLY=>true]);
        $this->addInput(FieldConstant::LOGNAME, FieldConstant::LOGNAME, LabelConstant::LBL_ID, $this->player->getField(FieldConstant::LOGNAME));
        $this->addInput(FieldConstant::PASSWORD, FieldConstant::PASSWORD, LabelConstant::LBL_MDP, $this->player->getField(FieldConstant::PASSWORD));

        // Lastname, firstname et surname
        $this->addRow();
        $this->addInput(FieldConstant::LASTNAME, FieldConstant::LASTNAME, LabelConstant::LBL_NAME, $this->player->getField(FieldConstant::LASTNAME));
        $this->addInput(FieldConstant::FIRSTNAME, FieldConstant::FIRSTNAME, LabelConstant::LBL_FIRSTNAME, $this->player->getField(FieldConstant::FIRSTNAME));
        $this->addInput(FieldConstant::SURNAME, FieldConstant::SURNAME, LabelConstant::LBL_SURNAME, $this->player->getField(FieldConstant::SURNAME));

        // Serialnumber, startDate, endDate
        $this->addRow();
        $this->addInput(FieldConstant::SERIALNUMBER, FieldConstant::SERIALNUMBER, LabelConstant::LBL_MATRICULE, $this->player->getField(FieldConstant::SERIALNUMBER));
        $this->addInput(FieldConstant::STARTDATE, FieldConstant::STARTDATE, LabelConstant::LBL_INTEGRATION_DATE, $this->player->getField(FieldConstant::STARTDATE), [ConstantConstant::CST_TYPE=>'date']);
        $this->addInput(FieldConstant::ENDDATE, FieldConstant::ENDDATE, LabelConstant::LBL_END_SHIFT_DATE, $this->player->getField(FieldConstant::ENDDATE), [ConstantConstant::CST_TYPE=>'date']);

        // Grade, échelon, section
        $this->addRow();
        $this->addSelect(FieldConstant::RANK, FieldConstant::RANK, LabelConstant::LBL_GRADE, RankEnum::cases(), $this->player->getField(FieldConstant::RANK));
        $this->addInput(FieldConstant::RANKECHELON, FieldConstant::RANKECHELON, LabelConstant::LBL_ECHELON, $this->player->getField(FieldConstant::RANKECHELON));
        $this->addSelect(FieldConstant::SECTION, FieldConstant::SECTION, LabelConstant::LBL_SECTION, SectionEnum::cases(), $this->player->getField(FieldConstant::SECTION));

        $this->addRow();
        $this->addHidden('formName', 'formName', 'copsPlayer');
    }

    public function getFormContent(): string
    {
        $formContent = parent::getFormContent();
        $btnSubmit = HtmlUtils::getButton(LabelConstant::LBL_SUBMIT, [ConstantConstant::CST_TYPE=>'submit']);

        $card = new CardUtils([ConstantConstant::CST_STYLE=>'max-width:initial']);
        $card->addClass('p-0')
            ->setHeader([ConstantConstant::CST_CONTENT=>'Informations Cops'])
            ->setBody([ConstantConstant::CST_CONTENT=>$formContent])
            ->setFooter([ConstantConstant::CST_CONTENT=>$btnSubmit]);

        return HtmlUtils::getBalise(
            'form',
            $card->display(),
            [
                'method'=>'post',
                ConstantConstant::CST_CLASS=>'col-6'
            ]
        );
    }

    public function controlForm(): void
    {
        // Donc on a un _POST qui vient d'être soumis. On vérifie les différents champs et leurs valeurs.
        // En cas d'erreur, on rajoute des flags d'erreur.
        $blnErrors = false;
        // Si aucune erreur, on met à jour les données en base et on les utilise pour l'affichage.
        $blnUpdate = false;

        // Les champs modifiables pour le moment
        // rank
        $postRank = SessionUtils::fromPost(FieldConstant::RANK);
        if ($this->player->getField(FieldConstant::RANK)!=$postRank) {
            $blnUpdate = true;
            $this->player->setField(FieldConstant::RANK, $postRank);
        }
        // rankEchelon
        $postRankEchelon = SessionUtils::fromPost(FieldConstant::RANKECHELON);
        if ($this->player->getField(FieldConstant::RANKECHELON)!=$postRankEchelon) {
            $blnUpdate = true;
            $this->player->setField(FieldConstant::RANKECHELON, $postRankEchelon);
        }
        // section
        $postSection = SessionUtils::fromPost(FieldConstant::SECTION);
        if ($this->player->getField(FieldConstant::SECTION)!=$postSection) {
            $blnUpdate = true;
            $this->player->setField(FieldConstant::SECTION, $postSection);
        }
        // startDate
        $postStartDate = SessionUtils::fromPost(FieldConstant::STARTDATE);
        if ($this->player->getField(FieldConstant::STARTDATE)!=$postStartDate) {
            $blnUpdate = true;
            $this->player->setField(FieldConstant::STARTDATE, $postStartDate);
        }
        // endDate
        $postEndDate = SessionUtils::fromPost(FieldConstant::ENDDATE);
        if ($this->player->getField(FieldConstant::ENDDATE)!=$postEndDate) {
            $blnUpdate = true;
            $this->player->setField(FieldConstant::ENDDATE, $postEndDate);
        }

        if ($blnUpdate && !$blnErrors) {
            $this->player->update();
        }
    }
}
