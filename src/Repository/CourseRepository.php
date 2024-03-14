<?php
namespace src\Repository;

use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Collection\CourseCollection;
use src\Entity\Course;

/**
 * @method Course|null find($id)
 * @method Course|null findOneBy(array $criteria, array $orderBy=null)
 * @method Course[]    findAll()
 * @method Course[]    findBy(array $criteria, array $orderBy=null, $limit=null, $offset=null)
 */
class CourseRepository extends Repository
{
    public function __construct(CourseCollection $collection)
    {
        $this->table = 'copsCourse';
        $this->collection = $collection;
    }

    public function createQueryBuilder(string $alias=''): self
    {
        $this->field = Course::getFields();
        return parent::createQueryBuilder($alias);
    }

    public function createDistinctQueryBuilder(string $field): self
    {
        $this->baseQuery = "SELECT DISTINCT $field FROM ".$this->table;
        return $this;
    }

    public function convertElement($row): Course
    {
        return Course::initFromRow($row);
    }

    public function find($id): ?Course
    {
        $this->collection->empty();
        return $this->createQueryBuilder('s')
            ->setCriteria(['s.id'=>$id])
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneBy(array $criteria, array $orderBy=[]): ?Course
    {
        $collection = $this->findBy($criteria, $orderBy, 1);
        return $collection->valid() ? $collection->current() : null;
    }

    public function findAll(array $orderBy=[FieldConstant::CST_NAME=>ConstantConstant::CST_ASC]): CourseCollection
    {
        return $this->findBy([], $orderBy);
    }

    public function findBy(array $criteria, array $orderBy=[], int $limit=-1): CourseCollection
    {
        return $this->createQueryBuilder('s')
            ->setCriteria($criteria)
            ->orderBy($orderBy)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
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
        return $this->createQueryBuilder('s')
            ->setCriteriaComplex($complexCriteria)
            ->orderBy($orderBy)
            ->getQuery()
            ->getResult();
    }
}
