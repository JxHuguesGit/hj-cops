<?php
namespace src\Controller;

use src\Constant\ConstantConstant;
use src\Constant\IconConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\FichierDump;
use src\Utils\FichierUtils;
use src\Utils\HtmlUtils;
use src\Utils\RepertoireUtils;
use src\Utils\SessionUtils;
use src\Utils\TableUtils;
use src\Utils\UrlUtils;

class AdminBddPageController extends AdminPageController
{
    public function getAdminContentPage(): string
    {
        //////////////////////////////////////////
        // BreadCrumbs
        $this->contentHeader = $this->setBreadCrumbsContent();
        //////////////////////////////////////////

        //////////////////////////////////////////
        // Onglets de navigation
        $this->arrTabs = [
            ConstantConstant::CST_BDD  => [ConstantConstant::CST_LABEL=>LabelConstant::LBL_DATABASE],
            ConstantConstant::CST_CODE => [ConstantConstant::CST_LABEL=>LabelConstant::LBL_CODE_OPTIMISATION],
        ];
        $this->defaultTab = ConstantConstant::CST_BDD;
        
        $this->tabsBar = $this->getTabsBar();
        //////////////////////////////////////////

        $attributes = [
            $this->contentHeader,
            $this->tabsBar,
            $this->getContentPage(),
        ];
        return $this->getRender(TemplateConstant::TPL_ADMIN_CONTENT_WRAP, $attributes);
    }

    public function getContentPage(): string
    {
        $strChemin = TemplateConstant::SQL_PATH;

        $action = $this->arrParams[ConstantConstant::CST_ACTION] ?? '';
        if ($action!='') {
            // $action peut valoir 01, 10 ou 11 pour le moment
            // mais avec les actions unitaires on pourra avoir restore ou delete
            // Dans ce cas, on ne passe pas par doDump, mais doRestore ou doDelete
            if ($action=='restore') {
                $fileName = SessionUtils::fromGet(ConstantConstant::CST_FILE);
                $objFile = new FichierDump($fileName);
                $objFile->doRestore();
            } elseif ($action=='delete') {
                $fileName = SessionUtils::fromGet(ConstantConstant::CST_FILE);
                $objFile = new FichierUtils(TemplateConstant::SQL_PATH, $fileName);
                $objFile->doDelete();
            } else {
                $objFichier = new FichierDump($action);
                $objFichier->doDump();
            }
        }
        $url = UrlUtils::getAdminUrl([
            ConstantConstant::CST_ONGLET => ConstantConstant::CST_BDD,
            ConstantConstant::CST_ACTION => '',
        ]);

        // On récupère la liste des fichiers du répertoire qui correspondent à un pattern spécifique
        // Ici, on n'a pas de pattern spécifique.
        // Ce serait bien de purger les fichiers les plus vieux
        $repertoire = new RepertoireUtils($strChemin);
        $repertoire->recupererFichiers();

        $table = new TableUtils();
        $table->setTable([ConstantConstant::CST_CLASS=>'table-sm table-striped']);

        $table->addHeaderRow()
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'#'])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>LabelConstant::LBL_NAME])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'&nbsp;']);

        $objs = $repertoire->getFiles();
        while ($objs->valid()) {
            $obj = $objs->current();

            $fileUrl = UrlUtils::getAdminUrl([
                ConstantConstant::CST_ONGLET => ConstantConstant::CST_BDD,
                ConstantConstant::CST_FILE   => $obj,
                ConstantConstant::CST_ACTION => 'restore',
            ]);
            $aIcon = HtmlUtils::getIcon(IconConstant::I_PLAY);
            $link  = HtmlUtils::getLink($aIcon, $fileUrl, 'text-white');
            $actions = HtmlUtils::getButton($link, [ConstantConstant::CST_CLASS=>'bg-secondary']).'&nbsp;';

            $fileUrl = UrlUtils::getAdminUrl([
                ConstantConstant::CST_ONGLET => ConstantConstant::CST_BDD,
                ConstantConstant::CST_FILE   => $obj,
                ConstantConstant::CST_ACTION => 'delete',
            ]);
            $aIcon = HtmlUtils::getIcon(IconConstant::I_TRASHALT);
            $link  = HtmlUtils::getLink($aIcon, $fileUrl, 'text-white');
            $actions .= HtmlUtils::getButton($link, [ConstantConstant::CST_CLASS=>'bg-secondary']);

            $table->addBodyRow()
                ->addBodyCell([ConstantConstant::CST_CONTENT=>''])
                ->addBodyCell([ConstantConstant::CST_CONTENT=>$obj])
                ->addBodyCell([ConstantConstant::CST_CONTENT=>$actions]);

            $objs->next();
        }

        $attributes = [
            $url,
            $table->display(),
        ];
        return $this->getRender(TemplateConstant::TPL_ADMIN_BDD, $attributes);
    }

    public function setBreadCrumbsContent(): string
    {
        return $this->getRender(
            TemplateConstant::TPL_CONTENT_HEADER,
            [
                'Base de données',
                parent::setBreadCrumbsContent()
                    . ' ' . HtmlUtils::getIcon(IconConstant::I_CARETRIGHT)
                    . ' ' . HtmlUtils::getButton(
                        'Base de données',
                        [ConstantConstant::CST_CLASS=>'btn-secondary disabled']
                    )
            ]
        );
    }
}
