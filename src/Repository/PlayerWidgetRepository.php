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
        $this->field = PlayerWidget::getFields();
    }

    public function convertElement($row): PlayerWidget
    {
        return PlayerWidget::initFromRow($row);
    }

}
