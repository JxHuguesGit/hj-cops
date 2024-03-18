<?php
namespace src\Controller;

class AdminPageController extends UtilitiesController
{
    public int $rootLength = 0;
    public array $arrFilesTemplate = [];

    public function getAdminContentPage(): string
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
        
        return '';
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
            if ($data['file']=='Bad Constante') {
                $strErrConstantes .= "Supprimer la constante <strong>$key</strong> "
                    ."car le fichier associé n'existe pas.<br>";
            } elseif ($data['file']=='Bad Template') {
                $strErrTemplates .= "Supprimer le template <strong>$key</strong> "
                    ."car il n'est pas associé à une constante.<br>";
            } elseif (!$data['used']) {
                $strErrConstantes .= "Supprimer la constante <strong>$key</strong> car elle n'est jamais utilisée.<br>";
                $strErrTemplates .= "Supprimer le template <strong>".$data['file']
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
                    $this->arrFilesTemplate[$strCst] = ['file'=>$strValue, 'used'=>false];
                    unset($this->arrFilesTemplate[$strValue]);
                } else {
                    $this->arrFilesTemplate[$strCst] = ['file'=>'Bad Constante'];
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
                    $this->arrFilesTemplate[substr($directory.$file, $this->rootLength)] = ['file'=>'Bad Template'];
                }
            }
        }
    }

}
