<?php
namespace src\Controller;

use src\Constant\ConstantConstant;
use src\Constant\TemplateConstant;
class AdminCodePageController extends AdminPageController
{
    public string $urlBase = '/wp-content/plugins/hj-cops/';
    public string $directory = '';
    public int $rootLength = 0;
    public array $arrFilesTemplate = [];
    public array $arrFiles = [];

    public function __construct($arrUri=[], string $slug='')
    {
        parent::__construct($arrUri, $slug);
        $this->directory = $_SERVER['DOCUMENT_ROOT'].$this->urlBase;
        $this->rootLength = strlen($this->directory);
    }

    public function getAdminContentPage(): string
    {
        ///////////////////////////////////////////////
        // On analyse les templates
        //$this->analyseTemplates();
        ///////////////////////////////////////////////

        ///////////////////////////////////////////////
        // On analyse les constantes
        $this->analyseConstants();
        $this->getConstantsContent();
        $arr = $this->getFiles($this->directory.TemplateConstant::SRC_PATH);
        while (!empty($arr)) {
            $file = array_shift($arr);
            if (strpos($file, '/Constant/')!==false) {
                continue;
            }
            $this->parseFileForConstants($file);
        }
        //var_dump($this->arrFiles);
        ///////////////////////////////////////////////

        return 'WIP';
    }

    private function parseFileForConstants(string $file): void
    {
        $pattern = "/'([^']*)'/";
        $firstString = true;
        $numLigne = 0;
        $numOcc = 0;
        $fp = fopen($file, 'r');
        while (!feof($fp)) {
            $numLigne++;
            $line = fgets($fp);
            if (preg_match_all($pattern, $line, $matches)) {
                $arr = $matches[1];
                $nbMatches = count($arr);
                for ($i=0; $i<$nbMatches; $i++) {
                    $strConstant = $arr[$i];
                    if ($strConstant=='') {
                        continue;
                    }
                    if ($firstString) {
                        echo $file.'<br>';
                        $firstString = false;
                    }
                    $numOcc++;
                    if (in_array($strConstant, $this->arrFiles['constant'])) {
                        echo $numLigne . ' : '.$strConstant.'<br>';
                    } else {
                        echo $numLigne . ' (TODO) : ' . $strConstant.'<br>';
                    }
                }
            }
        }
        if ($numOcc!=0) {
            echo 'Fin fichier '.$file.'. '.$numOcc.' occurrences à traiter.<br>';
        }

    }

    /** ///////////////////////////////////////////////
     * getFiles
     *  ///////////////////////////////////////////////
     * Retourne tous les fichiers du répertoire passé en paramètre.
     * Si $recursive est à true, renvoie aussi les fichiers des sous répertoires.
     *  ///////////////////////////////////////////////
     * @param string $subDir     : le répertoire initial à parcourir
     * @param bool $recursive    : si true, on effectue une recherche dans les sous répertoires
     *  ///////////////////////////////////////////////
     * @return array $files      : la liste des fichiers trouvés
     */
    private function getFiles(string $subDir='', bool $recursive=true): array
    {
        $files = [];
        $directory = $subDir;
        $handler = opendir($directory);
        while ($file = readdir($handler)) {
            // if file isn't this directory or its parent, add it to the results
            if ($file != "." && $file != "..") {
                if (is_dir($directory.$file.'/') && $recursive) {
                    $files = array_merge($files, $this->getFiles($directory.$file.'/'));
                } else {
                    $files[] = $directory.$file;
                }
            }
        }
        return $files;
    }

    private function getConstantsContent(): void
    {
        $pattern = "/public const ([A-Z_]*) *= (.*);/";
        foreach ($this->arrFiles['constant'] as $file => $fileContent) {
            $urlFile = $this->directory.'src/Constant/'.$file.'.php';
            $fp = fopen($urlFile, 'r');
            while (!feof($fp)) {
                $line = fgets($fp);
                if (preg_match($pattern, $line, $matches) && !in_array(substr($matches[1], 0, 4), ['LBL_', 'TPL_'])) {
                    $fileContent[$matches[1]] = substr($matches[2], 1, -1);
                }
            }
            $this->arrFiles['constant'] = array_merge($this->arrFiles['constant'], $fileContent);
        }
    }

    private function analyseConstants(): void
    {
        ///////////////////////////////////////////////
        // On parcourt le répertoire src/Constant pour récupérer les différents fichiers de Constant.
        $directory = $this->directory.'src/Constant/';
        $handler = opendir($directory);
        // open directory and walk through the filenames
        while ($file = readdir($handler)) {
            // if file isn't this directory or its parent, add it to the results
            if ($file != "." && $file != "..") {
                $this->arrFiles['constant'][substr($file, 0, -4)] = [];
            }
        }
        ///////////////////////////////////////////////
    }

    private function analyseTemplates(): void
    {
        ///////////////////////////////////////////////
        // On parcourt le répertoire templates pour récupérer les différents fichiers de template.
        // On stocke chaque fichier tpl dans un tableau.
        $urlBase = '/wp-content/plugins/hj-cops/';
        $directory = $_SERVER['DOCUMENT_ROOT'].$urlBase;
        $this->rootLength = strlen($directory);
        $this->analyseDir($directory.'templates/');
        ///////////////////////////////////////////////

        ///////////////////////////////////////////////
        // On ouvre le fichier src\Constant\TemplateConstant.php
        // On va parcourir chaque ligne du fichier pour trouver les constants équivalentes
        $this->getConstants($directory.'src/Constant/TemplateConstant.php');
        ///////////////////////////////////////////////
        
        ///////////////////////////////////////////////
        // On va parcourir tous les fichiers du répertoire Controller
        // Et on va chercher tous les templates utilisés.
        // On va vérifier qu'ils existent. Et on va aussi vérifier que touts les constantes de Template sont utilisées.
        $this->analyseControllers($directory.'src/Controller/');
        ///////////////////////////////////////////////
        
        ///////////////////////////////////////////////
        // On affiche le bilan
        echo $this->getBilanTemplates();
        ///////////////////////////////////////////////
    }
    
    private function analyseControllers($directory): string
    {
        $handler = opendir($directory);
        
        while ($file = readdir($handler)) {
            if ($file != "." && $file != "..") {
                echo "Traitement du fichier $file.<br>";
                
                $fp = fopen($directory.$file, 'r');
                while (!feof($fp)) {
                    $line = fgets($fp);
                    $posTC = strpos($line, 'TemplateConstant::');
                    if ($posTC!==false) {
                        $posTC += 18;
                        $posEnd = strpos($line, ',', $posTC);
                        if($posEnd===false) {
                            $posEnd = strpos($line, ')', $posTC);
                        }
                        if ($posEnd!==false) {
                            $strConst = trim(substr($line, $posTC, $posEnd-$posTC));
                            if (isset($this->arrFilesTemplate[$strConst])) {
                                $this->arrFilesTemplate[$strConst]['used'] = true;
                            } else {
                                echo $strConst.'<br>';
                            }
                        }
                    }
                }
            }
        }

        $fp = fopen($directory.'../../templates/base.php', 'r');
        while (!feof($fp)) {
            $line = fgets($fp);
            $posTC = strpos($line, 'TemplateConstant::');
            if ($posTC!==false) {
                $posTC += 18;
                $posEnd = strpos($line, ',', $posTC);
                if($posEnd===false) {
                    $posEnd = strpos($line, ')', $posTC);
                }
                if ($posEnd!==false) {
                    $strConst = trim(substr($line, $posTC, $posEnd-$posTC));
                    if (isset($this->arrFilesTemplate[$strConst])) {
                        $this->arrFilesTemplate[$strConst]['used'] = true;
                    } else {
                        echo $strConst.'<br>';
                    }
                }
            }
        }
        
        return '';
    }

    private function getBilanTemplates(): string
    {
        $strErrConstantes = '';
        $strErrTemplates  = '';
        foreach ($this->arrFilesTemplate as $key => $data) {
            if ($data[ConstantConstant::CST_FILE]=='Bad Constante') {
                $strErrConstantes .= "Supprimer la constante <strong>$key</strong> "
                    ."car le fichier associé n'existe pas.<br>";
            } elseif ($data[ConstantConstant::CST_FILE]=='Bad Template') {
                $strErrTemplates .= "Supprimer le template <strong>$key</strong> "
                    ."car il n'est pas associé à une constante.<br>";
            } elseif (!$data['used']) {
                $strErrConstantes .= "Supprimer la constante <strong>$key</strong> car elle n'est jamais utilisée.<br>";
                $strErrTemplates .= "Supprimer le template <strong>".$data[ConstantConstant::CST_FILE]
                    ."</strong> car sa constante associée n'est jamais utilisée.<br>";
            }
        }
        if ($strErrConstantes=='') {
            $strErrConstantes = "Pas d'anomalies dans les constantes.";
        }
        if ($strErrTemplates=='') {
            $strErrTemplates = "Pas d'anomalies dans les templates.";
        }
        return $strErrConstantes.'<br><br>'.$strErrTemplates;
    }
    
    private function getConstants($file): void
    {
        $fp = fopen($file, 'r');
        while (!feof($fp)) {
            $line = fgets($fp);
            $posCst = strpos($line, 'public const');
            if ($posCst!==false) {
                $posCst += 12;
                $posEqual = strpos($line, '=');
                $strCst = trim(substr($line, $posCst, $posEqual-$posCst));
                $strValue = trim(substr($line, $posEqual+3, -4));
                
                if (isset($this->arrFilesTemplate[$strValue])) {
                    $this->arrFilesTemplate[$strCst] = [ConstantConstant::CST_FILE=>$strValue, 'used'=>false];
                    unset($this->arrFilesTemplate[$strValue]);
                } else {
                    $this->arrFilesTemplate[$strCst] = [ConstantConstant::CST_FILE=>'Bad Constante'];
                }
            }
        }
    }
    
    private function analyseDir($directory, $rk=0): void
    {
        $handler = opendir($directory);
        
        // open directory and walk through the filenames
        while ($file = readdir($handler)) {

            // if file isn't this directory or its parent, add it to the results
            if ($file != "." && $file != "..") {
                if (is_dir($directory.$file.'/')) {
                    $this->analyseDir($directory.$file.'/', $rk+1);
                } elseif (substr($file, -3)!='php') {
                    $this->arrFilesTemplate[substr($directory.$file, $this->rootLength)] = [ConstantConstant::CST_FILE=>'Bad Template'];
                }
            }
        }
    }

}
