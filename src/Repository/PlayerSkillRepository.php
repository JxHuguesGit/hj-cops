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
        $this->field = PlayerSkill::getFields();
    }

    public function convertElement($row): PlayerSkill
    {
        return PlayerSkill::initFromRow($row);
    }

    public function findByAndOrdered(array $criteria): PlayerSkillCollection
    {
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
