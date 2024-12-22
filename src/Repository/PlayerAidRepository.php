<?php
namespace src\Repository;

use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Collection\PlayerAidCollection;
use src\Entity\PlayerAid;

class PlayerAidRepository extends Repository
{
    public function __construct(PlayerAidCollection $collection)
    {
        $this->table = 'copsPlayerAid';
        $this->collection = $collection;
        $this->field = PlayerAid::getFields();
    }

    public function convertElement($row): PlayerAid
    {
        return PlayerAid::initFromRow($row);
    }

    public function findAll(array $orderBy=[FieldConstant::NAME=>ConstantConstant::CST_ASC]): PlayerAidCollection
    {
        return $this->findBy([], $orderBy);
    }

}
