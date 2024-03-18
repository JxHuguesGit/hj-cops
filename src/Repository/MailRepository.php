<?php
namespace src\Repository;

use src\Collection\MailCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Entity\Mail;

class MailRepository extends Repository
{
    public function __construct(MailCollection $collection)
    {
        $this->table = 'copsMail';
        $this->collection = $collection;
    }

    public function createQueryBuilder(): self
    {
        $this->field = Mail::getFields();
        return parent::createQueryBuilder();
    }

    public function convertElement($row): Mail
    {
        return Mail::initFromRow($row);
    }

    public function findAll(array $orderBy=[FieldConstant::SENTDATE=>ConstantConstant::CST_ASC]): MailCollection
    {
        return $this->findBy([], $orderBy);
    }

    




    

    public function update(Mail $mail): void
    {
        $this->field = Mail::getFields();
        $this->updateQueryBuilder($mail)
            ->getQuery()
            ->execQuery();
    }
}
