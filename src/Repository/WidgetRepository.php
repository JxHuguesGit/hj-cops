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
        $this->field = Widget::getFields();
    }

    public function convertElement($row): Widget
    {
        return Widget::initFromRow($row);
    }

    public function findAll(array $orderBy=[FieldConstant::NAME=>ConstantConstant::CST_ASC]): WidgetCollection
    {
        return $this->findBy([], $orderBy);
    }

}
