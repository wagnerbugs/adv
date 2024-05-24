<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TreatmentPronounEnum: int implements HasLabel, HasColor
{
    case MR = 1;
    case MRS = 2;
    case MISS = 3;
    case LAWYER = 4;
    case YOUR_HONOR = 5;
    case YOUR_EXCELLENCY = 6;
    case DOCTOR = 7;
    case PROFESSOR = 8;
    case MASTER = 9;
    case PHD = 10;
    case YOUR_MAGNIFICENCE = 11;
    case CAPTAIN = 12;
    case MAJOR = 13;
    case ADMIRAL = 14;
    case GENERAL = 15;
    case COMMANDER = 16;
    case MAYOR = 17;
    case GOVERNOR = 18;
    case PRESIDENT = 19;
    case YOUR_LORDSHIP = 20;
    case FATHER = 21;
    case BISHOP = 22;
    case CARDINAL = 23;
    case LORD = 24;
    case YOUR_EMINENCE = 25;
    case YOUR_REVERENCE = 26;
    case HIS_HOLINESS = 27;
    case KING = 28;
    case QUEEN = 29;
    case PRINCE = 30;
    case PRINCESS = 31;
    case YOUR_MAJESTY = 32;
    case YOUR_HIGHNESS = 33;
    case FEMALE_PASTOR = 34;
    case PASTOR = 35;

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MR => 'Senhor - Sr.',
            self::MRS => 'Senhora - Sra.',
            self::MISS => 'Senhorita - Srta.',
            self::LAWYER => 'Advogado',
            self::YOUR_HONOR => 'Meritíssimo Juízo',
            self::YOUR_EXCELLENCY => 'Vossa Excelência',
            self::DOCTOR => 'Doutor',
            self::PROFESSOR => 'Professor',
            self::MASTER => 'Mestre',
            self::PHD => 'PhD',
            self::YOUR_MAGNIFICENCE => 'Vossa Magnificência',
            self::CAPTAIN => 'Capitão',
            self::MAJOR => 'Major',
            self::ADMIRAL => 'Almirante',
            self::GENERAL => 'General',
            self::COMMANDER => 'Comandante',
            self::MAYOR => 'Prefeito',
            self::GOVERNOR => 'Governador',
            self::PRESIDENT => 'Presidente',
            self::YOUR_LORDSHIP => 'Vossa Senhoria',
            self::FATHER => 'Padre',
            self::BISHOP => 'Bispo',
            self::CARDINAL => 'Cardeal',
            self::LORD => 'Dom',
            self::YOUR_EMINENCE => 'Vossa Eminência',
            self::YOUR_REVERENCE => 'Vossa Reverência',
            self::HIS_HOLINESS => 'Vossa Santidade',
            self::KING => 'Rei',
            self::QUEEN => 'Rainha',
            self::PRINCE => 'Príncipe',
            self::PRINCESS => 'Princesa',
            self::YOUR_MAJESTY => 'Vossa Majestade',
            self::YOUR_HIGHNESS => 'Vossa Alteza',
            self::FEMALE_PASTOR => 'Pastora',
            self::PASTOR => 'Pastor',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::MR => 'primary',
            self::MRS => 'primary',
            self::MISS => 'primary',
            self::LAWYER => 'primary',
            self::YOUR_HONOR => 'primary',
            self::YOUR_EXCELLENCY => 'primary',
            self::DOCTOR => 'primary',
            self::PROFESSOR => 'primary',
            self::MASTER => 'primary',
            self::PHD => 'primary',
            self::YOUR_MAGNIFICENCE => 'primary',
            self::CAPTAIN => 'primary',
            self::MAJOR => 'primary',
            self::ADMIRAL => 'primary',
            self::GENERAL => 'primary',
            self::COMMANDER => 'primary',
            self::MAYOR => 'primary',
            self::GOVERNOR => 'primary',
            self::PRESIDENT => 'primary',
            self::YOUR_LORDSHIP => 'primary',
            self::FATHER => 'primary',
            self::BISHOP => 'primary',
            self::CARDINAL => 'primary',
            self::LORD => 'primary',
            self::YOUR_EMINENCE => 'primary',
            self::YOUR_REVERENCE => 'primary',
            self::HIS_HOLINESS => 'primary',
            self::KING => 'primary',
            self::QUEEN => 'primary',
            self::PRINCE => 'primary',
            self::PRINCESS => 'primary',
            self::YOUR_MAJESTY => 'primary',
            self::YOUR_HIGHNESS => 'primary',
            self::FEMALE_PASTOR => 'primary',
            self::PASTOR => 'primary',
        };
    }
}
