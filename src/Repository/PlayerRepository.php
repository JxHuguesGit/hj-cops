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
        $this->field = Player::getFields();
    }

    public function convertElement($row): Player
    {
        return Player::initFromRow($row);
    }

    public function findAll(array $orderBy=[FieldConstant::LASTNAME=>ConstantConstant::CST_ASC]): PlayerCollection
    {
        return $this->findBy([], $orderBy);
    }
}
