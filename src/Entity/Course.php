<?php
namespace src\Entity;

use src\Collection\CourseCollection;
use src\Constant\FieldConstant;
use src\Controller\CourseController;
use src\Repository\CourseRepository;

class Course extends Entity
{
    //////////////////////////////////////////////////
    // ATTRIBUTES
    //////////////////////////////////////////////////
    protected int $id;
    protected string $name;
    protected string $description;
    protected string $category;
    protected int $level;
    protected int $courseId;
    protected string $reference;
    protected string $prerequisite;
    protected string $cumul;
    protected string $bonus;

    protected CourseRepository $repository;

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
        $this->repository = new CourseRepository(new CourseCollection());
    }

    public function __toString()
    {
        $str  = ($this->id ?? '').' - ';
        $str .= '<br>';
        return $str;
    }

    public static function initFromRow($row): Course
    {
        $obj = new Course();
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
            FieldConstant::DESCRIPTION,
            FieldConstant::CATEGORY,
            FieldConstant::LEVEL,
            FieldConstant::COURSEID,
            FieldConstant::REFERENCE,
            FieldConstant::PREREQUISITE,
            FieldConstant::CUMUL,
            FieldConstant::BONUS,
        ];
    }

    public function update(): void
    {
        $this->initRepositories();
        $this->repository->update($this);
    }

    public function getController(): CourseController
    {
        return new CourseController($this);
    }
}
