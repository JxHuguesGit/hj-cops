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
    case Cpt = 7;

    public function label(): string
    {
        return match($this) {
            static::DetI   => 'Détective I',
            static::DetII  => 'Détective II',
            static::DetIII => 'Détective III',
            static::LtnI   => 'Lieutenant I',
            static::LtnII  => 'Lieutenant II',
            static::LtnIII => 'Lieutenant III',
            static::Cpt    => 'Capitaine',
            default        => 'Rang inconnu',
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
