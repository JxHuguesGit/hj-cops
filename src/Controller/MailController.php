<?php
namespace src\Controller;

use src\Collection\MailCollection;
use src\Collection\MailDataCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\Mail;
use src\Repository\MailRepository;
use src\Repository\MailDataRepository;
use src\Utils\HtmlUtils;
use src\Utils\TableUtils;
use src\Utils\UrlUtils;

class MailController extends UtilitiesController
{
    private $menu = [];
    private $slug = '';

    public function __construct(array $arrUri=[], string $slug='')
    {
        parent::__construct($arrUri);
        if ($slug!='') {
            $this->slug = $slug;
            $this->menu = [
                ConstantConstant::CST_NOTIFICATION => [
                    ConstantConstant::CST_LABEL => LabelConstant::LBL_NOTIFICATIONS,
                    ConstantConstant::CST_SLUG  => ConstantConstant::CST_ALERT,
                    ConstantConstant::CST_ICON  => 'bell',
                ],
                ConstantConstant::CST_TRASH => [
                    ConstantConstant::CST_LABEL => LabelConstant::LBL_TRASH,
                    ConstantConstant::CST_SLUG  => ConstantConstant::CST_TRASH,
                    ConstantConstant::CST_ICON  => 'trash-alt',
                ],
            ];
            $this->title = $this->menu[$slug][ConstantConstant::CST_LABEL];
        }
    }

    public function getAdminContentPage(): string
    {
        switch ($this->arrParams['onglet']) {
            case 'mail' :
                $controller = null;
            break;
            case 'mailPlayer' :
                $controller = new MailPlayerController();
            break;
            case 'mailFolder' :
                $controller = new MailFolderController();
            break;
            default :
                $controller = new MailDataController();
            break;
        }
        if ($controller!=null) {
            $controller->setParams($this->arrParams);
            return $controller->getAdminContentPage();
        }

        $this->setBreadCrumbsContent();
        $attributes = [
            'Mail',
            $this->breadCrumbsContent,
        ];
        $contentHeader = $this->getRender(TemplateConstant::TPL_CONTENT_HEADER, $attributes);

        $this->arrTabs = [
            'mailData'   => ['label'=>'MailData'],
            'mail'       => ['label'=>'Mail'],
            'mailPlayer' => ['label'=>'MailPlayer'],
            'mailFolder' => ['label'=>'MailFolder'],
        ];
        $this->defaultTab = 'mailData';
        $tabsBar = $this->getTabsBar();

        return $contentHeader.$tabsBar;
    }

    public function getTabsBar(): string
    {
        /////////////////////////////////////////
        // Construction des onglets
        $strLis = '';
        foreach ($this->arrTabs as $slug => $arrData) {
            $urlAttributes = ['onglet'=>$slug];
            $strIcon = '';

            if (!empty($arrData['icon'])) {
                $strIcon = HtmlUtils::getIcon('icon').' ';
            }
            $blnActive = $this->getArrParams('onglet')==$slug;
            $strLink = HtmlUtils::getLink(
                $strIcon.$arrData['label'],
                UrlUtils::getAdminUrl($urlAttributes),
                'nav-link text-white'
            );

            $strLis .= HtmlUtils::getBalise(
                'li',
                $strLink,
                ['class'=>'nav-item'.($blnActive ? ' bg-secondary' : ' '.'bg-dark')]
            );
        }
        $attributes = ['class'=>implode(' ', ['nav', 'nav-pills', 'nav-fill'])];
        /////////////////////////////////////////

        return HtmlUtils::getBalise('ul', $strLis, $attributes);
    }


    public function setBreadCrumbsContent(): void
    {
        parent::setBreadCrumbsContent();

        //if ($this->slugOnglet==self::ONGLET_DESK || $this->slugOnglet=='') {
        //    $buttonAttributes = [self::ATTR_CLASS=>' '.self::BTS_BTN_DARK_DISABLED];
        //} else {
            $buttonAttributes = ['class'=>'btn-secondary disabled'];
        //}
        $this->breadCrumbsContent .= ' <i class="fa-solid fa-caret-right"></i> ';
        $this->breadCrumbsContent .= HtmlUtils::getButton('Mail', $buttonAttributes);

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
                $this->arrParams[ConstantConstant::CST_ACTION]=='trash'
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
            return $this->getRender(TemplateConstant::TPL_MAIL_PANEL, $attributes);
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
            return $this->getRender(TemplateConstant::TPL_MAIL_PANEL, $attributes);
        }
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
            $strLink = HtmlUtils::getLink($aContent, "/".$slug, 'nav-link btn-outline-gray text-gray-dark');
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
        $strIcon = HtmlUtils::getIcon('square', 'regular');
        $btnAttributes = ['class'=>'ajaxAction', 'data-trigger'=>'click', 'data-ajax'=>'selectAll'];
        $strButtonList = HtmlUtils::getButton($strIcon, $btnAttributes).' ';

        // trash
        $strIcon = HtmlUtils::getIcon('trash-alt');
        $btnAttributes['data-ajax'] = 'trash';
        $strButtonList .= HtmlUtils::getButton($strIcon, $btnAttributes).' ';

        // refresh
        $strIcon = HtmlUtils::getIcon('rotate');
        $btnAttributes['data-ajax'] = 'refresh';
        $strButtonList .= HtmlUtils::getButton($strIcon, $btnAttributes);

        return $strButtonList;
    }

}
