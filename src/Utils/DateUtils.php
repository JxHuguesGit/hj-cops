<?php
namespace src\Utils;

use src\Constant\ConstantConstant;

class DateUtils
{
    public static $arrFullMonths = [
        1=>'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
        'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
    ];
    public static $arrShortMonths = [
        1=>'Jan', 'Fév', 'Mars', 'Avr', 'Mai', 'Juin',
        'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'
    ];
    public static $arrFullDays = [0=>'Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];

    public static $arrShortDays = [0=>'Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'];

    public static $arrFullEnglishMonths = [
        1=>'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];

    public static $arrFullEnglishDays = [
        0=>'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
    ];
    public static $arrShortEnglishDays = [0=>'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

    public static $arrOrdinals = [1=>'first', 2=>'second', 3=>'third', 4=>'fourth', -1=>'last'];



    
    public static function getTempsEcoule(int $timestamp): string
    {
        $now = static::getCopsDate('ts');
        if ($now<=$timestamp+10) {
            $str = "A l'instant";
        } else {
            if ($now<=$timestamp+60) {
                $duree = $now-$timestamp;
                $unite = 'seconde';
            } elseif ($now<=$timestamp+60*60) {
                $duree = floor(($now-$timestamp)/60);
                $unite = 'minute';
            } elseif ($now<=$timestamp+60*60*24) {
                $duree = floor(($now-$timestamp)/(60*60));
                $unite = 'heure';
            } elseif ($now<=$timestamp+60*60*24*7) {
                $duree = floor(($now-$timestamp)/(60*60*24));
                $unite = 'jour';
            } elseif ($now<=$timestamp+60*60*24*7*4) {
                $duree = floor(($now-$timestamp)/(60*60*24*7));
                $unite = 'semaine';
            } elseif (date('Y', $now)!=date('Y', $timestamp)) {
                $duree = 0;
                $str = "Le ".(date('d', $timestamp)*1)."&nbsp;".static::$arrShortMonths[date('m', $timestamp)*1];
                $str .= " ".date('Y', $timestamp);
            } else {
                $duree = 0;
                $str = "Le ".(date('d', $timestamp)*1)."&nbsp;".static::$arrShortMonths[date('m', $timestamp)*1];
                
            }

            if ($duree!=0) {
                $str = "Il y a ".$duree.' '.$unite.($duree>1?'s':'');
            }
        }
        return $str;
    }

    public static function setCopsDate(int $tsNow): void
    {
        update_option(ConstantConstant::CST_COPSDATE, date('h:i:s d/m/Y', $tsNow));
    }

    public static function getCopsDate(string $strFormat): string
    {
        $strCopsDate = get_option(ConstantConstant::CST_COPSDATE);
        if ($strCopsDate=='') {
            $strCopsDate = '07:00:00 03/06/2030';
        }
 
        $h = substr((string) $strCopsDate, 0, 2);
        $i = substr((string) $strCopsDate, 3, 2);
        $s = substr((string) $strCopsDate, 6, 2);
        $d = substr((string) $strCopsDate, 9, 2);
        $m = substr((string) $strCopsDate, 12, 2);
        $y = substr((string) $strCopsDate, 15);
        return static::getStrDate($strFormat, [$d, $m, $y, $h, $i, $s]);
    }

    public static function getStrDate(string $strFormat, $when): string
    {
        [$d, $m, $y, $h, $i, $s] = $when;

        if ($strFormat=='ts') {
            $strFormatted = mktime($h, $i, $s, $m, $d, $y);
        } elseif ($strFormat=='dbDate') {
            $strFormatted = date('Y-m-d', mktime($h, $i, $s, $m, $d, $y));
        } else {
            $strFormatted = 'Error';
        }
        return $strFormatted;
    }

    /**
     * @since v1.23.05.05
     * @version v1.23.05.14
     *
    public static function isMonday(string $strDate): bool
    {
        [$d, $m, $y] = static::parseDate($strDate);
        return date('N', mktime(0, 0, 0, $m, $d, $y))==1;
    }

    /**
     *

    /**
     * @since 1.23.04.27
     * @version v1.23.04.30
     *
    public static function setCopsDate(int $tsNow): void
    { update_option(self::CST_CAL_COPSDATE, date('h:i:s d/m/Y', $tsNow)); }

    /**
     * Retourne au format donné le date obtenue en ajoutant $nbJours à la date passée.
     * $tmpArray attend les informations comme ça : [$nbJours, $nbMois, $nbAns]
     * @since v1.23.04.26
     * @version v1.23.05.21
     *
    public static function getDateAjout(
        string $strDate,
        array $tmpArray,
        string $dateFormat
    ): string
    {
        [$nbJours, $nbMois, $nbAns] = $tmpArray;
        [$d, $m, $y] = static::parseDate($strDate);
        return date($dateFormat, mktime(0, 0, 0, (int)$m+(int)$nbMois, (int)$d+(int)$nbJours, (int)$y+(int)$nbAns));
    }

    /**
     * @since v1.23.04.28
     * @version v1.23.08.05
     *
    public static function getStrDate(string $strFormat, $when): string
    {
        // On détermine si $when est string ou int.
        if (is_numeric($when)) {
            // Si int, c'est un timestamp.
            [$d, $m, $y, $h, $i, $s] = explode(' ', date('d m Y H i s', $when));
        } elseif (is_array($when)) {
            // A priori, rien à faire
            [$d, $m, $y, $h, $i, $s] = $when;
            $tsCops = mktime($h, $i, $s, $m, $d, $y);
            $his = $h.':'.$i.':'.$s;
            $dmy = $d.'/'.$m.'/'.$y;
        } else {
            // Si string, c'est une chaine de type YYYY-mm-dd HH:ii:ss ou autre.
            [$d, $m, $y, $h, $i, $s] = static::parseDate($when);
        }

        switch ($strFormat) {
            case 'sduk' :
                // sduk pour Short Day English
                $w = date('w', mktime(0, 0, 0, $m, $d, $y));
                $strFormatted = static::$arrShortEnglishDays[$w*1];
            break;
            case 'fd d fm' :
                // fd pour Full Day
                // fm pour Full Month
                $w = date('w', mktime(0, 0, 0, $m, $d, $y));
                $strFormatted = static::$arrFullDays[$w*1].' '.$d.' '.static::$arrFullMonths[$m*1];
            break;
            case 'd M y' :
                $strFormatted = $d.' '.static::$arrShortMonths[$m*1].' '.$y;
            break;
            case 'd M y H:i:s' :
                $strFormatted = $d.' '.static::$arrShortMonths[$m*1].' '.$y.' '.$h.':'.$i.':'.$s;
            break;
            case 'month y' :
                $strFormatted = static::$arrFullMonths[$m*1].' '.$y;
            break;
            case 'd month' :
                $strFormatted = $d.' '.static::$arrFullMonths[$m*1];
            break;
            case self::FORMAT_SIDEBAR_DATE :
                $strFormatted = static::$arrShortDays[date('N', $tsCops)].' '.$dmy.'<br>'.$his;
                break;
            case self::FORMAT_STRJOUR :
                $strJour = static::$arrFullDays[date('N', $tsCops)];
                $attributes = [$strJour, $d, static::$arrFullMonths[$m*1], $y];
                $strFormatted = implode(' ', $attributes);
                break;
            case self::FORMAT_TS_NOW :
                $strFormatted = mktime($h, $i, $s, $m, $d, $y);
                break;
            case self::FORMAT_TS_START_DAY :
                $strFormatted = mktime(0, 0, 0, $m, $d, $y);
                break;
            case self::FORMAT_DATE_DMONTHY :
                $strFormatted = $d.' '.static::$arrFullMonths[$m*1].' '.$y;
            break;
            case 'w d/m' :
                $w = date('w', mktime(0, 0, 0, $m, $d, $y));
                $strFormatted = static::$arrShortDays[$w*1].' '.$d.'/'.$m;
            break;
            case 'N'   :
            case 'W'   :
            case 'ga'  :
            case 'Ymd' :
            case 'd m y h i s' :
            case 'Y-m-d H:i:s' :
            case self::FORMAT_DATE_YMD    :
            case self::FORMAT_DATE_HIS    :
            case self::FORMAT_DATE_DMDY   :
            case self::FORMAT_DATE_MDY    :
            case self::FORMAT_DATE_DMY    :
            case self::FORMAT_DATE_YMDHIS :
                $tsDisplay = mktime($h, $i, $s, $m, $d, $y);
                $strFormatted = date($strFormat, $tsDisplay);
            break;
            default :
                $strFormatted = '';
            break;
        }
        return $strFormatted;
    }
    
    /**
     * Retourne au format donné le premier jour de la semaine de la date passée.
     * @since v1.23.04.26
     * @version v1.23.05.14
     *
    public static function getDateStartWeek(string $strDate, string $dateFormat): string
    {
        [$d, $m, $y, , ,] = static::parseDate($strDate);
        $n = date('N', mktime(0, 0, 0, $m, $d, $y));
        return static::getDateAjout($strDate, [1-$n, 0, 0], $dateFormat);
    }
    
    /**
     * Retourne au format donné le premier jour du mois de la date passée.
     * @since v1.23.05.03
     * @version v1.23.05.07
     *
    public static function getDateStartWeekMonth(string $strDate, string $dateFormat): string
    {
        [, $m, $y, , ,] = static::parseDate($strDate);
        [$fN, $fd, $fm, $fY] = explode(' ', date('N d m Y', mktime(0, 0, 0, $m, 1, $y)));
        if ($fN!=1) {
            [$fd, $fm, $fY] = explode(' ', date('d m Y', mktime(0, 0, 0, $m, 2-$fN, $y)));
        }
        return date($dateFormat, mktime(0, 0, 0, $fm, $fd, $fY));
    }

    /**
     * Retourne un tableau [$d, $m, $y, $h, $i, $s] de la date passée.
     * @since v1.23.04.26
     * @version v1.23.04.30
     *
    public static function parseDate(string $strDate): array
    {
        // On attend un des formats ci-dessous
        // YYYY-MM-DD
        $patternDate = "/^(\d{4})-(\d{2})-(\d{2})$/";
        // HH:II:SS - Note : ":SS" peut ne pas être présent
        $patternHour = "/^(\d{2}):(\d{2}):?(\d{2})?$/";
        // YYYY-MM-DD HH:II:SS - Note : ":SS" peut ne pas être présent
        $patternBoth = "/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):?(\d{2})?$/";

        if (preg_match($patternDate, $strDate, $matches)) {
            $arrParsed = [$matches[3], $matches[2], $matches[1], 0, 0, 0];
        } elseif (preg_match($patternHour, $strDate, $matches)) {
            $arrParsed = [0, 0, 0, $matches[1], $matches[2], $matches[3] ?? 0];
        } elseif (preg_match($patternBoth, $strDate, $matches)) {
            $arrParsed = [$matches[3], $matches[2], $matches[1], $matches[4], $matches[5], $matches[6] ?? 0];
        } else {
            $arrParsed = [0, 0, 0, 0, 0, 0];
        }
        return $arrParsed;
    }

    /**
     * @since v1.23.05.10
     * @version v1.23.05.14
     *
    public static function getNbDaysBetween(string $firstDate, string $secondDate): int
    {
        // On retourne la différence de jour entre la date la plus élevée et la moins élevée.
        if ($firstDate>$secondDate) {
            $startDate = $secondDate;
            $endDate = $firstDate;
        } else {
            $startDate = $firstDate;
            $endDate = $secondDate;
        }
        // On calcule les timestamp des deux dates.
        [$sy, $sm, $sd] = explode('-', $startDate);
        [$ey, $em, $ed] = explode('-', $endDate);

        $tsStart = mktime(0, 0, 0, $sm, $sd, $sy);
        $tsEnd = mktime(0, 0, 0, $em, $ed, $ey);

        return round(($tsEnd-$tsStart)/(60*60*24));
    }
    */
}
