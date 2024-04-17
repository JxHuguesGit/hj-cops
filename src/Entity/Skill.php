<?php
namespace src\Entity;

use src\Collection\SkillCollection;
use src\Constant\FieldConstant;
use src\Controller\SkillController;
use src\Repository\SkillRepository;

class Skill extends Entity
{
    //////////////////////////////////////////////////
    // ATTRIBUTES
    //////////////////////////////////////////////////
    protected int $id;
    protected string $name;
    protected string $description;
    protected string $uses;
    protected int $specLevel;
    protected int $skillId;
    protected bool $padUsable;
    protected string $reference;
    protected string $defaultAttribute;

    protected SkillRepository $repository;

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
        $this->repository = new SkillRepository(new SkillCollection());
    }

    public static function initFromRow($row): Skill
    {
        $obj = new Skill();
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
            FieldConstant::NAME,
            FieldConstant::DESCRIPTION,
            FieldConstant::USES,
            FieldConstant::SPECLEVEL,
            FieldConstant::SKILLID,
            FieldConstant::PADUSABLE,
            FieldConstant::REFERENCE,
            FieldConstant::DEFAULTABILITY,
        ];
    }

    public function update(): void
    {
        $this->initRepositories();
        $this->repository->update($this);
    }

    public function getController(): SkillController
    {
        return new SkillController($this);
    }

    public function getParentSkill(): Skill
    {
        $this->initRepositories();
        return $this->repository->find($this->skillId);
    }

    public function getFullName(): string
    {
        if ($this->skillId==0) {
            return $this->name;
        } else {
            $pSkill = $this->getParentSkill();
            return $pSkill->getFullName().' ['.$this->name.']';
        }
    }
}
