<?php
namespace src\Entity;

use src\Collection\MailPlayerCollection;
use src\Constant\FieldConstant;
use src\Controller\MailPlayerController;
use src\Repository\MailPlayerRepository;

class MailPlayer extends Entity
{
    //////////////////////////////////////////////////
    // ATTRIBUTES
    //////////////////////////////////////////////////
    protected int $id;
    protected string $mail;
    protected string $user;
    protected string $playerId;

    protected MailPlayerRepository $repository;

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
        $this->repository = new MailPlayerRepository(new MailPlayerCollection());
    }

    public function __toString()
    {
        $str  = ($this->id ?? '').' - ';
        $str .= '<br>';
        return $str;
    }

    public static function initFromRow($row): MailPlayer
    {
        $obj = new MailPlayer();
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
            FieldConstant::MAIL,
            FieldConstant::USER,
            FieldConstant::PLAYERID,
        ];
    }

    public function getController(): MailPlayerController
    {
        return new MailPlayerController($this);
    }
}
