<?php
namespace src\Constant;

interface LabelConstant
{
    public const LBL_HOME               = 'Accueil';
    public const LBL_NO_RESULTS         = 'Aucun résultat';
    public const LBL_LIBRARY            = 'Bibliothèque';
    public const LBL_DESK               = 'Bureau';
    public const LBL_CATEGORY           = 'Catégorie';
    public const LBL_SKILLS             = 'Compétences';
    public const LBL_LOGIN              = 'Connexion';
    public const LBL_TRASH              = 'Corbeille';
    public const LBL_ID                 = 'Identifiant';
    public const LBL_LEVEL              = 'Niveau';
    public const LBL_NAME               = 'Nom';
    public const LBL_NOTIFICATIONS      = 'Notifications';
    public const LBL_SETTINGS           = 'Paramètres';
    public const LBL_PROFILE            = 'Profil';
    public const LBL_SOURCE             = 'Source';
    public const LBL_COURSES            = 'Stages';

    public const LBL_ERR_LOGIN          = 'Une erreur est survenue lors de la saisie de votre identifiant et de votre mot de passe.<br>L\'un des champs était vide, ou les deux ne correspondaient pas à une valeur attendue.<br>Veuillez réessayer ou contacter un administrateur.<br><br>';
    public const LBL_ERR_MDPCHG         = 'Une erreur est survenue lors du changement de votre mot de passe.<br>Soit vous n\'avez pas saisi correctement votre ancien mot de passe, soit le nouveau mot de passe et sa confirmation ne correspondent pas.<br><br>';
    
}
