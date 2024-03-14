<?php
namespace src\Controller;

use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Entity\MailPlayer;
use src\Utils\TableUtils;

class MailPlayerController extends UtilitiesController
{
    private MailPlayer $mailPlayer;

    public function __construct(MailPlayer $mailPlayer=null)
    {
        $this->mailPlayer = $mailPlayer ?? new MailPlayer();
    }

    public function addBodyRow(TableUtils &$table, array $arrParams=[]): void
    {
		$blnCops = $this->mailPlayer->getField(FieldConstant::PLAYERID)!=0;
        $table->addBodyRow(['attributes'=>$arrParams])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>''])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$this->mailPlayer->getField(FieldConstant::MAIL)])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$this->mailPlayer->getField(FieldConstant::USER)])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$blnCops ? 'Oui' : 'Non']);
    }

    public function getContentPage(): string
    {
        return '';
    }

}
