<?php
namespace src\Repository;

use src\Collection\InvestigationCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Entity\Investigation;

class InvestigationRepository extends Repository
{
    public function __construct(InvestigationCollection $collection)
    {
        $this->table = 'copsInvestigation';
        $this->collection = $collection;
        $this->field = Investigation::getFields();
    }

    public function convertElement($row): Investigation
    {
        return Investigation::initFromRow($row);
    }

    public function findAll(array $orderBy=[FieldConstant::NAME=>ConstantConstant::CST_ASC]): InvestigationCollection
    {
        return $this->findBy([], $orderBy);
    }

}
