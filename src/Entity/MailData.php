<?php
namespace src\Entity;

use src\Collection\MailDataCollection;
use src\Constant\FieldConstant;
use src\Controller\MailDataController;
use src\Repository\MailDataRepository;

class MailData extends Entity
{
    //////////////////////////////////////////////////
    // ATTRIBUTES
    //////////////////////////////////////////////////
    protected int $id;
    protected int $mailId;
    protected int $toId;
    protected string $status;
    protected int $fromId;
    protected int $folderId;
    protected int $read;

    protected MailDataRepository $repository;

    //////////////////////////////////////////////////
    // CONSTRUCT
    //////////////////////////////////////////////////
    public function __construct(array $attributes=[])
    {
        parent::__construct($attributes);
        $this->initRepositories();
    }

    private function initRepositories()
    {
        $this->repository = new MailDataRepository(new MailDataCollection());
    }

    public function __toString()
    {
        $str  = ($this->id ?? '').' - ';
        $str .= '<br>';
        return $str;
    }

    public static function initFromRow($row): MailData
    {
        $obj = new MailData();
        $fields = $obj->getFields();
        foreach ($fields as $field) {
            $obj->setField($field, $row->{$field});
        }
        return $obj;
    }

    public static function getFields(): array
    {
        return [
            FieldConstant::ID,
            FieldConstant::MAILID,
            FieldConstant::TOID,
            FieldConstant::STATUS,
            FieldConstant::FROMID,
            FieldConstant::FOLDERID,
            FieldConstant::READ,
        ];
    }

    public function getController(): MailDataController
    {
        return new MailDataController($this);
    }
}
