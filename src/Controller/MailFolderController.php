<?php
namespace src\Controller;

use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Entity\MailFolder;
use src\Utils\HtmlUtils;
use src\Utils\TableUtils;

class MailFolderController extends UtilitiesController
{
    private MailFolder $mailFolder;

    public function __construct(MailFolder $mailFolder=null)
    {
        $this->mailFolder = $mailFolder ?? new MailFolder();
    }

    public function addBodyRow(TableUtils &$table, array $arrParams=[]): void
    {
        $strIcon = HtmlUtils::getIcon($this->mailFolder->getField(FieldConstant::ICON));
        $table->addBodyRow(['attributes'=>$arrParams])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>''])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$this->mailFolder->getField(FieldConstant::SLUG)])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$this->mailFolder->getField(FieldConstant::LABEL)])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$strIcon]);
    }

    public function getContentPage(): string
    {
        return '';
    }

}
