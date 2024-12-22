<?php
namespace src\Entity;

use src\Collection\Collection;
use src\Constant\ConstantConstant;
use src\Constant\TemplateConstant;
use src\Repository\Repository;
use src\Utils\FichierUtils;

class FichierDump extends FichierUtils
{
    protected $dumpMode;
    protected $tables;

    public function __construct(string $dumpMode)
    {
        $fileName = 'dump_';
        switch ($dumpMode) {
            case '01' :
                $fileName .= 'data_';
            break;
            case '10' :
                $fileName .= 'tables_';
            break;
            case '11' :
            default :
                $fileName .= 'complete_';
            break;
        }
        $this->dumpMode = $dumpMode;
        $fileName .= strpos(PLUGIN_PATH, 'wamp64')!==false ? 'dev_' : 'prod_';
        $fileName .= date('Ymd_His').'.'.ConstantConstant::CST_EXT_SQL;
        parent::__construct(TemplateConstant::SQL_PATH, $fileName);
    }

    public function doDump(): void
    {
        if ($this->openFile(ConstantConstant::CST_FILE_WRITE)) {
            $this->creerDebutFichier()
                ->recupererListeTables('cops')
                ->creerFinFichier()
                ->closeFile();
        }
    }

    private function creerDebutFichier(): self
    {
        $this->ecrireLigne('-- SQL Dump')
            ->ecrireLigne()
            ->ecrireLigne('SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";')
            ->ecrireLigne('START TRANSACTION;')
            ->ecrireLigne('SET time_zone = "+01:00";')
            ->ecrireLigne();
        return $this;
    }

    private function creerFinFichier(): self
    {
        $this->ecrireLigne('-- --------------------------------------------------------')
            ->ecrireLigne()
            ->ecrireLigne('-- Fin de la génération : '.date('dmY à H:i'));
        return $this;
    }

    private function recupererListeTables(string $prefix=''): self
    {
        global $wpdb;

        $sql = "SELECT TABLE_NAME, ENGINE, AUTO_INCREMENT, TABLE_COLLATION "
            . "FROM information_schema.TABLES "
            . "WHERE TABLE_NAME LIKE '%s';";
        $query = $wpdb->prepare($sql, [$prefix.'%']);
        $rows  = $wpdb->get_results($query);

        while (!empty($rows)) {
            $row = array_shift($rows);
            $this->ecrireTable($row);
        }

        return $this;
    }

    private function ecrireTable($obj): self
    {
        global $wpdb;

        $sql = "SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, EXTRA, COLUMN_KEY "
            . "FROM information_schema.COLUMNS "
            . "WHERE TABLE_NAME = '%s';";
        $query = $wpdb->prepare($sql, [$obj->TABLE_NAME]);
        $rows  = $wpdb->get_results($query);

        $this->ecrireLigne('-- --------------------------------------------------------')
            ->ecrireLigne();

        if ($this->dumpMode[0]==1) {
            $this->ecrireStructure($obj, $rows);
        }

        if ($this->dumpMode[1]==1) {
            $this->ecrireDonnees($obj, $rows);
        }

        return $this;
    }

    private function ecrireDonnees($obj, $rows): void
    {
        global $wpdb;

        $tableName = $obj->TABLE_NAME;

        $sortField = '';
        $strInsert = 'INSERT INTO `'.$tableName.'` (';
        foreach ($rows as $row) {
            $strInsert .= '`'.$row->COLUMN_NAME.'`, ';
            if ($row->COLUMN_KEY=='PRI') {
                $sortField = $row->COLUMN_NAME;
            }
        }

        $sql = 'SELECT * FROM `'.$tableName.'` ORDER BY '.$sortField.' ASC;';
        $query = $wpdb->prepare($sql);
        $dataRows  = $wpdb->get_results($query);
        $nb = count($dataRows);
        if ($nb==0) {
            return;
        }
        
        $this->ecrireLigne('--')
            ->ecrireLigne('-- Données de la table `'.$tableName.'`')
            ->ecrireLigne('--')
            ->ecrireLigne();
        $strInsert = substr($strInsert, 0, -2).') VALUES';
        $this->ecrireLigne($strInsert);

        $cpt = 1;
        while (!empty($dataRows)) {
            $strData = '(';
            $dataRow = array_shift($dataRows);
            foreach ($rows as $row) {
                $value = str_replace(["\r\n", "'"], ['\r\n', "''"], $dataRow->{$row->COLUMN_NAME});
                if (in_array($row->COLUMN_TYPE, ['int', 'smallint', 'tinyint', 'int unsigned', 'smallint unsigned', 'tinyint unsigned'])) {
                    $strData .= $value.', ';
                } else {
                    $strData .= '\'' . $value . '\', ';
                }
            }
            $strData = substr($strData, 0, -2) . ')' . ($cpt==$nb ? ';' : ',');
            $this->ecrireLigne($strData);
            $cpt++;
        }

        // On doit requêter la table et afficher toutes les données.


        $this->ecrireLigne();
    }

    private function ecrireStructure($obj, $rows): void
    {
        $tableName = $obj->TABLE_NAME;
        $engine = $obj->ENGINE;
        $autoIncremente = $obj->AUTO_INCREMENT;
        $charset = explode('_', $obj->TABLE_COLLATION);

        $this->ecrireLigne('--')
            ->ecrireLigne('-- Structure de la table `'.$tableName.'`')
            ->ecrireLigne('--')
            ->ecrireLigne()
            ->ecrireLigne('DROP TABLE IF EXISTS `'.$tableName.'`;')
            ->ecrireLigne('CREATE TABLE IF NOT EXISTS `'.$tableName.'` (');

        $keys = [];
        while (!empty($rows)) {
            $row = array_shift($rows);
            $columnName = $row->COLUMN_NAME;
            $columnType = str_replace('unsigned', 'UNSIGNED', $row->COLUMN_TYPE);
            $this->getKeys($row, $keys);

            $strCol  = '  `'.$columnName.'`'
                . ' '.$columnType
                . ' '.($row->IS_NULLABLE=='NO' ? 'NOT' : '') . ' NULL'
                . ($row->COLUMN_DEFAULT!=null ? ' DEFAULT \''.$row->COLUMN_DEFAULT.'\'' : '')
                . ($row->EXTRA!='' ? ' '.strtoupper($row->EXTRA) : '')
                . ',';

            $this->ecrireLigne($strCol);
        }
        if (!empty($keys)) {
            $nb = count($keys);
            $cpt = 1;
            foreach ($keys as $key) {
                $this->ecrireLigne($key.($cpt!=$nb ? ',' : ''));
                $cpt++;
            }
        }

        $this->ecrireLigne(') ENGINE='.$engine.' AUTO_INCREMENT='.$autoIncremente.' DEFAULT CHARSET='.$charset[0].';')
            ->ecrireLigne();
    }

    private function getKeys($row, &$keys): void
    {
        $columnName = $row->COLUMN_NAME;
        if ($row->COLUMN_KEY=='PRI') {
            $keys[] = '  PRIMARY KEY (`'.$columnName.'`)';
        } elseif ($row->COLUMN_KEY=='MUL') {
            $keys[] = '  KEY `'.$columnName.'` (`'.$columnName.'`)';
        } elseif ($row->COLUMN_KEY=='UNI') {
            $keys[] = '  UNIQUE KEY `'.$columnName.'` (`'.$columnName.'`)';
        }
    }

    private function doRestore(): self
    {
        global $wpdb;
        $sql = file_get_contents($this->getFullUrl());
        //$wpdb->query($sql);
        echo "doRestore à développer.<br>";
        return $this;
    }
}
