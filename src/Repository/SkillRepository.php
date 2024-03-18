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
    }

    public function createQueryBuilder(): self
    {
        $this->field = Skill::getFields();
        return parent::createQueryBuilder();
    }

    public function convertElement($row): Skill
    {
        return Skill::initFromRow($row);
    }

    public function findAll(array $orderBy=[FieldConstant::CST_NAME=>ConstantConstant::CST_ASC]): SkillCollection
    {
        return $this->findBy([], $orderBy);
    }





 



    

    public function getDistinct(string $field): array
    {
        return $this->createDistinctQueryBuilder($field)
            ->orderBy([$field=>ConstantConstant::CST_ASC])
            ->getQuery()
            ->getDistinctResult($field);
    }

    public function update(Skill $skill): void
    {
        $this->field = Skill::getFields();
        $this->updateQueryBuilder($skill)
            ->getQuery()
            ->execQuery();
    }

    public function findByCriteria(array $criteria, array $orderBy): SkillCollection
    {
        $this->field = Skill::getFields();
        return $this->createQueryBuilder()
            ->setCriteriaComplex($criteria)
            ->orderBy($orderBy)
            ->getQuery()
            ->getResult();
    }
}
