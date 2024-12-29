<?php
namespace src\Enum;

enum OccupationEnum: int
{
    case AssistantDistrictAttorney = 1;
    case DistrictAttorney = 2;

    public function label(): string
    {
        return match($this) {
            static::AssistantDistrictAttorney => 'Assistant District Attorney',
            static::DistrictAttorney          => 'District Attorney',
            default                           => 'Métier inconnu.',
        };
    }

    public static function fromDb(int $i): string
    {
        foreach (static::cases() as $element) {
            if ($element->value==$i) {
                return $element->label();
            }
        }
        return 'Métier inconnu.';
    }
}
