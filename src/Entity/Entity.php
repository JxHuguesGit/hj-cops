<?php
namespace src\Entity;

class Entity
{
    public function __construct(array $attributes=[])
    {
        if (!empty($attributes)) {
            foreach ($attributes as $key=>$value) {
                $this->{$key} = $value;
            }
        }
    }

    public function __toString(): string
    {
        $fields = static::getFields();
        $str = '';
        foreach ($fields as $field) {
            $str .= ($this->{$field} ?? '').' - ';
        }
        return substr($str, 0, -3).'<br>';
    }

    public function initRepository($repositories=[])
    {
        while (!empty($repositories)) {
            $repository = array_shift($repositories);
            $this->{$repository} = new $repository;
        }
    }

    public function getField(string $field): mixed
    {
        return $this->{$field};
    }

    public function setField(string $field, $value): void
    {
        if ($value==null) {
            $value = ' ';
        }
        $this->{$field} = $value;
    }

}
