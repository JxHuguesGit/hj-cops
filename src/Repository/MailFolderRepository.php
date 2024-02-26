<?php
namespace src\Repository;

use src\Collection\MailFolderCollection;
use src\Entity\MailFolder;

/**
 * @method MailFolder|null find($id)
 * @method MailFolder|null findOneBy(array $criteria, array $orderBy=null)
 * @method MailFolder[]    findAll()
 * @method MailFolder[]    findBy(array $criteria, array $orderBy=null, $limit=null, $offset=null)
 */
class MailFolderRepository extends Repository
{
    public function __construct(MailFolderCollection $collection)
    {
        $this->table = 'copsMailFolder';
        $this->collection = $collection;
    }

    public function createQueryBuilder(string $alias=''): self
    {
        $this->field = MailFolder::getFields();
        return parent::createQueryBuilder($alias);
    }

    public function convertElement($row): MailFolder
    {
        return MailFolder::initFromRow($row);
    }

    public function find($id): ?MailFolder
    {
        $this->collection->empty();
        return $this->createQueryBuilder('s')
            ->setCriteria(['s.id'=>$id])
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneBy(array $criteria, array $orderBy=[]): ?MailFolder
    {
        $collection = $this->findBy($criteria, $orderBy, 1);
        return $collection->valid() ? $collection->current() : null;
    }

    public function findAll(array $orderBy=['label'=>'ASC']): MailFolderCollection
    {
        return $this->findBy([], $orderBy);
    }

    public function findBy(array $criteria, array $orderBy=[], int $limit=-1): MailFolderCollection
    {
        return $this->createQueryBuilder('s')
            ->setCriteria($criteria)
            ->orderBy($orderBy)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function update(MailFolder $folder): void
    {
        $this->field = MailFolder::getFields();
        $this->updateQueryBuilder($folder)
            ->getQuery()
            ->execQuery();
    }
}
