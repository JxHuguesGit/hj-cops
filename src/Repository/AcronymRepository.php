<?php
namespace src\Repository;

use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Collection\AcronymCollection;
use src\Entity\Acronym;

class AcronymRepository extends Repository
{
    public function __construct(AcronymCollection $collection)
    {
        $this->table = 'copsAcronym';
        $this->collection = $collection;
    }

    public function createQueryBuilder(): self
    {
        $this->field = Acronym::getFields();
        return parent::createQueryBuilder();
    }

    public function convertElement($row): Acronym
    {
        return Acronym::initFromRow($row);
    }

    public function findAll(array $orderBy=[FieldConstant::CST_CODE=>ConstantConstant::CST_ASC]): AcronymCollection
    {
        return $this->findBy([], $orderBy);
    }

}
