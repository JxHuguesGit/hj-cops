<?php
namespace src\Entity;

use src\Collection\PlayerSkillCollection;
use src\Collection\SkillCollection;
use src\Constant\FieldConstant;
use src\Repository\SkillRepository;
use src\Repository\PlayerSkillRepository;

class PlayerSkill extends Entity
{
    //////////////////////////////////////////////////
    // ATTRIBUTES
    //////////////////////////////////////////////////
    protected int $id;
    protected int $playerId;
    protected int $skillId;
    protected int $score;

    protected PlayerSkillRepository $repository;

    //////////////////////////////////////////////////
    // CONSTRUCT
    //////////////////////////////////////////////////
    public function __contruct(array $attributes=[])
    {
        parent::__contruct($attributes);
        $this->initRepositories();
    }

    private function initRepositories()
    {
        $this->repository = new PlayerSkillRepository(new PlayerSkillCollection());
    }

    public static function initFromRow($row): PlayerSkill
    {
        $obj = new PlayerSkill();
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
            FieldConstant::PLAYERID,
            FieldConstant::SKILLID,
            FieldConstant::SCORE,
        ];
    }

    public function getSkill(): Skill
    {
        $repository = new SkillRepository(new SkillCollection());
        return $repository->find($this->skillId);
    }
}
