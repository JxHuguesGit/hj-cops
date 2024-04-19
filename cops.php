<?php
use src\Controller\UtilitiesController;

define('PLUGIN_PATH', plugin_dir_path(__FILE__));
define('PLUGIN_PACKAGE', 'Copsite');
session_start([]);

/**
 * Plugin Name: HJ - COPS
 * Description: COPS
 * @author Hugues
 * @since 1.00.01.01
 */
class Copsite
{
    public function __construct()
    {
        add_filter('template_include', array($this,'templateLoader'));
    }

    public function templateLoader()
    {
        wp_enqueue_script('jquery');
        return PLUGIN_PATH.'templates/base.php';
    }
}
$objCopsite = new Copsite();

function exceptionHandler($objException)
{
    $strHandler  = '<div class="card border-danger" style="max-width: 100%;margin-right: 15px;">';
    $strHandler .= '  <div class="card-header bg-danger text-white"><strong>';
    $strHandler .= $objException->getMessage().'</strong></div>';
    $strHandler .= '  <div class="card-body text-danger">';
    $strHandler .= '    <p>Une erreur est survenue dans le fichier <strong>'.$objException->getFile();
    $strHandler .= '</strong> à la ligne <strong>'.$objException->getLine().'</strong>.</p>';
    $strHandler .= '    <ul class="list-group">';

    $arrTraces = $objException->getTrace();
    foreach ($arrTraces as $trace) {
        $strHandler .= '<li class="list-group-item">Fichier <strong>'.$trace['file'];
        $strHandler .= '</strong> ligne <em>'.$trace['line'].'</em> :<br>';
        if (isset($trace['args'])) {
            if (is_array($trace['args'])) {
                $strHandler .= $trace['function'].'()</li>';
            } else {
                $strHandler .= $trace['class'].$trace['type'].$trace['function'];
                $strHandler .= '('.implode(', ', $trace['args']).')</li>';
            }
        }
    }

    $strHandler .= '    </ul>';
    $strHandler .= '  </div>';
    $strHandler .= '  <div class="card-footer"></div>';
    $strHandler .= '</div>';

    echo $strHandler;
}
set_exception_handler('exceptionHandler');

spl_autoload_register(PLUGIN_PACKAGE.'Autoloader');
function copsiteAutoloader(string $classname)
{
    $pattern = "/(Collection|Constant|Controller|Entity|Enum|Exception|Form|Repository|Utils)/";
    preg_match($pattern, $classname, $matches);
    if (isset($matches[1])) {
        include_once PLUGIN_PATH.str_replace('\\', '/', $classname).'.php';
    }
}

function copsiteMenu()
{
    $urlRoot = 'hj-cops/admin_manage.php';
    if (function_exists('add_menu_page')) {
        $uploadFiles = 'upload_files';
        $pluginName = 'Copsite';
        $urlIcon = plugins_url('/hj-cops/assets/images/favicon-24x24.svg');
        add_menu_page($pluginName, $pluginName, $uploadFiles, $urlRoot, '', $urlIcon);
        if (function_exists('add_submenu_page')) {
            $arrUrlSubMenu = array(
                'home'     => 'Accueil',
                'admin'    => 'Admin',
                'player'   => 'COPS',
                'mailData' => 'Mails',
            );
            foreach ($arrUrlSubMenu as $key => $value) {
                $urlSubMenu = $urlRoot.'&amp;onglet='.$key;
                add_submenu_page($urlRoot, $value, $value, $uploadFiles, $urlSubMenu, $key);
            }
        }
    }
}
add_action('admin_menu', 'copsiteMenu');

function dealWithAjaxCallback()
{
    echo AjaxActions::dealWithAjax();
    die();
}
add_action('wp_ajax_dealWithAjax', 'dealWithAjaxCallback');
add_action('wp_ajax_nopriv_dealWithAjax', 'dealWithAjaxCallback');
