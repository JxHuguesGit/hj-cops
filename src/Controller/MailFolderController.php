<?php
namespace src\Controller;

use src\Collection\MailFolderCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\MailFolder;
use src\Repository\MailFolderRepository;
use src\Utils\HtmlUtils;
use src\Utils\TableUtils;

class MailFolderController extends UtilitiesController
{
    private MailFolder $mailFolder;

    public function __construct(MailFolder $mailFolder=null)
    {
        $this->mailFolder = $mailFolder ?? new MailFolder();
    }

    public function getContentPage(): string
    {
        return '';
    }

}
