<?php
namespace src\Controller;

use src\Collection\MailDataCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Constant\IconConstant;
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

    public function addBodyRow(TableUtils &$table, array $arrParams=[]): void
    {
        $label   = $this->mailData->getMailFolder()->getField(FieldConstant::LABEL);
        $blnRead = $this->mailData->getField(FieldConstant::READ)==1;
        $table->addBodyRow(['attributes'=>$arrParams])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>''])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$this->mailData->getField(FieldConstant::MAILID)])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$this->mailData->getDestinataire()])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$this->mailData->getField(FieldConstant::STATUS)])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$this->mailData->getAuteur()])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$label])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$blnRead ? 'Oui' : 'Non']);
    }

    public function getRow(): string
    {
        $id      = $this->mailData->getField(FieldConstant::ID);
        $checkId = IconConstant::I_CHECK.$id;
        $blnRead = $this->mailData->getField(FieldConstant::READ)==1;
        $strTrContent = '';

        // Champ Id avec case à cocher
        $strInput = HtmlUtils::getInput([
            ConstantConstant::CST_ID    => $checkId,
            ConstantConstant::CST_NAME  => $checkId,
            ConstantConstant::CST_VALUE => $id,
            ConstantConstant::CST_TYPE  => 'checkbox'
        ]);
        $strInput .= HtmlUtils::getBalise(ConstantConstant::CST_LABEL, '', [ConstantConstant::CST_FOR=>$checkId]);
        $strDiv = HtmlUtils::getDiv($strInput, [ConstantConstant::CST_CLASS=>'icheck-primary']);
        $strTrContent .= HtmlUtils::getBalise(
            'td',
            $strDiv,
            $blnRead ? [] : [ConstantConstant::CST_CLASS=>'border-left-lightblue']
        );

        // Champ "Star"
        //<td class="mailbox-star"><a href="#"><i class="fas fa-star text-warning"></i></a></td>
        $strTrContent .= HtmlUtils::getBalise(
            'td',
            ConstantConstant::CST_NBSP,
            [ConstantConstant::CST_CLASS=>'mailbox-star']
        );

        // Champ Auteur
        $auteur = $this->mailData->getAuteur();
        $href = $this->mailData->getUrl(ConstantConstant::CST_READ);
        $strTrContent .= HtmlUtils::getBalise(
            'td',
            HtmlUtils::getLink($auteur, $href),
            [ConstantConstant::CST_CLASS=>'mailbox-name']
        );

        // Champ Subject
        $strTrContent .= HtmlUtils::getBalise(
            'td',
            $this->mailData->getSubjectExcerpt(),
            [ConstantConstant::CST_CLASS=>'mailbox-subject']
        );

        // Champ Pièces jointes
        $strTrContent .= HtmlUtils::getBalise(
            'td',
            ConstantConstant::CST_NBSP,
            [ConstantConstant::CST_CLASS=>'mailbox-attachment']
        );

        // Champ Time
        $strTrContent .= HtmlUtils::getBalise(
            'td',
            '',//$this->mailData->getSinceWhen(),
            [ConstantConstant::CST_CLASS=>'mailbox-date']
        );

        return HtmlUtils::getBalise('tr', $strTrContent);
    }
    
    public function getMailContent(): string
    {
        ////////////////////////////////////////////:
        // Url du message précédent. Et si aucun, définir la classe pour le bouton à disabled
        $repository = new MailDataRepository(new MailDataCollection());
        $mailDatas = $repository->findByAndOrdered(
            [
                FieldConstant::TOID     => $this->mailData->getField(FieldConstant::TOID),
                FieldConstant::FOLDERID => $this->mailData->getField(FieldConstant::FOLDERID),
                '-----'    => " AND `sentDate` < '".$this->mailData->getMail()->getField(FieldConstant::SENTDATE)."'"
            ],
            [FieldConstant::SENTDATE => ConstantConstant::CST_DESC],
            1
        );
        if ($mailDatas->valid()) {
            $prevClass = 'bg-dark text-white';
            $prevMailData = $mailDatas->current();
            $prevUrl = $prevMailData->getUrl(ConstantConstant::CST_READ);
        } else {
            $prevClass = ConstantConstant::CST_DISABLED;
            $prevUrl = '#';
        }
        // Url du message suivant. Et si aucun, définir la classe pour le bouton à disabled
        $repository = new MailDataRepository(new MailDataCollection());
        $mailDatas = $repository->findByAndOrdered(
            [
                FieldConstant::TOID     => $this->mailData->getField(FieldConstant::TOID),
                FieldConstant::FOLDERID => $this->mailData->getField(FieldConstant::FOLDERID),
                '-----'    => " AND `sentDate` > '".$this->mailData->getMail()->getField(FieldConstant::SENTDATE)."'"
            ],
            [FieldConstant::SENTDATE => ConstantConstant::CST_ASC],
            1
        );
        if ($mailDatas->valid()) {
            $nextClass = 'bg-dark text-white';
            $nextMailData = $mailDatas->current();
            $nextUrl = $nextMailData->getUrl(ConstantConstant::CST_READ);
        } else {
            $nextClass = ConstantConstant::CST_DISABLED;
            $nextUrl = '#';
        }
        ////////////////////////////////////////////:

        ////////////////////////////////////////////:
        // Boutons à afficher en haut. Pour le moment, seulement Suppression
        $strTopButtons = HtmlUtils::getButton(
            HtmlUtils::getIcon(IconConstant::I_TRASHALT),
            [
                ConstantConstant::CST_CLASS=>'ajaxAction',
                'data-trigger'=>'click',
                'data-ajax'=>ConstantConstant::CST_TRASH
            ]
        );
        ////////////////////////////////////////////:

        ////////////////////////////////////////////:
        // Boutons à afficher en bas. Pour le moment, seulement Suppression
        $strBottomButtons = HtmlUtils::getButton(
            HtmlUtils::getIcon(IconConstant::I_TRASHALT).' Supprimer',
            [
                ConstantConstant::CST_CLASS=>'ajaxAction',
                'data-trigger'=>'click',
                'data-ajax'=>ConstantConstant::CST_TRASH
            ]
        );
        ////////////////////////////////////////////:

/*
        $strButton = '            <div class="btn-group">
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
    <button type="button" class="btn btn-default"><i class="fa-solid fa-print"></i> Print</button>
';
*/
        $attributes = [
            'Lecture',
            $prevUrl,
            $prevClass,
            $nextUrl,
            $nextClass,
            $this->mailData->getMail()->getField(FieldConstant::SUBJECT),
            $this->mailData->getAuteur(),
            $this->mailData->getSinceWhen(),
            $strTopButtons,
            $this->mailData->getMail()->getContent(),
            $strBottomButtons,
        ];
        return $this->getRender(TemplateConstant::TPL_MAIL_VIEW, $attributes);
    }


}
