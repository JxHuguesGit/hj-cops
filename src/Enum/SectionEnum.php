<?php
namespace src\Enum;

enum SectionEnum: int
{
    case ALPHA = 1;
    case BRAVO = 2;
    case GAMMA = 3;
    case DELTA = 4;
    case EPSILON = 5;
    case THETA = 6;
    case MU = 7;
    case PI = 8;
    case SIGMA = 9;
    case TAU = 10;

    public function label(): string
    {
        return match($this) {
            static::ALPHA   => 'A - Alpha',
            static::BRAVO   => 'A - Bravo',
            static::GAMMA   => 'A - Gamma',
            static::DELTA   => 'B - Delta',
            static::EPSILON => 'B - Epsilon',
            static::THETA   => 'B - Theta',
            static::MU      => 'B - Mu',
            static::PI      => 'C - Pi',
            static::SIGMA   => 'C - Sigma',
            static::TAU     => 'C - Tau',
            default         => 'Section inconnue',
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
