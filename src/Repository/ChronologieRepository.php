<?php
namespace src\Repository;

use src\Collection\ChronologieCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Entity\Chronologie;

class ChronologieRepository extends Repository
{
    public function __construct(ChronologieCollection $collection)
    {
        $this->table = 'copsChronologie';
        $this->collection = $collection;
        $this->field = Chronologie::getFields();
    }

    public function convertElement($row): Chronologie
    {
        return Chronologie::initFromRow($row);
    }

    public function findAll(
        array $orderBy=[
            FieldConstant::DATE_EVENT=>ConstantConstant::CST_ASC,
            FieldConstant::HOUR_EVENT=>ConstantConstant::CST_ASC
        ]
    ): ChronologieCollection
    {
        return $this->findBy([], $orderBy);
    }

}
