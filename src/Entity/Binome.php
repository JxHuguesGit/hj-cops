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

    public function __toString(): string
    {
        return '['.implode(', ', [$this->id, $this->leaderId, $this->binomeId, $this->startDate, $this->endDate]).']';
    }

    private function initRepositories()
    {
        $this->repository = new BinomeRepository(new BinomeCollection());
        $this->playerRepository = new PlayerRepository(new PlayerCollection());
    }

    public function insert(): void
    {
        $this->initRepositories();
        $this->repository->insert($this);
    }

    public function update(): void
    {
        $this->initRepositories();
        $this->repository->update($this);
    }

    public function delete(): void
    {
        $this->initRepositories();
        $this->repository->delete($this);
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
