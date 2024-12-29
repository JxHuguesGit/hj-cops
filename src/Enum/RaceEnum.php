<?php
namespace src\Enum;

enum RaceEnum: int
{
    case American   = 1;
    case Arabic     = 2;
    case Australian = 3;
    case Brazil     = 4;
    case Chinese    = 5;
    case Croatian   = 6;
    case Czech      = 7;
    case Danish     = 8;
    case Dutch      = 9;
    case England    = 10;
    case Finnish    = 11;
    case French     = 12;
    case German     = 13;
    case Hispanic   = 14;
    case Hungarian  = 15;
    case Italian    = 16;
    case Japanese   = 17;
    case Norwegian  = 18;
    case Persian    = 19;
    case Polish     = 20;
    case Russian    = 21;
    case Scottish   = 22;
    case Slovenian  = 23;
    case Swedish    = 24;
    case Thai       = 25;
    case Vietnamese = 26;
    case AfroAmerican = 27;

    public function label(): string
    {
        return match($this) {
            static::American   => 'American',
            static::Arabic     => 'Arabic',
            static::Australian => 'Australian',
            static::Brazil     => 'Brazil',
            static::Chinese    => 'Chinese',
            static::Croatian   => 'Croatian',
            static::Czech      => 'Czech',
            static::Danish     => 'Danish',
            static::Dutch      => 'Dutch',
            static::England    => 'England',
            static::Finnish    => 'Finnish',
            static::French     => 'French',
            static::German     => 'German',
            static::Hispanic   => 'Hispanic',
            static::Hungarian  => 'Hungarian',
            static::Italian    => 'Italian',
            static::Japanese   => 'Japanese',
            static::Norwegian  => 'Norwegian',
            static::Persian    => 'Persian',
            static::Polish     => 'Polish',
            static::Russian    => 'Russian',
            static::Scottish   => 'Scottish',
            static::Slovenian  => 'Slovenian',
            static::Swedish    => 'Swedish',
            static::Thai       => 'Thai',
            static::Vietnamese => 'Vietnamese',
            static::AfroAmerican => 'Afro-American',
            default            => 'Naionalité inconnue.',
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
