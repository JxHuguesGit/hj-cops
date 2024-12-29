<?php
namespace src\Repository;

use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Collection\IndividuCollection;
use src\Entity\Individu;

class IndividuRepository extends Repository
{
    public function __construct(IndividuCollection $collection)
    {
        $this->table = 'copsIndividu';
        $this->collection = $collection;
        $this->field = Individu::getFields();
    }

    public function convertElement($row): Individu
    {
        return Individu::initFromRow($row);
    }

    public function findAll(array $orderBy=[FieldConstant::LASTNAME=>ConstantConstant::CST_ASC]): IndividuCollection
    {
        return $this->findBy([], $orderBy);
    }
}
