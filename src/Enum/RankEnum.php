<?php
namespace src\Enum;

enum RankEnum: int
{
    case DetI = 1;
    case DetII = 2;
    case DetIII = 3;
    case LtnI = 4;
    case LtnII = 5;
    case LtnIII = 6;
    case CptI = 7;
    case CptII = 8;
    case CptIII = 9;

    public function label(): string
    {
        return match($this) {
            static::DetI   => 'Détective I',
            static::DetII  => 'Détective II',
            static::DetIII => 'Détective III',
            static::LtnI   => 'Lieutenant I',
            static::LtnII  => 'Lieutenant II',
            static::LtnIII => 'Lieutenant III',
            static::CptI   => 'Capitaine I',
            static::CptII  => 'Capitaine II',
            static::CptIII => 'Capitaine III',
            default        => 'Rang inconnu.',
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
