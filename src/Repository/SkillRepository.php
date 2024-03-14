<?php
namespace src\Repository;

use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Collection\SkillCollection;
use src\Entity\Skill;

/**
 * @method Skill|null find($id)
 * @method Skill|null findOneBy(array $criteria, array $orderBy=null)
 * @method Skill[]    findAll()
 * @method Skill[]    findBy(array $criteria, array $orderBy=null, $limit=null, $offset=null)
 */
class SkillRepository extends Repository
{
    public function __construct(SkillCollection $collection)
    {
        $this->table = 'copsSkill';
        $this->collection = $collection;
    }

    public function createQueryBuilder(string $alias=''): self
    {
        $this->field = Skill::getFields();
        return parent::createQueryBuilder($alias);
    }

    public function createDistinctQueryBuilder(string $field): self
    {
        $this->baseQuery = "SELECT DISTINCT $field FROM ".$this->table;
        return $this;
    }

    public function convertElement($row): Skill
    {
        return Skill::initFromRow($row);
    }

    public function find($id): ?Skill
    {
        $this->collection->empty();
        return $this->createQueryBuilder('s')
            ->setCriteria(['s.id'=>$id])
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneBy(array $criteria, array $orderBy=[]): ?Skill
    {
        $collection = $this->findBy($criteria, $orderBy, 1);
        return $collection->valid() ? $collection->current() : null;
    }

    public function findAll(array $orderBy=[FieldConstant::CST_NAME=>ConstantConstant::CST_ASC]): SkillCollection
    {
        return $this->findBy([], $orderBy);
    }

    public function findBy(array $criteria, array $orderBy=[], int $limit=-1): SkillCollection
    {
        return $this->createQueryBuilder('s')
            ->setCriteria($criteria)
            ->orderBy($orderBy)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
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
        return $this->createQueryBuilder('s')
            ->setCriteriaComplex($criteria)
            ->orderBy($orderBy)
            ->getQuery()
            ->getResult();
    }
}
