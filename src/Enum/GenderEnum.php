<?php
namespace src\Enum;

enum GenderEnum: int
{
    case Female = 1;
    case Male = 2;

    public function label(): string
    {
        return match($this) {
            static::Female => 'Female',
            static::Male   => 'Male',
            default        => 'Genre inconnu.',
        };
    }

    public static function fromDb(int $i): string
    {
        foreach (static::cases() as $element) {
            if ($element->value==$i) {
                return $element->label();
            }
        }
        return 'err';
    }
}
