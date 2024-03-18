<?php
namespace src\Repository;

use src\Collection\PlayerSkillCollection;
use src\Constant\ConstantConstant;
use src\Entity\PlayerSkill;

class PlayerSkillRepository extends Repository
{
    public function __construct(PlayerSkillCollection $collection)
    {
        $this->table = 'copsPlayerSkill';
        $this->collection = $collection;
    }

    public function createQueryBuilder(): self
    {
        $this->field = PlayerSkill::getFields();
        return parent::createQueryBuilder();
    }

    public function convertElement($row): PlayerSkill
    {
        return PlayerSkill::initFromRow($row);
    }






    

    public function getDistinct(string $field): array
    {
        return $this->createDistinctQueryBuilder($field)
            ->orderBy([$field=>ConstantConstant::CST_ASC])
            ->getQuery()
            ->getDistinctResult($field);
    }

    public function update(PlayerSkill $playerSkill): void
    {
        $this->field = PlayerSkill::getFields();
        $this->updateQueryBuilder($playerSkill)
            ->getQuery()
            ->execQuery();
    }

    public function insert(PlayerSkill $playerSkill): void
    {
        $this->field = PlayerSkill::getFields();
        $this->insertQueryBuilder($playerSkill)
            ->getQuery()
            ->execQuery();
    }

    public function findByAndOrdered(array $criteria): PlayerSkillCollection
    {
        $this->field = PlayerSkill::getFields();
        $this->baseQuery  = "SELECT cps.`".implode('`, cps.`', $this->field)."`, ";
        $this->baseQuery .= "IF (cs2.name IS NULL, cs.name, cs2.name) AS mainSKill, ";
        $this->baseQuery .= "IF (cs2.name IS NULL, '', cs.name) AS secondarySkill ";
        $this->baseQuery .= "FROM ".$this->table." AS cps ";
        $this->baseQuery .= "LEFT JOIN `copsSkill` AS cs ON cps.skillId = cs.id ";
        $this->baseQuery .= "LEFT JOIN `copsSkill` AS cs2 ON cs.skillId = cs2.id ";

        return $this->setCriteria($criteria)
            ->orderBy(['mainSkill'=>ConstantConstant::CST_ASC, 'secondarySkill'=>ConstantConstant::CST_ASC])
            ->getQuery()
            ->getResult();
    }
}
