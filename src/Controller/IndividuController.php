<?php
namespace src\Controller;

use src\Collection\IndividuCollection;
use src\Constant\ConstantConstant;
use src\Constant\CssConstant;
use src\Constant\FieldConstant;
use src\Constant\IconConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\Individu;
use src\Enum\GenderEnum;
use src\Enum\OccupationEnum;
use src\Enum\RaceEnum;
use src\Repository\IndividuRepository;
use src\Utils\CardUtils;
use src\Utils\HtmlUtils;
use src\Utils\TableUtils;
use src\Utils\UrlUtils;

class IndividuController extends UtilitiesController
{
    private Individu $individu;

    public function __construct(Individu $individu=null)
    {
        $this->individu = $individu ?? new Individu();
    }

    // Note : suppression du paramètre array $arrParams=[] qui n'était pas utilisé.
    public function addBodyRow(TableUtils &$table): void
    {
        $id = $this->individu->getField(FieldConstant::ID);
        $linkAttributes = [
            ConstantConstant::CST_ONGLET => ConstantConstant::CST_RANDOMGUY,
            ConstantConstant::CST_ID => $id,
        ];
        $strId     = HtmlUtils::getLink(
            $id,
            UrlUtils::getAdminUrl($linkAttributes)
        ) ;
        $firstName = $this->individu->getField(FieldConstant::FIRSTNAME);
        $lastName  = $this->individu->getField(FieldConstant::LASTNAME);
        $gender    = GenderEnum::fromDb($this->individu->getField(FieldConstant::GENDER));
        $race      = RaceEnum::fromDb($this->individu->getField(FieldConstant::NAMESET));
        $metierId  = $this->individu->getField(FieldConstant::OCCUPATIONID);

        $table->addBodyRow()
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$strId])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$firstName])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$lastName])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$gender])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$race])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>OccupationEnum::fromDb($metierId)]);
    }

}
