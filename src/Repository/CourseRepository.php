<?php
namespace src\Repository;

use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Collection\CourseCollection;
use src\Entity\Course;

class CourseRepository extends Repository
{
    public function __construct(CourseCollection $collection)
    {
        $this->table = 'copsCourse';
        $this->collection = $collection;
    }

    public function createQueryBuilder(): self
    {
        $this->field = Course::getFields();
        return parent::createQueryBuilder();
    }

    public function convertElement($row): Course
    {
        return Course::initFromRow($row);
    }

    public function findAll(array $orderBy=[FieldConstant::CST_NAME=>ConstantConstant::CST_ASC]): CourseCollection
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

    public function update(Course $course): void
    {
        $this->field = Course::getFields();
        $this->updateQueryBuilder($course)
            ->getQuery()
            ->execQuery();
    }

    public function findByCriteria(array $complexCriteria, array $orderBy): CourseCollection
    {
        $this->field = Course::getFields();
        return $this->createQueryBuilder()
            ->setCriteriaComplex($complexCriteria)
            ->orderBy($orderBy)
            ->getQuery()
            ->getResult();
    }
}
