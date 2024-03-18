<?php
namespace src\Entity;

use src\Collection\AcronymCollection;
use src\Constant\FieldConstant;
use src\Controller\AcronymController;
use src\Repository\AcronymRepository;

class Acronym extends Entity
{
    //////////////////////////////////////////////////
    // ATTRIBUTES
    //////////////////////////////////////////////////
    protected int $id;
    protected string $code;
    protected string $name;

    protected AcronymRepository $repository;

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
        $this->repository = new AcronymRepository(new AcronymCollection());
    }

    public function __toString()
    {
        $str  = ($this->id ?? '').' - ';
        $str .= '<br>';
        return $str;
    }

    public static function initFromRow($row): Acronym
    {
        $obj = new Acronym();
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
            FieldConstant::CODE,
            FieldConstant::NAME,
        ];
    }

    public function getController(): AcronymController
    {
        return new AcronymController($this);
    }
}
