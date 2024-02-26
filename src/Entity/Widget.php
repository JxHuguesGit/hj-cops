<?php
namespace src\Entity;

use src\Collection\WidgetCollection;
use src\Constant\FieldConstant;
use src\Controller\WidgetController;
use src\Repository\WidgetRepository;

class Widget extends Entity
{
    //////////////////////////////////////////////////
    // ATTRIBUTES
    //////////////////////////////////////////////////
    protected int $id;
    protected string $name;
    protected string $slug;
    protected bool $active;

    protected WidgetRepository $repository;

    //////////////////////////////////////////////////
    // CONSTRUCT
    //////////////////////////////////////////////////
    public function __contruct(array $attributes=[])
    {
        parent::__contruct($attributes);
        $this->initRepositories();
    }

    private function initRepositories()
    {
        $this->repository = new WidgetRepository(new WidgetCollection());
    }

    public function __toString()
    {
        $str  = ($this->id ?? '').' - ';
        $str .= '<br>';
        return $str;
    }

    public static function initFromRow($row): Widget
    {
        $obj = new Widget();
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
            FieldConstant::NAME,
            FieldConstant::SLUG,
            FieldConstant::ACTIVE,
        ];
    }

    public function update(): void
    {
        $this->initRepositories();
        $this->repository->update($this);
    }

    public function getController(): WidgetController
    {
        return new WidgetController($this);
    }
}
