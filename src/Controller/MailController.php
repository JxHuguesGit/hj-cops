<?php
namespace src\Controller;

use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Entity\Mail;
use src\Utils\TableUtils;

class MailController extends UtilitiesController
{
    private Mail $mail;

    public function __construct(Mail $mail=null)
    {
        $this->mail = $mail ?? new Mail();
    }

    public function addBodyRow(TableUtils &$table, array $arrParams=[]): void
    {
        $table->addBodyRow(['attributes'=>$arrParams])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>''])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$this->mail->getField(FieldConstant::SUBJECT)])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$this->mail->getExcerpt()])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$this->mail->getField(FieldConstant::SENTDATE)])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>''])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>'']);
    }
}
