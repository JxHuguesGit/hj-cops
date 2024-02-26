<?php
namespace src\Repository;

use src\Collection\MailDataCollection;
use src\Entity\MailData;

/**
 * @method MailData|null find($id)
 * @method MailData|null findOneBy(array $criteria, array $orderBy=null)
 * @method MailData[]    findAll()
 * @method MailData[]    findBy(array $criteria, array $orderBy=null, $limit=null, $offset=null)
 */
class MailDataRepository extends Repository
{
    public function __construct(MailDataCollection $collection)
    {
        $this->table = 'copsMailData';
        $this->collection = $collection;
    }

    public function createQueryBuilder(string $alias=''): self
    {
        $this->field = MailData::getFields();
        return parent::createQueryBuilder($alias);
    }

    public function convertElement($row): MailData
    {
        return MailData::initFromRow($row);
    }

    public function find($id): ?MailData
    {
        $this->collection->empty();
        return $this->createQueryBuilder('s')
            ->setCriteria(['s.id'=>$id])
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneBy(array $criteria, array $orderBy=[]): ?MailData
    {
        $collection = $this->findBy($criteria, $orderBy, 1);
        return $collection->valid() ? $collection->current() : null;
    }

    public function findAll(array $orderBy=['id'=>'ASC']): MailDataCollection
    {
        return $this->findBy([], $orderBy);
    }

    public function findBy(array $criteria, array $orderBy=[], int $limit=-1): MailDataCollection
    {
        return $this->createQueryBuilder('s')
            ->setCriteria($criteria)
            ->orderBy($orderBy)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function update(MailData $mailData): void
    {
        $this->field = MailData::getFields();
        $this->updateQueryBuilder($mailData)
            ->getQuery()
            ->execQuery();
    }
}
