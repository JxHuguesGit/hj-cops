<?php
namespace src\Repository;

use src\Collection\MailPlayerCollection;
use src\Entity\MailPlayer;

/**
 * @method MailPlayer|null find($id)
 * @method MailPlayer|null findOneBy(array $criteria, array $orderBy=null)
 * @method MailPlayer[]    findAll()
 * @method MailPlayer[]    findBy(array $criteria, array $orderBy=null, $limit=null, $offset=null)
 */
class MailPlayerRepository extends Repository
{
    public function __construct(MailPlayerCollection $collection)
    {
        $this->table = 'copsMailPlayer';
        $this->collection = $collection;
    }

    public function createQueryBuilder(string $alias=''): self
    {
        $this->field = MailPlayer::getFields();
        return parent::createQueryBuilder($alias);
    }

    public function convertElement($row): MailPlayer
    {
        return MailPlayer::initFromRow($row);
    }

    public function find($id): ?MailPlayer
    {
        $this->collection->empty();
        return $this->createQueryBuilder('s')
            ->setCriteria(['id'=>$id])
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneBy(array $criteria, array $orderBy=[]): ?MailPlayer
    {
        $collection = $this->findBy($criteria, $orderBy, 1);
        return $collection->valid() ? $collection->current() : null;
    }

    public function findAll(array $orderBy=['mail'=>'ASC']): MailPlayerCollection
    {
        return $this->findBy([], $orderBy);
    }

    public function findBy(array $criteria, array $orderBy=[], int $limit=-1): MailPlayerCollection
    {
        return $this->createQueryBuilder('s')
            ->setCriteria($criteria)
            ->orderBy($orderBy)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function update(MailPlayer $mailPlayer): void
    {
        $this->field = MailPlayer::getFields();
        $this->updateQueryBuilder($mailPlayer)
            ->getQuery()
            ->execQuery();
    }
}
