<?php
namespace src\Entity;

use src\Collection\MailCollection;
use src\Collection\MailDataCollection;
use src\Collection\MailFolderCollection;
use src\Collection\MailPlayerCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Controller\MailDataController;
use src\Entity\Mail;
use src\Repository\MailRepository;
use src\Repository\MailDataRepository;
use src\Repository\MailFolderRepository;
use src\Repository\MailPlayerRepository;
use src\Utils\DateUtils;
use src\Utils\HtmlUtils;

class MailData extends Entity
{
    //////////////////////////////////////////////////
    // ATTRIBUTES
    //////////////////////////////////////////////////
    protected int $id;
    protected int $mailId;
    protected int $toId;
    protected string $status;
    protected int $fromId;
    protected int $folderId;
    protected int $read;

    protected MailDataRepository $repository;
    protected ?Mail $mail = null;

    //////////////////////////////////////////////////
    // CONSTRUCT
    //////////////////////////////////////////////////
    public function __construct(array $attributes=[])
    {
        parent::__construct($attributes);
        $this->initRepositories();
    }

    private function initRepositories()
    {
        $this->repository = new MailDataRepository(new MailDataCollection());
        $this->mailRepository = new MailRepository(new MailCollection());
        $this->mailFolderRepository = new MailFolderRepository(new MailFolderCollection());
        $this->mailPlayerRepository = new MailPlayerRepository(new MailPlayerCollection());
    }

    public static function initFromRow($row): MailData
    {
        $obj = new MailData();
        $fields = $obj->getFields();
        foreach ($fields as $field) {
            $obj->setField($field, $row->{$field});
        }
        return $obj;
    }

    public static function getFields(): array
    {
        return [
            FieldConstant::ID,
            FieldConstant::MAILID,
            FieldConstant::TOID,
            FieldConstant::STATUS,
            FieldConstant::FROMID,
            FieldConstant::FOLDERID,
            FieldConstant::READ,
        ];
    }

    public function getController(): MailDataController
    {
        return new MailDataController($this);
    }

    public function update(): void
    {
        $this->repository->update($this);
    }

    public function getMail(): Mail
    {
        return $this->mailRepository->find($this->mailId);
    }

    public function getMailFolder(): MailFolder
    {
        return $this->mailFolderRepository->find($this->folderId);
    }

    public function getDestinataire(): string
    {
        return $this->mailPlayerRepository->find($this->toId)->getField(FieldConstant::USER);
    }

    public function getAuteur(): string
    {
        return $this->mailPlayerRepository->find($this->fromId)->getField(FieldConstant::USER);
    }

    public function getSubjectExcerpt(): string
    {
        $this->mail = $this->mailRepository->find($this->mailId);
        $excerpt = mb_substr($this->mail->getField(FieldConstant::CONTENT), 0, 20).'...';
        $title = $this->mail->getField(FieldConstant::SUBJECT);
        return HtmlUtils::getBalise('strong', $title).' - '.$excerpt;
    }

    public function getSinceWhen(): string
    {
        if ($this->mail==null) {
            $this->mail = $this->mailRepository->find($this->mailId);
        }
        list($y, $m, $d, $h, $i, $s) = preg_split("/[ :-]/", $this->mail->getField(FieldConstant::SENTDATE));
        $from = mktime($h, $i, $s, $m, $d, $y);
        return DateUtils::getTempsEcoule($from);
    }

    public function getUrl(string $type): string
    {
        $mailFolder = $this->getMailFolder();
        if ($mailFolder->getField(FieldConstant::SLUG)==ConstantConstant::CST_ALERT) {
            $base = '/notification/';
        } elseif ($mailFolder->getField(FieldConstant::SLUG)==ConstantConstant::CST_TRASH) {
            $base = '/trash/';
        } else {
            $base = '/error/';
        }
        return $base.'?action='.$type.'&amp;id='.$this->id;
    }
}
