<?php
namespace src\Form;

use src\Collection\BinomeCollection;
use src\Collection\PlayerCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Constant\LabelConstant;
use src\Entity\Binome;
use src\Entity\Player;
use src\Repository\BinomeRepository;
use src\Repository\PlayerRepository;
use src\Utils\CardUtils;
use src\Utils\HtmlUtils;
use src\Utils\SessionUtils;
use src\Utils\UrlUtils;

class BinomeForm extends Form
{
    public Player $player;
    public Binome $binome;

    public function __construct(Binome $binome=null)
    {
        $this->binome = $binome ?? new Binome();
    }

    public function setPlayer(Player $player): self
    {
        $this->player = $player;
        return $this;
    }

    public function controlForm():void
    {
        $binomeRepository = new BinomeRepository(new BinomeCollection);

        ////////////////////////////////////////////
        // Suppression d'un binôme
        $postedFormName = $_GET['formName'] ?? null;
        if ($postedFormName!=null) {
            $id = SessionUtils::fromGet('playerBinomeId');
            $binome = $binomeRepository->find($id);
            if ($binome!=null) {
                $binome->delete();
            }
        }
        ////////////////////////////////////////////

        ////////////////////////////////////////////
        // Création d'un nouveau binôme
        $playerId = $this->player->getField(FieldConstant::ID);
        $newBinomeId = SessionUtils::fromPost('new'.FieldConstant::BINOMEID);
        $newStartDate = SessionUtils::fromPost('new'.FieldConstant::STARTDATE);
        $newEndDate = SessionUtils::fromPost('new'.FieldConstant::ENDDATE);

        if ($newStartDate!='' && $newEndDate!='' && $newBinomeId!=$playerId) {
            $binome = new Binome();
            $binome->setField(FieldConstant::LEADERID, $playerId);
            $binome->setField(FieldConstant::BINOMEID, $newBinomeId);
            $binome->setField(FieldConstant::STARTDATE, $newStartDate);
            $binome->setField(FieldConstant::ENDDATE, $newEndDate);
            // Création d'une nouvelle entrée
            $binome->insert();
        }
        ////////////////////////////////////////////

        ////////////////////////////////////////////
        // Récupération des données du formulaire
        $binomeIds = SessionUtils::fromTabPost(FieldConstant::BINOMEID);
        $leaderIds = SessionUtils::fromTabPost(FieldConstant::LEADERID);
        $ids = SessionUtils::fromTabPost(FieldConstant::ID);
        $startDates = SessionUtils::fromTabPost(FieldConstant::STARTDATE);
        $endDates = SessionUtils::fromTabPost(FieldConstant::ENDDATE);
        ////////////////////////////////////////////
        
        while (!empty($ids)) {
            $id = array_shift($ids);
            $binome = $binomeRepository->find($id);

            // Donc on a un _POST qui vient d'être soumis. On vérifie les différents champs et leurs valeurs.
            // En cas d'erreur, on rajoute des flags d'erreur.
            $blnErrors = false;
            // Si aucune erreur, on met à jour les données en base et on les utilise pour l'affichage.
            $blnUpdate = false;

            $postLeaderId = isset($leaderIds[$id]) ? $leaderIds[$id] : $playerId;
            if ($binome->getField(FieldConstant::LEADERID)!=$postLeaderId) {
                $blnUpdate = true;
                $binome->setField(FieldConstant::LEADERID, $postLeaderId);
            }
            $postBinomeId = isset($binomeIds[$id]) ? $binomeIds[$id] : $playerId;
            if ($binome->getField(FieldConstant::BINOMEID)!=$postBinomeId) {
                $blnUpdate = true;
                $binome->setField(FieldConstant::BINOMEID, $postBinomeId);
            }
            $postStartDate = $startDates[$id];
            if ($binome->getField(FieldConstant::STARTDATE)!=$postStartDate) {
                $blnUpdate = true;
                $binome->setField(FieldConstant::STARTDATE, $postStartDate);
            }
            $postEndDate = $endDates[$id];
            if ($binome->getField(FieldConstant::ENDDATE)!=$postEndDate) {
                $blnUpdate = true;
                $binome->setField(FieldConstant::ENDDATE, $postEndDate);
            }

            if ($blnUpdate && !$blnErrors) {
                $this->binome->update();
            }
        }
    }

    public function buildForm(): void
    {
        $binomes = $this->player->getBinomes();
        $this->playerRepository = new PlayerRepository(new PlayerCollection());
        $players = $this->playerRepository->findAll();
        $selectList = [];
        while ($players->valid()) {
            $player = $players->current();
            $selectList[] = ['value'=>$player->getField(FieldConstant::ID), 'label'=>$player->getFullName()];
            $players->next();
        }

        while ($binomes->valid()) {
            $binome = $binomes->current();
            $binomeId = $binome->getField(FieldConstant::ID);
            if ($binome->getField(FieldConstant::LEADERID)==$this->player->getField(FieldConstant::ID)) {
                $cops = $binome->getBinome();
                $field = FieldConstant::BINOMEID;
            } else {
                $cops = $binome->getLeader();
                $field = FieldConstant::LEADERID;
            }

            $href = add_query_arg(['formName'=>'copsPlayerBinome', 'playerBinomeId'=>$binomeId]);

            // binome, startDate, endDate
            $this->addRow();
            $this->addSelect($field.'_'.$binomeId, $field.'['.$binomeId.']', LabelConstant::LBL_BINOME, $selectList, $cops->getField(FieldConstant::ID));
            $this->addHidden(FieldConstant::ID.'_'.$binomeId, 'id['.$binomeId.']', $binomeId);
            $this->addInput(FieldConstant::STARTDATE.'_'.$binomeId, FieldConstant::STARTDATE.'['.$binomeId.']', 'Début', $binome->getField(FieldConstant::STARTDATE), [ConstantConstant::CST_TYPE=>'date']);
            $this->addInput(FieldConstant::ENDDATE.'_'.$binomeId, FieldConstant::ENDDATE.'['.$binomeId.']', 'Fin', $binome->getField(FieldConstant::ENDDATE), [ConstantConstant::CST_TYPE=>'date']);
            $this->addBtnDelete($href, [ConstantConstant::CST_CLASS=>'col-1 bg-danger']);
    
            $binomes->next();
        }
        // Nouveau binôme
        $this->addRow();
        $this->addSelect('new'.FieldConstant::BINOMEID, 'new'.FieldConstant::BINOMEID, LabelConstant::LBL_BINOME, $selectList, '');
        $this->addInput('new'.FieldConstant::STARTDATE, 'new'.FieldConstant::STARTDATE, 'Début', '', [ConstantConstant::CST_TYPE=>'date']);
        $this->addInput('new'.FieldConstant::ENDDATE, 'new'.FieldConstant::ENDDATE, 'Fin', '', [ConstantConstant::CST_TYPE=>'date']);
        $this->addFiller([ConstantConstant::CST_CLASS=>'col-1']);

        $this->addRow();
        $this->addHidden('formName', 'formName', 'copsPlayerBinome');
    }

    public function getFormContent(): string
    {
        $formContent = parent::getFormContent();
        $btnSubmit = HtmlUtils::getButton(LabelConstant::LBL_SUBMIT, [ConstantConstant::CST_TYPE=>'submit']);

        $card = new CardUtils([ConstantConstant::CST_STYLE=>'max-width:initial']);
        $card->addClass('p-0')
            ->setHeader([ConstantConstant::CST_CONTENT=>'Historique des binômes'])
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
}
