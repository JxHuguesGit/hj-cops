<?php
namespace src\Constant;

interface TemplateConstant
{
    public const ASSETS_PATH            = 'assets/';
    public const SQL_PATH               = self::ASSETS_PATH.'sql/';
    public const SRC_PATH               = 'src/';

    public const TPL_ADMINBASE          = 'templates/adminBase.tpl';

    public const TPL_CONN_PANEL         = 'templates/home/section-connexion-panel.tpl';
    public const TPL_NEW_PASSWORD_CARD  = 'templates/settings/change-password-card.tpl';
    public const TPL_SETTINGS_DASH      = 'templates/settings/dashboard.tpl';

    public const TPL_CENTRAL_PANEL      = 'templates/central/main-panel.tpl';

    public const TPL_ADJ_PANEL          = 'templates/library/adj-panel.tpl';
    public const TPL_LIBRARY_CARD       = 'templates/library/library-card.tpl';
    public const TPL_LIBRARY_PANEL      = 'templates/library/main-panel.tpl';
    public const TPL_LIBRARY_SUBPANEL   = 'templates/library/sub-panel.tpl';

    public const TPL_MAIL_PANEL         = 'templates/mail/main-panel.tpl';
    public const TPL_MAIL_LIST          = 'templates/mail/mail-list.tpl';
    public const TPL_MAIL_VIEW          = 'templates/mail/mail-view.tpl';
    
    public const TPL_PROFILE_CARDS      = 'templates/profile/section-fiche-personnage.tpl';
    public const TPL_PROFILE_CARAC_CARD = 'templates/profile/caracteristiques-card.tpl';
    public const TPL_PROFILE_SKILL_CARD = 'templates/profile/competences-card.tpl';

    public const TPL_COURSE_ACCORDION   = 'templates/course/accordion.tpl';
    public const TPL_COURSE_FORM        = 'templates/course/form.tpl';
    public const TPL_SKILL_FORM         = 'templates/skill/form.tpl';
    public const TPL_ADMIN_BASE         = 'templates/admin/base.tpl';
    public const TPL_ADMIN_EDIT         = 'templates/admin/edit.tpl';
    public const TPL_ADMIN_LIST         = 'templates/admin/list.tpl';
    public const TPL_ADMIN_BDD          = 'templates/admin/bdd.tpl';

    public const TPL_ADMIN_CONTENT_WRAP = 'templates/section/admin-content-wrapper.tpl';
    public const TPL_CONTENT_HEADER     = 'templates/section/content-header.tpl';
    public const TPL_DASHBOARD_PANEL    = 'templates/section/dashboard.tpl';
    public const TPL_SECTION_ERROR      = 'templates/section/section-error.tpl';
    
    public const TPL_BASE               = 'templates/base.tpl';
    public const TPL_FOOTER             = 'templates/footer.tpl';
    public const TPL_HEADER             = 'templates/header.tpl';
    public const TPL_LOCAL_CSS          = 'templates/localCss.tpl';
    public const TPL_LOCAL_JS           = 'templates/localJs.tpl';
    public const TPL_WWW_CSS            = 'templates/wwwCss.tpl';
    public const TPL_WWW_JS             = 'templates/wwwJs.tpl';

}
