<?php
namespace src\Repository;

use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Collection\WidgetCollection;
use src\Entity\Widget;

class WidgetRepository extends Repository
{
    public function __construct(WidgetCollection $collection)
    {
        $this->table = 'copsWidget';
        $this->collection = $collection;
    }

    public function createQueryBuilder(): self
    {
        $this->field = Widget::getFields();
        return parent::createQueryBuilder();
    }


    public function convertElement($row): Widget
    {
        return Widget::initFromRow($row);
    }

    public function findAll(array $orderBy=[FieldConstant::NAME=>ConstantConstant::CST_ASC]): WidgetCollection
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

    public function update(Widget $course): void
    {
        $this->field = Widget::getFields();
        $this->updateQueryBuilder($course)
            ->getQuery()
            ->execQuery();
    }

}
