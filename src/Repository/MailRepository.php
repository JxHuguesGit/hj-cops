<?php
namespace src\Repository;

use src\Collection\MailCollection;
use src\Entity\Mail;

/**
 * @method Mail|null find($id)
 * @method Mail|null findOneBy(array $criteria, array $orderBy=null)
 * @method Mail[]    findAll()
 * @method Mail[]    findBy(array $criteria, array $orderBy=null, $limit=null, $offset=null)
 */
class MailRepository extends Repository
{
    public function __construct(MailCollection $collection)
    {
        $this->table = 'copsMail';
        $this->collection = $collection;
    }

    public function createQueryBuilder(string $alias=''): self
    {
        $this->field = Mail::getFields();
        return parent::createQueryBuilder($alias);
    }

    public function convertElement($row): Mail
    {
        return Mail::initFromRow($row);
    }

    public function find($id): ?Mail
    {
        $this->collection->empty();
        return $this->createQueryBuilder('s')
            ->setCriteria(['s.id'=>$id])
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneBy(array $criteria, array $orderBy=[]): ?Mail
    {
        $collection = $this->findBy($criteria, $orderBy, 1);
        return $collection->valid() ? $collection->current() : null;
    }

    public function findAll(array $orderBy=['sentDate'=>'ASC']): MailCollection
    {
        return $this->findBy([], $orderBy);
    }

    public function findBy(array $criteria, array $orderBy=[], int $limit=-1, int $offset=0): MailCollection
    {
        return $this->createQueryBuilder('s')
            ->setCriteria($criteria)
            ->orderBy($orderBy)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function update(Mail $mail): void
    {
        $this->field = Mail::getFields();
        $this->updateQueryBuilder($mail)
            ->getQuery()
            ->execQuery();
    }
}
