<?php
namespace src\Repository;

use src\Collection\PlayerCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Entity\Player;

class PlayerRepository extends Repository
{
    public function __construct(PlayerCollection $collection)
    {
        $this->table = 'copsPlayer';
        $this->collection = $collection;
    }

    public function createQueryBuilder(): self
    {
        $this->field = Player::getFields();
        return parent::createQueryBuilder();
    }

    public function convertElement($row): Player
    {
        return Player::initFromRow($row);
    }

    public function findAll(array $orderBy=[FieldConstant::LASTNAME=>ConstantConstant::CST_ASC]): PlayerCollection
    {
        return $this->findBy([], $orderBy);
    }

    




    

    public function getDistinct(string $field): array
    {
        return $this->createDistinctQueryBuilder($field)
            ->orderBy([$field=>ConstantConstant::CST_ASC])
            ->getQuery()
            ->getDistinctResult($field);
    }

    public function update(Player $player): void
    {
        $this->field = Player::getFields();
        $this->updateQueryBuilder($player)
            ->getQuery()
            ->execQuery();
    }
}
