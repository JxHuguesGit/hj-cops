<?php
namespace src\Entity;

use src\Collection\PlayerAidCollection;
use src\Constant\FieldConstant;
use src\Controller\PlayerAidController;
use src\Repository\PlayerAidRepository;

class PlayerAid extends Entity
{
    //////////////////////////////////////////////////
    // ATTRIBUTES
    //////////////////////////////////////////////////
    protected int $id;
    protected string $name;
    protected string $code;

    protected PlayerAidRepository $repository;

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
        $this->repository = new PlayerAidRepository(new PlayerAidCollection());
    }

    public static function initFromRow($row): PlayerAid
    {
        $obj = new PlayerAid();
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
            FieldConstant::CODE,
        ];
    }

    public function getController(): PlayerAidController
    {
        return new PlayerAidController($this);
    }
}
