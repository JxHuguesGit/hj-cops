<?php
namespace src\Controller;

use src\Collection\MailCollection;
use src\Collection\MailDataCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Constant\IconConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Repository\MailRepository;
use src\Repository\MailDataRepository;
use src\Utils\HtmlUtils;
use src\Utils\TableUtils;
use src\Utils\UrlUtils;

class MailPageController extends PageController
{
    public function __construct(array $arrUri=[], string $slug='')
    {
        parent::__construct($arrUri, $slug);
        if ($slug!='') {
            $this->menu = [
                ConstantConstant::CST_NOTIFICATION => [
                    ConstantConstant::CST_LABEL => LabelConstant::LBL_NOTIFICATIONS,
                    ConstantConstant::CST_SLUG  => ConstantConstant::CST_ALERT,
                    ConstantConstant::CST_ICON  => IconConstant::I_BELL,
                ],
                ConstantConstant::CST_TRASH => [
                    ConstantConstant::CST_LABEL => LabelConstant::LBL_TRASH,
                    ConstantConstant::CST_SLUG  => ConstantConstant::CST_TRASH,
                    ConstantConstant::CST_ICON  => IconConstant::I_TRASHALT,
                ],
            ];
            $this->title = $this->menu[$slug][ConstantConstant::CST_LABEL];
        }
    }

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
        $repository = new MailRepository(new MailCollection());
        $mails = $repository->findAll([], [FieldConstant::ID=>ConstantConstant::CST_ASC]);
        // Initialisation de la table
        $table = new TableUtils();
        $table->setTable([ConstantConstant::CST_CLASS=>'table-sm table-striped']);
        // Pagination
        $table->setPaginate([
            ConstantConstant::PAGE_OBJS => $mails,
            ConstantConstant::CST_CURPAGE => $this->arrParams[ConstantConstant::CST_CURPAGE] ?? 1,
            ConstantConstant::CST_URL => UrlUtils::getAdminUrl([ConstantConstant::CST_ONGLET=>'mail']),
        ]);
        // Définition du header de la table
        $table->addHeaderRow()
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'#'])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'Sujet'])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'Extrait'])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'Date d\'envoi'])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'Nombre'])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>ConstantConstant::CST_NBSP]);

        $table->addBodyRows($mails, 6);
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
				'Mail',
				parent::setBreadCrumbsContent()
					. ' ' . HtmlUtils::getIcon(IconConstant::I_CARETRIGHT)
					. ' ' . HtmlUtils::getButton(
						'Mail',
						[ConstantConstant::CST_CLASS=>'btn-secondary disabled']
					)
			]
		);
    }





















    public function getContentPage(): string
    {
        $blnRead = false;
        // Si action = read et que l'id est un mailData lisible par le toId
        if (isset($this->arrParams[ConstantConstant::CST_ACTION])) {
            if (
                $this->arrParams[ConstantConstant::CST_ACTION]==ConstantConstant::CST_READ
                && isset($this->arrParams[ConstantConstant::CST_ID])
            ) {
                $repository = new MailDataRepository(new MailDataCollection());
                $mailData = $repository->find($this->arrParams[ConstantConstant::CST_ID]);
                $mailUser = $this->player->getMailPlayer();
                $blnRead = $mailData->getField(FieldConstant::TOID)==$mailUser->getField(FieldConstant::ID);

                if ($blnRead) {
                    $mailData->setField(FieldConstant::READ, 1);
                    $mailData->update();
                }
            } elseif (
                $this->arrParams[ConstantConstant::CST_ACTION]==ConstantConstant::CST_TRASH
                && isset($this->arrParams['ids'])
            ) {
                $repository = new MailDataRepository(new MailDataCollection());
                $mailUser = $this->player->getMailPlayer();
                $ids = explode(',', $this->arrParams['ids']);
                foreach ($ids as $id) {
                    $mailData = $repository->find($id);
                    if ($mailData->getField(FieldConstant::TOID)==$mailUser->getField(FieldConstant::ID)) {
                        // TODO, si le message est déjà dans Trash, il doit être supprimé définitivement.
                        // Pour l'heure, on se content de le déplacer dans Trash
                        $mailData->setField(FieldConstant::FOLDERID, 6);
                        $mailData->update();
                    }
                }
            }
        }

        $strFoldersListContent     = $this->getFoldersListContent();
        if ($blnRead) {
            // alors on affiche la lecture du message
            $attributes = [
                $strFoldersListContent,
                $mailData->getController()->getMailContent(),
            ];
        } else {
            // Sinon la liste des messages du dossier courant
            $attributes = [
                $this->title,
                $this->getMailboxControlsContent(),
                $this->getMailTableContent(),
            ];
            $attributes = [
                $strFoldersListContent,
                $this->getRender(TemplateConstant::TPL_MAIL_LIST, $attributes),
            ];
        }
        return $this->getRender(TemplateConstant::TPL_MAIL_PANEL, $attributes);
    }

    private function getMailTableContent(): string
    {
        $searchAttributes = [
            FieldConstant::TOID => $this->player->getField(FieldConstant::ID),
            FieldConstant::FOLDERID => $this->menu[$this->slug][ConstantConstant::CST_SLUG]
        ];
        $mailNotifs = $this->player->getMailData($searchAttributes);

        $strRows = '';
        while ($mailNotifs->valid()) {
            $mailData = $mailNotifs->current();
            $strRows .= $mailData->getController()->getRow();
            $mailNotifs->next();
        }
        return $strRows;
    }

    private function getFoldersListContent(): string
    {
        $strFoldersList = '';
        foreach ($this->menu as $slug => $content) {
            $strIcon = HtmlUtils::getIcon($this->menu[$slug][ConstantConstant::CST_ICON]);
            $searchAttributes = [
                FieldConstant::TOID=>$this->player->getField(FieldConstant::ID),
                FieldConstant::READ=>0,
                FieldConstant::FOLDERID=>$this->menu[$slug][ConstantConstant::CST_SLUG]
            ];
            $mailNotifs = $this->player->getMailData($searchAttributes);
            $strBadge = '';
            if ($mailNotifs->valid()) {
                $strBadge .= HtmlUtils::getSpan(
                    $mailNotifs->length(),
                    [ConstantConstant::CST_CLASS=>'badge bg-teal float-end']
                );
            }
            $aContent = $strIcon.' '.$this->menu[$slug][ConstantConstant::CST_LABEL].' '.$strBadge;
            $strLink = HtmlUtils::getLink(
                $aContent,
                UrlUtils::getPublicUrl($slug),
                'nav-link btn-outline-gray text-gray-dark'
            );
            $strLi = HtmlUtils::getLi(
                $strLink,
                [ConstantConstant::CST_CLASS=>'nav-item '.($slug==$this->slug?'border-left-lightblue':'')]
            );
            $strFoldersList .= $strLi;
        }
        return $strFoldersList;
    }

    private function getMailboxControlsContent(): string
    {
        // Nouvelle version.
        // Pour l'heure, on ne met que les boutons selectAll, Trash et Refresh.

        // selectAll
        $strIcon = HtmlUtils::getIcon(IconConstant::I_SQUARE, IconConstant::REGULAR);
        $btnAttributes = [
			ConstantConstant::CST_CLASS=>'ajaxAction',
			'data-trigger'=>'click',
			'data-ajax'=>'selectAll'
		];
        $strButtonList = HtmlUtils::getButton($strIcon, $btnAttributes).' ';

        // trash
        $strIcon = HtmlUtils::getIcon(IconConstant::I_TRASHALT);
        $btnAttributes['data-ajax'] = ConstantConstant::CST_TRASH;
        $strButtonList .= HtmlUtils::getButton($strIcon, $btnAttributes).' ';

        // refresh
        $strIcon = HtmlUtils::getIcon(IconConstant::I_ROTATE);
        $btnAttributes['data-ajax'] = 'refresh';
        $strButtonList .= HtmlUtils::getButton($strIcon, $btnAttributes);

        return $strButtonList;
    }

}
