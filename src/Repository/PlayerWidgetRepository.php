<?php
namespace src\Repository;

use src\Collection\PlayerWidgetCollection;
use src\Entity\PlayerWidget;

/**
 * @method PlayerWidget|null find($id)
 * @method PlayerWidget|null findOneBy(array $criteria, array $orderBy=null)
 * @method PlayerWidget[]    findAll()
 * @method PlayerWidget[]    findBy(array $criteria, array $orderBy=null, $limit=null, $offset=null)
 */
class PlayerWidgetRepository extends Repository
{
    public function __construct(PlayerWidgetCollection $collection)
    {
        $this->table = 'copsPlayerWidget';
        $this->collection = $collection;
    }

    public function createQueryBuilder(string $alias=''): self
    {
        $this->field = PlayerWidget::getFields();
        return parent::createQueryBuilder($alias);
    }

    public function createDistinctQueryBuilder(string $field): self
    {
        $this->baseQuery = "SELECT DISTINCT $field FROM ".$this->table;
        return $this;
    }

    public function convertElement($row): PlayerWidget
    {
        return PlayerWidget::initFromRow($row);
    }

    public function find($id): ?PlayerWidget
    {
        return $this->createQueryBuilder('pw')
            ->setCriteria(['pw.id'=>$id])
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneBy(array $criteria, array $orderBy=[]): ?PlayerWidget
    {
        $collection = $this->findBy($criteria, $orderBy, 1);
        return $collection->valid() ? $collection->current() : null;
    }

    public function findAll(array $orderBy=['id'=>'ASC']): PlayerWidgetCollection
    {
        return $this->findBy([], $orderBy);
    }

    public function findBy(array $criteria, array $orderBy=[], int $limit=-1, int $offset=0): PlayerWidgetCollection
    {
        return $this->createQueryBuilder('pw')
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

    public function update(PlayerWidget $playerSkill): void
    {
        $this->field = PlayerWidget::getFields();
        $this->updateQueryBuilder($playerSkill)
            ->getQuery()
            ->execQuery();
    }

    public function insert(PlayerWidget $playerWidget): void
    {
        $this->field = PlayerWidget::getFields();
        $this->insertQueryBuilder($playerWidget)
            ->getQuery()
            ->execQuery();
    }

}
