<?php
namespace src\Repository;

use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Collection\WidgetCollection;
use src\Entity\Widget;

/**
 * @method Widget|null find($id)
 * @method Widget|null findOneBy(array $criteria, array $orderBy=null)
 * @method Widget[]    findAll()
 * @method Widget[]    findBy(array $criteria, array $orderBy=null, $limit=null, $offset=null)
 */
class WidgetRepository extends Repository
{
    public function __construct(WidgetCollection $collection)
    {
        $this->table = 'copsWidget';
        $this->collection = $collection;
    }

    public function createQueryBuilder(string $alias=''): self
    {
        $this->field = Widget::getFields();
        return parent::createQueryBuilder($alias);
    }

    public function createDistinctQueryBuilder(string $field): self
    {
        $this->baseQuery = "SELECT DISTINCT $field FROM ".$this->table;
        return $this;
    }

    public function convertElement($row): Widget
    {
        return Widget::initFromRow($row);
    }

    public function find($id): ?Widget
    {
        $this->collection->empty();
        return $this->createQueryBuilder('s')
            ->setCriteria(['s.id'=>$id])
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneBy(array $criteria, array $orderBy=[]): ?Widget
    {
        $collection = $this->findBy($criteria, $orderBy, 1);
        return $collection->valid() ? $collection->current() : null;
    }

    public function findAll(array $orderBy=[FieldConstant::NAME=>ConstantConstant::CST_ASC]): WidgetCollection
    {
        return $this->findBy([], $orderBy);
    }

    public function findBy(array $criteria, array $orderBy=[], int $limit=-1): WidgetCollection
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

    public function update(Widget $course): void
    {
        $this->field = Widget::getFields();
        $this->updateQueryBuilder($course)
            ->getQuery()
            ->execQuery();
    }

}
