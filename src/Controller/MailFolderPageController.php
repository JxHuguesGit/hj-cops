<?php
namespace src\Controller;

use src\Collection\MailFolderCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Constant\IconConstant;
use src\Constant\TemplateConstant;
use src\Repository\MailFolderRepository;
use src\Utils\HtmlUtils;
use src\Utils\TableUtils;
use src\Utils\UrlUtils;

class MailFolderPageController extends PageController
{
    public function getAdminContentPage(): string
    {
        //////////////////////////////////////////
        // BreadCrumbs
        $contentHeader = $this->setBreadCrumbsContent();
        //////////////////////////////////////////

        //////////////////////////////////////////
        // Onglets de navigation
        $this->arrTabs = [
            'mailData'   => [ConstantConstant::CST_LABEL=>'MailData'],
            'mail'       => [ConstantConstant::CST_LABEL=>'Mail'],
            'mailPlayer' => [ConstantConstant::CST_LABEL=>'MailPlayer'],
            'mailFolder' => [ConstantConstant::CST_LABEL=>'MailFolder'],
        ];
        $this->defaultTab = 'mailData';
        $tabsBar = $this->getTabsBar();
        //////////////////////////////////////////

        //////////////////////////////////////////
        // Contenu du corps
        $repository = new MailFolderRepository(new MailFolderCollection());
        $mailFolders = $repository->findAll([], [FieldConstant::ID=>ConstantConstant::CST_ASC]);
        // Initialisation de la table
        $table = new TableUtils();
        $table->setTable([ConstantConstant::CST_CLASS=>'table-sm table-striped']);
        // Pagination
        $table->setPaginate([
            ConstantConstant::PAGE_OBJS => $mailFolders,
            ConstantConstant::CST_CURPAGE => $this->arrParams[ConstantConstant::CST_CURPAGE] ?? 1,
            ConstantConstant::CST_URL => UrlUtils::getAdminUrl([ConstantConstant::CST_ONGLET=>'mailFolder']),
        ]);
        // Définition du header de la table
        $table->addHeaderRow()
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'#'])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'Slug'])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'Dossier'])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'Icône']);

        $table->addBodyRows($mailFolders, 4);
        //////////////////////////////////////////

        $attributes = [
            $contentHeader,
            $tabsBar,
            $table->display(),
        ];
        return $this->getRender(TemplateConstant::TPL_ADMIN_CONTENT_WRAP, $attributes);
    }

    public function setBreadCrumbsContent(): string
    {
        return $this->getRender(
            TemplateConstant::TPL_CONTENT_HEADER,
            [
                'MailFolder',
                parent::setBreadCrumbsContent()
                    . ' ' . HtmlUtils::getIcon(IconConstant::I_CARETRIGHT)
                    . ' ' . HtmlUtils::getButton(
                        'MailFolder',
                        [ConstantConstant::CST_CLASS=>'btn-secondary disabled']
                    )
            ]
        );
    }

    public function getContentPage(): string
    {
        return '';
    }

}
