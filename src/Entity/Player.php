<?php
namespace src\Entity;

use src\Collection\PlayerCollection;
use src\Collection\PlayerWidgetCollection;
use src\Constant\FieldConstant;
use src\Controller\PlayerController;
use src\Entity\PlayerWidget;
use src\Repository\PlayerRepository;
use src\Repository\PlayerWidgetRepository;

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

    //////////////////////////////////////////////////
    // CONSTRUCT
    //////////////////////////////////////////////////
    public function __contruct(array $attributes=[])
    {
        parent::__contruct($attributes);
        $this->initRepositories();
    }

    private function initRepositories()
    {
        $this->repository = new PlayerRepository(new PlayerCollection());
    }

    public function __toString()
    {
        $str  = ($this->id ?? '').' - ';
        $str .= '<br>';
        return $str;
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
            FieldConstant::SURNAME,
            FieldConstant::XPCUMUL,
            FieldConstant::XPCUR,
        ];
    }

    public function isGuest(): bool
    {
        return $this->id==64;
    }

    public function getFullName(): string
    {
        return $this->firstname.($this->firstname=='' ? '' : ' ').$this->lastname;
    }

    public function update(): void
    {
        $this->initRepositories();
        $this->repository->update($this);
    }

    public function getWidgets(): PlayerWidgetCollection
    {
        $repository = new PlayerWidgetRepository(new PlayerWidgetCollection());
        return $repository->findBy([FieldConstant::PLAYERID=>$this->id], [FieldConstant::POS=>'asc']);
    }

    public function getWidget(int $widgetId): ?PlayerWidget
    {
        $repository = new PlayerWidgetRepository(new PlayerWidgetCollection());
        return $repository->findOneBy([FieldConstant::PLAYERID=>$this->id, FieldConstant::WIDGETID=>$widgetId]);
    }
}
