<?php
namespace src\Repository;

use src\Collection\MailFolderCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Entity\MailFolder;

class MailFolderRepository extends Repository
{
    public function __construct(MailFolderCollection $collection)
    {
        $this->table = 'copsMailFolder';
        $this->collection = $collection;
    }

    public function createQueryBuilder(): self
    {
        $this->field = MailFolder::getFields();
        return parent::createQueryBuilder();
    }

    public function convertElement($row): MailFolder
    {
        return MailFolder::initFromRow($row);
    }

    public function findAll(
        array $orderBy=[FieldConstant::LABEL=>ConstantConstant::CST_ASC]
    ): MailFolderCollection
    {
        return $this->findBy([], $orderBy);
    }

  
    

    public function update(MailFolder $folder): void
    {
        $this->field = MailFolder::getFields();
        $this->updateQueryBuilder($folder)
            ->getQuery()
            ->execQuery();
    }
}
