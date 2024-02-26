<?php
namespace src\Entity;

use src\Collection\PlayerWidgetCollection;
use src\Collection\WidgetCollection;
use src\Constant\FieldConstant;
use src\Controller\PlayerWidgetController;
use src\Repository\WidgetRepository;
use src\Repository\PlayerWidgetRepository;

class PlayerWidget extends Entity
{
    //////////////////////////////////////////////////
    // ATTRIBUTES
    //////////////////////////////////////////////////
    protected int $id;
    protected int $playerId;
    protected int $widgetId;
    protected int $active;
    protected string $color;
    protected int $width;
    protected int $pos;

    protected PlayerWidgetRepository $repository;

    //////////////////////////////////////////////////
    // CONSTRUCT
    //////////////////////////////////////////////////
    public function __construct(array $attributes=[])
    {
        parent::__construct($attributes);
        $this->initRepositories();
    }

    private function initRepositories()
    {
        $this->repository = new PlayerWidgetRepository(new PlayerWidgetCollection());
    }

    public static function initFromRow($row): PlayerWidget
    {
        $obj = new PlayerWidget();
        $fields = $obj->getFields();
        foreach ($fields as $field) {
            $obj->setField($field, $row->{$field});
        }
        return $obj;
    }

    public static function getFields(): array
    {
        return [
            FieldConstant::ID,
            FieldConstant::PLAYERID,
            FieldConstant::WIDGETID,
            FieldConstant::ACTIVE,
            FieldConstant::COLOR,
            FieldConstant::WIDTH,
            FieldConstant::POS,
        ];
    }

    public function getController(): PlayerWidgetController
    {
        return new PlayerWidgetController($this);
    }

    public function insert(): void
    {
        $this->initRepositories();
        $this->repository->insert($this);
    }

    public function update(): void
    {
        $this->initRepositories();
        $this->repository->update($this);
    }

    public function getWidget(): ?Widget
    {
        $repository = new WidgetRepository(new WidgetCollection());
        return $repository->find($this->widgetId);
    }

}
