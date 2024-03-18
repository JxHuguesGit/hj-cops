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
        $this->field = MailFolder::getFields();
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
  
}
