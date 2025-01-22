<?php
namespace src\Entity;

use src\Collection\ChronologieCollection;
use src\Controller\ChronologieController;
use src\Constant\FieldConstant;
use src\Form\ChronologieForm;
use src\Repository\ChronologieRepository;

class Chronologie extends Entity
{
    //////////////////////////////////////////////////
    // ATTRIBUTES
    //////////////////////////////////////////////////
    protected int $id;
    protected string $investigationId;
    protected string $chronoEvent;
    protected string $dateEvent;
    protected string $hourEvent;
    protected string $playerId;

    protected ChronologieRepository $repository;

    //////////////////////////////////////////////////
    // CONSTRUCT
    //////////////////////////////////////////////////
    public function __construct(array $attributes=[])
    {
        parent::__construct($attributes);
        $this->initRepositories();
    }

    public function init()
    {
        //protected int $id;
        //protected string $investigationId;
        $this->chronoEvent = '';
        $this->dateEvent   = '';
        $this->hourEvent   = '';
        //protected string $playerId;
    }

    private function initRepositories(): void
    {
        $this->repository = new ChronologieRepository(new ChronologieCollection());
    }

    public static function initFromRow($row): Chronologie
    {
        $obj = new Chronologie();
        $fields = $obj->getFields();
        foreach ($fields as $field) {
            $obj->setField($field, $row->{$field});
        }
        return $obj;
    }

    public function getFormattedDate(): string
    {
        if ($this->dateEvent=='') {
            $strReturned = '';
        } else {
            list($y, $m, $d) = explode('-', $this->dateEvent);
            $strReturned = $d!=0 ? implode('/', [$d, $m, $y]) : implode('/', [$m, $y]);
        }
        return $strReturned;
    }

    public static function getFields(): array
    {
        return [
            FieldConstant::ID,
            FieldConstant::INVESTIGATIONID,
            FieldConstant::CHRONO_EVENT,
            FieldConstant::DATE_EVENT,
            FieldConstant::HOUR_EVENT,
            FieldConstant::PLAYERID,
        ];
    }

    public function update(): void
    {
        $this->initRepositories();
        $this->repository->update($this);
    }

    public function insert(): void
    {
        $this->initRepositories();
        $this->repository->insert($this);
    }

    public function getController(): ChronologieController
    {
        $controller = new ChronologieController();
        $controller->setChronologie($this);
        return $controller;
    }

    public function getForm(): ChronologieForm
    {
        return new ChronologieForm($this);
    }

}
