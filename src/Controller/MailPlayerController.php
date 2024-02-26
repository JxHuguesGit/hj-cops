<?php
namespace src\Controller;

use src\Collection\MailPlayerCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\MailPlayer;
use src\Repository\MailPlayerRepository;
use src\Utils\HtmlUtils;
use src\Utils\TableUtils;

class MailPlayerController extends UtilitiesController
{
    private MailPlayer $mailPlayer;

    public function __construct(MailPlayer $mailPlayer=null)
    {
        $this->mailPlayer = $mailPlayer ?? new MailPlayer();
    }

    public function getContentPage($arrParams): string
    {
        return '';
    }

}
