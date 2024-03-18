<?php
namespace src\Repository;

use src\Collection\PlayerWidgetCollection;
use src\Constant\ConstantConstant;
use src\Entity\PlayerWidget;

class PlayerWidgetRepository extends Repository
{
    public function __construct(PlayerWidgetCollection $collection)
    {
        $this->table = 'copsPlayerWidget';
        $this->collection = $collection;
    }

    public function createQueryBuilder(): self
    {
        $this->field = PlayerWidget::getFields();
        return parent::createQueryBuilder();
    }

    public function convertElement($row): PlayerWidget
    {
        return PlayerWidget::initFromRow($row);
    }


    




    

    public function getDistinct(string $field): array
    {
        return $this->createDistinctQueryBuilder($field)
            ->orderBy([$field=>ConstantConstant::CST_ASC])
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
