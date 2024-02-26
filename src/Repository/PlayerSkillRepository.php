<?php
namespace src\Repository;

use src\Collection\PlayerSkillCollection;
use src\Entity\PlayerSkill;

/**
 * @method PlayerSkill|null find($id)
 * @method PlayerSkill|null findOneBy(array $criteria, array $orderBy=null)
 * @method PlayerSkill[]    findAll()
 * @method PlayerSkill[]    findBy(array $criteria, array $orderBy=null, $limit=null, $offset=null)
 */
class PlayerSkillRepository extends Repository
{
    public function __construct(PlayerSkillCollection $collection)
    {
        $this->table = 'copsPlayerSkill';
        $this->collection = $collection;
    }

    public function createQueryBuilder(string $alias=''): self
    {
        $this->field = PlayerSkill::getFields();
        return parent::createQueryBuilder($alias);
    }

    public function createDistinctQueryBuilder(string $field): self
    {
        $this->baseQuery = "SELECT DISTINCT $field FROM ".$this->table;
        return $this;
    }

    public function convertElement($row): PlayerSkill
    {
        return PlayerSkill::initFromRow($row);
    }

    public function find($id): ?Player
    {
        return $this->createQueryBuilder('ps')
            ->setCriteria(['ps.id'=>$id])
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneBy(array $criteria, array $orderBy=[]): ?PlayerSkill
    {
        $collection = $this->findBy($criteria, $orderBy, 1);
        return $collection->valid() ? $collection->current() : null;
    }

    public function findAll(array $orderBy=['id'=>'ASC']): PlayerSkillCollection
    {
        return $this->findBy([], $orderBy);
    }

    public function findBy(array $criteria, array $orderBy=[], int $limit=-1): PlayerSkillCollection
    {
        return $this->createQueryBuilder('ps')
            ->setCriteria($criteria)
            ->orderBy($orderBy)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getDistinct(string $field): array
    {
        return $this->createDistinctQueryBuilder($field)
            ->orderBy([$field=>'asc'])
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
            ->orderBy(['mainSkill'=>'asc', 'secondarySkill'=>'asc'])
            ->getQuery()
            ->getResult();
    }
}
