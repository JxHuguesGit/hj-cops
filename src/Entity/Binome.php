<?php
namespace src\Entity;

use src\Collection\BinomeCollection;
use src\Collection\PlayerCollection;
use src\Constant\FieldConstant;
use src\Repository\BinomeRepository;
use src\Repository\PlayerRepository;

class Binome extends Entity
{
    //////////////////////////////////////////////////
    // ATTRIBUTES
    //////////////////////////////////////////////////
    protected int $id;
    protected int $leaderId;
    protected int $binomeId;
    protected string $startDate;
    protected string $endDate;

    protected BinomeRepository $repository;
    protected PlayerRepository $playerRepository;

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
        $this->repository = new BinomeRepository(new BinomeCollection());
        $this->playerRepository = new PlayerRepository(new PlayerCollection());
    }

    public static function initFromRow($row): Binome
    {
        $obj = new Binome();
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
            FieldConstant::LEADERID,
            FieldConstant::BINOMEID,
            FieldConstant::STARTDATE,
            FieldConstant::ENDDATE,
        ];
    }

    public function getLeader(): ?Player
    {
        return $this->playerRepository->find($this->leaderId);
    }

    public function getBinome(): ?Player
    {
        return $this->playerRepository->find($this->binomeId);
    }
}
