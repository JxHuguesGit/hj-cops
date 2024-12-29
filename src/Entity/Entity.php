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

    public function initAttributes(): self
    {
        $reflectionClass = new \ReflectionClass(Investigation::class);
        foreach ($reflectionClass->getProperties() as $property) {
            $name = $property->getName();
            $type = $property->getType();
            if ($type=='int') {
                $this->{$name} = 0;
            } elseif ($type=='string') {
                $this->{$name} = '';
            } else {
                echo "Un type d'attribut (".$type.") n'est pas prévu dans le processus d'initialisation.<br>";
            }
        }
    
        return $this;
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
