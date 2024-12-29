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
        $this->contentHeader = 'Historique des binômes';
    }

    public function setPlayer(Player $player): self
    {
        $this->player = $player;
        return $this;
    }

    private function deleteBinome(): void
    {
        $binomeRepository = new BinomeRepository(new BinomeCollection);

        ////////////////////////////////////////////
        // Suppression d'un binôme
        $postedFormName = $_GET[ConstantConstant::CST_FORMNAME] ?? null;
        if ($postedFormName!=null) {
            $id = SessionUtils::fromGet('playerBinomeId');
            $binome = $binomeRepository->find($id);
            if ($binome!=null) {
                $binome->delete();
            }
        }
        ////////////////////////////////////////////
    }

    private function insertBinome(): void
    {
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
    }

    public function controlForm():void
    {
        $this->deleteBinome();
        $this->insertBinome();

        $binomeRepository = new BinomeRepository(new BinomeCollection);
        $playerId = $this->player->getField(FieldConstant::ID);

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

            $postLeaderId = isset($leaderIds[$id]) ? $leaderIds[$id] : $playerId;
            $binome->setField(FieldConstant::LEADERID, $postLeaderId);

            $postBinomeId = isset($binomeIds[$id]) ? $binomeIds[$id] : $playerId;
            $binome->setField(FieldConstant::BINOMEID, $postBinomeId);

            $postStartDate = $startDates[$id];
            $binome->setField(FieldConstant::STARTDATE, $postStartDate);

            $postEndDate = $endDates[$id];
            $binome->setField(FieldConstant::ENDDATE, $postEndDate);

            $this->binome->update();
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
            $selectList[] = [ConstantConstant::CST_VALUE=>$player->getField(FieldConstant::ID), ConstantConstant::CST_LABEL=>$player->getFullName()];
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

            $href = add_query_arg([ConstantConstant::CST_FORMNAME=>'copsPlayerBinome', 'playerBinomeId'=>$binomeId]);

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
        $this->addHidden(ConstantConstant::CST_FORMNAME, ConstantConstant::CST_FORMNAME, 'copsPlayerBinome');
    }

}
