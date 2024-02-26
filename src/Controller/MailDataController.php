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

class MailDataController extends UtilitiesController
{
    private MailData $mailData;

    public function __construct(MailData $mailData=null)
    {
        $this->mailData = $mailData ?? new MailData();
    }

    public function getContentPage($arrParams): string
    {
        return '';
    }

}
