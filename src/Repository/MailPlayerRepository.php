<?php
namespace src\Repository;

use src\Collection\MailPlayerCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Entity\MailPlayer;

class MailPlayerRepository extends Repository
{
    public function __construct(MailPlayerCollection $collection)
    {
        $this->table = 'copsMailPlayer';
        $this->collection = $collection;
    }

    public function createQueryBuilder(): self
    {
        $this->field = MailPlayer::getFields();
        return parent::createQueryBuilder();
    }

    public function convertElement($row): MailPlayer
    {
        return MailPlayer::initFromRow($row);
    }

    public function findAll(array $orderBy=[FieldConstant::MAIL=>ConstantConstant::CST_ASC]): MailPlayerCollection
    {
        return $this->findBy([], $orderBy);
    }




    
    

    public function update(MailPlayer $mailPlayer): void
    {
        $this->field = MailPlayer::getFields();
        $this->updateQueryBuilder($mailPlayer)
            ->getQuery()
            ->execQuery();
    }
}
