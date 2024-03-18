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
        $this->field = Course::getFields();
    }

    public function convertElement($row): Course
    {
        return Course::initFromRow($row);
    }

    public function findAll(array $orderBy=[FieldConstant::CST_NAME=>ConstantConstant::CST_ASC]): CourseCollection
    {
        return $this->findBy([], $orderBy);
    }

    public function findByCriteria(array $criteria, array $orderBy): CourseCollection
    {
        return $this->createQueryBuilder()
            ->setCriteriaComplex($criteria)
            ->orderBy($orderBy)
            ->getQuery()
            ->getResult();
    }
}
