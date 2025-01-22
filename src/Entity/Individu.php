<?php
namespace src\Entity;

use src\Collection\IndividuCollection;
use src\Constant\FieldConstant;
use src\Controller\IndividuController;
use src\Enum\OccupationEnum;
use src\Form\IndividuForm;
use src\Repository\IndividuRepository;

class Individu extends Entity
{
    //////////////////////////////////////////////////
    // ATTRIBUTES
    //////////////////////////////////////////////////
    protected int $id;
    protected string $gender;
    protected string $nameset;
    protected string $firstname;
    protected string $lastname;
    protected string $streetaddress;
    protected string $city;
    protected string $zipcode;
    protected string $occupation;
    protected int $occupationId;
    protected float $weight;
    protected int $height;
    protected string $birthday;

    protected IndividuRepository $repository;

    //////////////////////////////////////////////////
    // CONSTRUCT
    //////////////////////////////////////////////////
    public function __construct(array $attributes=[])
    {
        parent::__construct($attributes);
        $this->initRepositories();
    }

    private function initRepositories(): void
    {
        $this->repository = new IndividuRepository(new IndividuCollection());
    }

    public static function initFromRow($row): Individu
    {
        $obj = new Individu();
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
            FieldConstant::GENDER,
            FieldConstant::NAMESET,
            FieldConstant::FIRSTNAME,
            FieldConstant::LASTNAME,
            FieldConstant::OCCUPATIONID,
            FieldConstant::STREETADDRESS,
            FieldConstant::CITY,
            FieldConstant::ZIPCODE,
            FieldConstant::WEIGHT,
            FieldConstant::HEIGHT,
            FieldConstant::BIRTHDAY,
        ];
    }

    public function update(): void
    {
        $this->initRepositories();
        $this->repository->update($this);
    }

    public function getController(): IndividuController
    {
        return new IndividuController($this);
    }

    public function getForm(): IndividuForm
    {
        return new IndividuForm($this);
    }

    public function getNameAndOccupation(bool $andOccupation=true): string
    {
        $str = $this->lastname.' '.$this->firstname;
        if ($andOccupation) {
            $str .= ' ('.OccupationEnum::fromDb($this->occupationId).')';
        }
        return $str;
    }
}
