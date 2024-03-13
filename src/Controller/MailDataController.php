<?php
namespace src\Controller;

use src\Collection\MailDataCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\MailData;
use src\Repository\MailDataRepository;
use src\Utils\HtmlUtils;
use src\Utils\TableUtils;
use src\Utils\UrlUtils;

class MailDataController extends UtilitiesController
{
    private MailData $mailData;

    public function __construct(MailData $mailData=null)
    {
        $this->mailData = $mailData ?? new MailData();
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
        $this->breadCrumbsContent .= HtmlUtils::getButton('MailData', $buttonAttributes);

    }
    public function getAdminContentPage(): string
    {
        //////////////////////////////////////////
        // BreadCrumbs
        $this->setBreadCrumbsContent();
        $attributes = [
            'MailData',
            $this->breadCrumbsContent,
        ];
        $contentHeader = $this->getRender(TemplateConstant::TPL_CONTENT_HEADER, $attributes);
        //////////////////////////////////////////

        //////////////////////////////////////////
        // Onglets de navigation
        $this->arrTabs = [
            'mailData'   => ['label'=>'MailData'],
            'mail'       => ['label'=>'Mail'],
            'mailPlayer' => ['label'=>'MailPlayer'],
            'mailFolder' => ['label'=>'MailFolder'],
        ];
        $this->defaultTab = 'mailData';
        $tabsBar = $this->getTabsBar();
        //////////////////////////////////////////

        //////////////////////////////////////////
        // Contenu du corps
        $repository = new MailDataRepository(new MailDataCollection());
        $mailDatas = $repository->findByAndOrdered([], ['sentDate'=>'desc']);
        // Initialisation de la table
        $table = new TableUtils();
        $table->setTable([ConstantConstant::CST_CLASS=>'table-sm table-striped']);
        // Pagination
        $table->setPaginate([
            ConstantConstant::PAGE_OBJS => $mailDatas,
            ConstantConstant::CST_CURPAGE => $this->arrParams[ConstantConstant::CST_CURPAGE] ?? 1,
            ConstantConstant::CST_URL => UrlUtils::getAdminUrl(['onglet'=>'mailData']),
        ]);
        // Définition du header de la table
        $table->addHeaderRow()
            ->addHeaderCell(['content'=>'#'])
            ->addHeaderCell(['content'=>'MailId'])
            ->addHeaderCell(['content'=>'Destinataire'])
            ->addHeaderCell(['content'=>'Statut'])
            ->addHeaderCell(['content'=>'Expéditeur'])
            ->addHeaderCell(['content'=>'Dossier'])
            ->addHeaderCell(['content'=>'Lu']);

        $table->addBodyRows($mailDatas, 6);
        //////////////////////////////////////////

        $attributes = [
            $contentHeader,
            $tabsBar,
            $table->display(),
        ];
        return $this->getRender(TemplateConstant::TPL_ADMIN_CONTENT_WRAP, $attributes);
    }

    public function addBodyRow(TableUtils &$table, array $arrParams=[]): void
    {
        
        $table->addBodyRow(['attributes'=>$arrParams])
            ->addBodyCell(['content'=>''])
            ->addBodyCell(['content'=>$this->mailData->getField(FieldConstant::MAILID)])
            ->addBodyCell(['content'=>$this->mailData->getDestinataire()])
            ->addBodyCell(['content'=>$this->mailData->getField(FieldConstant::STATUS)])
            ->addBodyCell(['content'=>$this->mailData->getAuteur()])
            ->addBodyCell(['content'=>$this->mailData->getMailFolder()->getField(FieldConstant::LABEL)])
            ->addBodyCell(['content'=>$this->mailData->getField(FieldConstant::READ)==1 ? 'Oui' : 'Non']);
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

    public function getContentPage(): string
    {
        return '';
    }

    public function getMailContent(): string
    {
        $strIcon = HtmlUtils::getIcon('trash-alt');
        $btnAttributes = ['class'=>'ajaxAction', 'data-trigger'=>'click', 'data-ajax'=>'trash'];
        $strTopButtons = HtmlUtils::getButton($strIcon, $btnAttributes);

        $strIcon = HtmlUtils::getIcon('trash-alt');
        $btnAttributes = ['class'=>'ajaxAction', 'data-trigger'=>'click', 'data-ajax'=>'trash'];
        $strBottomButtons = HtmlUtils::getButton($strIcon.' Supprimer', $btnAttributes);

        
/*
        $strButton = '            <div class="btn-group">
        <button type="button" class="btn btn-default btn-sm" data-container="body" title="Delete">
        <i class="fa-regular fa-trash-alt"></i></button>
        <button type="button" class="btn btn-default btn-sm" data-container="body" title="Reply">
        <i class="fa-solid fa-reply"></i></button>
        <button type="button" class="btn btn-default btn-sm" data-container="body" title="Forward">
        <i class="fa-solid fa-share"></i></button>
    </div>
    <button type="button" class="btn btn-default btn-sm" title="Print"><i class="fa-solid fa-print">
    </i></button>
';
        $strButtonBottom = '        <div class="float-end">
        <button type="button" class="btn btn-default"><i class="fa-solid fa-reply"></i> Reply</button>
        <button type="button" class="btn btn-default"><i class="fa-solid fa-share"></i> Forward</button>
    </div>
    <button type="button" class="btn btn-default"><i class="fa-regular fa-trash-alt"></i> Delete</button>
    <button type="button" class="btn btn-default"><i class="fa-solid fa-print"></i> Print</button>
';
*/
        $attributes = [
            'Read Mail',
            '#urlPrevious',
            'disabled or not',
            '#urlNext',
            'disabled or not',
            $this->mailData->getMail()->getField(FieldConstant::SUBJECT),
            $this->mailData->getAuteur(),
            '15 Feb. 2015 11:03 PM',
            $strTopButtons,
            $this->mailData->getMail()->getContent(),
            $strBottomButtons,
        ];
        return $this->getRender(TemplateConstant::TPL_MAIL_VIEW, $attributes);
}

    public function getRow(): string
    {
        $id = $this->mailData->getField(FieldConstant::ID);
        $read = $this->mailData->getField(FieldConstant::READ);
        $strTrContent = '';

        // Champ Id avec case à cocher
        $strInput = HtmlUtils::getInput(['id'=>'check'.$id, 'name'=>'check'.$id, 'value'=>$id, 'type'=>'checkbox']);
        $strInput .= HtmlUtils::getBalise('label', '', ['for'=>'check'.$id]);
        $strDiv = HtmlUtils::getDiv($strInput, ['class'=>'icheck-primary']);
        $strTrContent .= HtmlUtils::getBalise('td', $strDiv, $read ? [] : ['class'=>'border-left-lightblue']);

        // Champ "Star"
        //<td class="mailbox-star"><a href="#"><i class="fas fa-star text-warning"></i></a></td>
        $strTrContent .= HtmlUtils::getBalise('td', ConstantConstant::CST_NBSP, ['class'=>'mailbox-star']);

        // Champ Auteur
        $auteur = $this->mailData->getAuteur();
        $href = $this->mailData->getUrl('read');
        $strTrContent .= HtmlUtils::getBalise('td', '<a href="'.$href.'">'.$auteur.'</a>', ['class'=>'mailbox-name']);

        // Champ Subject
        $strTrContent .= HtmlUtils::getBalise('td', $this->mailData->getSubjectExcerpt(), ['class'=>'mailbox-subject']);

        // Champ Pièces jointes
        $strTrContent .= HtmlUtils::getBalise('td', ConstantConstant::CST_NBSP, ['class'=>'mailbox-attachment']);

        // Champ Time
        $strTrContent .= HtmlUtils::getBalise('td', $this->mailData->getSinceWhen(), ['class'=>'mailbox-date']);

        return HtmlUtils::getBalise('tr', $strTrContent);
    }

}
