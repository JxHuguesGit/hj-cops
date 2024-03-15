<?php
namespace src\Controller;

use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Entity\MailData;
use src\Utils\HtmlUtils;
use src\Utils\TableUtils;

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
        $checkId = 'check'.$id;
        $blnRead = $this->mailData->getField(FieldConstant::READ)==1;
        $strTrContent = '';

        // Champ Id avec case à cocher
        $strInput = HtmlUtils::getInput([
            'id'=>$checkId,
            ConstantConstant::CST_NAME=>$checkId,
            ConstantConstant::CST_VALUE=>$id,
            ConstantConstant::CST_TYPE=>'checkbox'
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
            $this->mailData->getSinceWhen(),
            [ConstantConstant::CST_CLASS=>'mailbox-date']
        );

        return HtmlUtils::getBalise('tr', $strTrContent);
    }

}
