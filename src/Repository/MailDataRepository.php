<?php
namespace src\Repository;

use src\Collection\MailDataCollection;
use src\Entity\MailData;

class MailDataRepository extends Repository
{
    public function __construct(MailDataCollection $collection)
    {
        $this->table = 'copsMailData';
        $this->collection = $collection;
    }

    public function createQueryBuilder(): self
    {
        $this->field = MailData::getFields();
        return parent::createQueryBuilder();
    }

    public function convertElement($row): MailData
    {
        return MailData::initFromRow($row);
    }









    public function update(MailData $mailData): void
    {
        $this->field = MailData::getFields();
        $this->updateQueryBuilder($mailData)
            ->getQuery()
            ->execQuery();
    }
    
    public function findByAndOrdered(array $criteria=[], array $orderBy=[], int $limit=-1): MailDataCollection
    {
        $this->field = MailData::getFields();
        $this->baseQuery  = "SELECT cmd.`".implode('`, cmd.`', $this->field)."` ";
        $this->baseQuery .= "FROM ".$this->table." AS cmd ";
        $this->baseQuery .= "LEFT JOIN `copsMail` AS cm ON cmd.mailId = cm.id ";

        return $this->setCriteria($criteria)
            ->orderBy($orderBy)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

}
