<?php
namespace src\Controller;

use src\Collection\MailCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\Mail;
use src\Repository\MailRepository;
use src\Utils\HtmlUtils;
use src\Utils\TableUtils;

class MailController extends UtilitiesController
{
    private Mail $mail;

    public function __construct(Mail $mail=null)
    {
        $this->mail = $mail ?? new Mail();
    }

    public function getContentPage($arrParams): string
    {
        return '';
    }

}
