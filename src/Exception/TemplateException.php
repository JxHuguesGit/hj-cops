<?php
namespace src\Exception;

class TemplateException extends \Exception
{
    public function __construct(string $tpl)
    {
        throw new \Exception("Fichier $tpl introuvable.<br>Vérifier le chemin ou la présence.");
    }
}
