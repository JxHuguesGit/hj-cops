<?php
namespace src\Entity;

use src\Collection\MailFolderCollection;
use src\Constant\FieldConstant;
use src\Controller\MailFolderController;
use src\Repository\MailFolderRepository;

class MailFolder extends Entity
{
    //////////////////////////////////////////////////
    // ATTRIBUTES
    //////////////////////////////////////////////////
    protected int $id;
    protected string $slug;
    protected string $label;
    protected string $icon;

    protected MailFolderRepository $repository;

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
        $this->repository = new MailFolderRepository(new MailFolderCollection());
    }

    public static function initFromRow($row): MailFolder
    {
        $obj = new MailFolder();
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
            FieldConstant::SLUG,
            FieldConstant::LABEL,
            FieldConstant::ICON,
        ];
    }

    public function getController(): MailFolderController
    {
        return new MailFolderController($this);
    }
}
