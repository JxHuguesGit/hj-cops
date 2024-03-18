<?php
namespace src\Repository;

use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Collection\SkillCollection;
use src\Entity\Skill;

class SkillRepository extends Repository
{
    public function __construct(SkillCollection $collection)
    {
        $this->table = 'copsSkill';
        $this->collection = $collection;
        $this->field = Skill::getFields();
    }

    public function convertElement($row): Skill
    {
        return Skill::initFromRow($row);
    }

    public function findAll(array $orderBy=[FieldConstant::CST_NAME=>ConstantConstant::CST_ASC]): SkillCollection
    {
        return $this->findBy([], $orderBy);
    }
}
