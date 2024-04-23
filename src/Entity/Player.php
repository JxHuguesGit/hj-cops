<?php
namespace src\Entity;

use src\Collection\BinomeCollection;
use src\Collection\MailDataCollection;
use src\Collection\MailFolderCollection;
use src\Collection\MailPlayerCollection;
use src\Collection\PlayerCollection;
use src\Collection\PlayerWidgetCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Controller\CopsController;
use src\Entity\MailPlayer;
use src\Entity\PlayerWidget;
use src\Repository\BinomeRepository;
use src\Repository\MailDataRepository;
use src\Repository\MailFolderRepository;
use src\Repository\MailPlayerRepository;
use src\Repository\PlayerRepository;
use src\Repository\PlayerWidgetRepository;
use src\Utils\DateUtils;

class Player extends Entity
{
    //////////////////////////////////////////////////
    // ATTRIBUTES
    //////////////////////////////////////////////////
    protected int $id;
    protected string $logname;
    protected string $password;
    protected string $serialnumber;
    protected string $lastname;
    protected string $firstname;
    protected string $surname;
    protected int $attrCarrure;
    protected int $attrCharme;
    protected int $attrCoordination;
    protected int $attrEducation;
    protected int $attrPerception;
    protected int $attrReflexes;
    protected int $attrSangfroid;
    protected int $hpMax;
    protected int $hpCur;
    protected int $adMax;
    protected int $adCur;
    protected int $anMax;
    protected int $anCur;
    protected int $xpCumul;
    protected int $xpCur;
    protected string $rank;
    protected int $rankEchelon;
    protected string $section;
    protected string $startDate;

    protected PlayerRepository $repository;
    protected BinomeRepository $binomeRepository;

    //////////////////////////////////////////////////
    // CONSTRUCT
    //////////////////////////////////////////////////
    public function __construct(array $attributes=[])
    {
        parent::__construct($attributes);
        $this->initRepositories();
    }

    private function initRepositories()
    {
        $this->repository = new PlayerRepository(new PlayerCollection());
        $this->binomeRepository = new BinomeRepository(new BinomeCollection());
    }

    public static function initFromRow($row): Player
    {
        $obj = new Player();
        $fields = $obj->getFields();
        foreach ($fields as $field) {
            $obj->setField($field, $row->{$field});
        }
        return $obj;
    }

    public static function getFields(): array
    {
        return [
            FieldConstant::ID,
            FieldConstant::ADCUR,
            FieldConstant::ADMAX,
            FieldConstant::ANCUR,
            FieldConstant::ANMAX,
            FieldConstant::ATTRCARRURE,
            FieldConstant::ATTRCHARME,
            FieldConstant::ATTRCOORDINATION,
            FieldConstant::ATTREDUCATION,
            FieldConstant::ATTRPERCEPTION,
            FieldConstant::ATTRREFLEXES,
            FieldConstant::ATTRSANGFROID,
            FieldConstant::FIRSTNAME,
            FieldConstant::HPCUR,
            FieldConstant::HPMAX,
            FieldConstant::LASTNAME,
            FieldConstant::LOGNAME,
            FieldConstant::PASSWORD,
            FieldConstant::RANK,
            FieldConstant::RANKECHELON,
            FieldConstant::SECTION,
            FieldConstant::SERIALNUMBER,
            FieldConstant::STARTDATE,
            FieldConstant::ENDDATE,
            FieldConstant::SURNAME,
            FieldConstant::XPCUMUL,
            FieldConstant::XPCUR,
        ];
    }

    public function isGuest(): bool
    {
        return $this->id==64;
    }

    public function getFullName(bool $withSurname=false): string
    {
        return $this->lastname . ' ' .
            (!$withSurname || $this->surname==' ' ? '' : '"'.$this->surname.'" ').
            $this->firstname;
    }

    public function update(): void
    {
        $this->initRepositories();
        $this->repository->update($this);
    }

    public function getController(): CopsController
    {
        return new CopsController($this);
    }

    public function getWidgets(): PlayerWidgetCollection
    {
        $repository = new PlayerWidgetRepository(new PlayerWidgetCollection());
        return $repository->findBy(
            [FieldConstant::PLAYERID=>$this->id],
            [FieldConstant::POS=>ConstantConstant::CST_ASC]
        );
    }

    public function getWidget(int $widgetId): ?PlayerWidget
    {
        $repository = new PlayerWidgetRepository(new PlayerWidgetCollection());
        return $repository->findOneBy([FieldConstant::PLAYERID=>$this->id, FieldConstant::WIDGETID=>$widgetId]);
    }

    public function getMailPlayer(): ?MailPlayer
    {
        $repository = new MailPlayerRepository(new MailPlayerCollection());
        return $repository->findOneBy([FieldConstant::PLAYERID=>$this->id]);
    }

    public function getMailData(array $searchAttributes): ?MailDataCollection
    {
        $mailPlayer = $this->getMailPlayer();
        if (
            isset($searchAttributes[FieldConstant::FOLDERID])
            && !is_numeric($searchAttributes[FieldConstant::FOLDERID])
        ) {
            $slug = $searchAttributes[FieldConstant::FOLDERID];
            $repository = new MailFolderRepository(new MailFolderCollection());
            $mailFolder = $repository->findOneBy([FieldConstant::SLUG=>$slug]);
            $searchAttributes[FieldConstant::FOLDERID] = $mailFolder->getField(FieldConstant::ID);
        }

        $repository = new MailDataRepository(new MailDataCollection());
        $searchAttributes[FieldConstant::TOID] = $mailPlayer->getField(FieldConstant::ID);
        return $repository->findByAndOrdered($searchAttributes);
    }

    public function getBinome(): ?Player
    {
        $binomes = $this->binomeRepository->findBinome([
            FieldConstant::ID=>$this->id,
            ConstantConstant::CST_CURDATE=>DateUtils::getCopsDate('dbDate')
        ]);

        if ($binomes->valid()) {
            $binome = $binomes->current();
            if ($binome->getField(FieldConstant::LEADERID)==$this->id) {
                return $binome->getBinome();
            } else {
                return $binome->getLeader();
            }
        } else {
            return null;
        }
    }

    public function getBinomes(): BinomeCollection
    {
        return $this->binomeRepository->findBinome([FieldConstant::ID=>$this->id]);
    }
}
