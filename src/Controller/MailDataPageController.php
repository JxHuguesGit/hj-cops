<?php
namespace src\Controller;

use src\Collection\MailDataCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Constant\IconConstant;
use src\Constant\TemplateConstant;
use src\Repository\MailDataRepository;
use src\Utils\HtmlUtils;
use src\Utils\TableUtils;
use src\Utils\UrlUtils;

class MailDataPageController extends PageController
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
        $repository = new MailDataRepository(new MailDataCollection());
        $mailDatas = $repository->findAll([], [FieldConstant::ID=>ConstantConstant::CST_ASC]);
        // Initialisation de la table
        $table = new TableUtils();
        $table->setTable([ConstantConstant::CST_CLASS=>'table-sm table-striped']);
        // Pagination
        $table->setPaginate([
            ConstantConstant::PAGE_OBJS => $mailDatas,
            ConstantConstant::CST_CURPAGE => $this->arrParams[ConstantConstant::CST_CURPAGE] ?? 1,
            ConstantConstant::CST_URL => UrlUtils::getAdminUrl([ConstantConstant::CST_ONGLET=>'mailData']),
        ]);
        // Définition du header de la table
        $table->addHeaderRow()
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'#'])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'MailId'])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'Destinataire'])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'Statut'])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'Expéditeur'])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'Dossier'])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'Lu']);

        $table->addBodyRows($mailDatas, 7);
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
				'MailData',
				parent::setBreadCrumbsContent()
					. ' ' . HtmlUtils::getIcon(IconConstant::I_CARETRIGHT)
					. ' ' . HtmlUtils::getButton(
						'MailData',
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
