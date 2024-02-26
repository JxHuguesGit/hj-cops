<?php
namespace src\Entity;

use src\Collection\MailCollection;
use src\Constant\FieldConstant;
use src\Controller\MailController;
use src\Repository\MailRepository;

class Mail extends Entity
{
    //////////////////////////////////////////////////
    // ATTRIBUTES
    //////////////////////////////////////////////////
    protected int $id;
    protected string $subject;
    protected string $content;
    protected string $sentDate;

    protected MailRepository $repository;

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
        $this->repository = new MailRepository(new MailCollection());
    }

    public function __toString()
    {
        $str  = ($this->id ?? '').' - ';
        $str .= '<br>';
        return $str;
    }

    public static function initFromRow($row): Mail
    {
        $obj = new Mail();
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
            FieldConstant::SUBJECT,
            FieldConstant::CONTENT,
            FieldConstant::SENTDATE,
        ];
    }

    public function getController(): MailController
    {
        return new MailController($this);
    }
}
