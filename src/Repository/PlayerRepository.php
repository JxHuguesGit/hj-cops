<?php
namespace src\Repository;

use src\Collection\PlayerCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Entity\Player;
use src\Utils\DateUtils;

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

    public function findAllCopsBy(
        array $criteria=[],
        array $orderBy=[FieldConstant::LASTNAME=>ConstantConstant::CST_ASC]
    ): PlayerCollection
    {
        $this->baseQuery  = "SELECT * ";
        $this->baseQuery .= "FROM ".$this->table;
        $criteriaComplex = [
            [ConstantConstant::CST_FIELD=>FieldConstant::RANK, 'operand'=>'<>', ConstantConstant::CST_VALUE=>''],
            [ConstantConstant::CST_FIELD=>FieldConstant::STARTDATE, 'operand'=>'<=', ConstantConstant::CST_VALUE=>DateUtils::getCopsDate('dbDate')]
        ];

        return $this->setCriteria($criteria)
            ->setCriteriaComplex($criteriaComplex)
            ->orderBy($orderBy)
            ->getQuery()
            ->getResult();
    }
}
