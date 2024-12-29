<?php
namespace src\Form;

use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Constant\LabelConstant;
use src\Entity\Individu;
use src\Enum\GenderEnum;
use src\Enum\OccupationEnum;
use src\Enum\RaceEnum;
use src\Utils\CardUtils;
use src\Utils\DateUtils;
use src\Utils\HtmlUtils;
use src\Utils\SessionUtils;

class IndividuForm extends Form
{
    public Individu $individu;

    public function __construct(Individu $individu=null)
    {
        $this->individu = $individu ?? new Individu();
        $this->contentHeader = 'Edition d\'un individu';
    }

    public function buildForm(): self
    {
        $defaultAttributes = [];

        list($y, $m, $d) = explode('-', $this->individu->getField(FieldConstant::BIRTHDAY));
        $strBirthday = implode('/', [$d, $m, $y]);

        $now = DateUtils::getCopsDate('ts');
        $start = mktime(0, 0, 0, $m, $d, $y);
        $strAge = floor(($now-$start)/(60*60*24*365)).' ans';

        $this->addRow()
            ->addInput(
                FieldConstant::LASTNAME,
                FieldConstant::LASTNAME,
                LabelConstant::LBL_NAME,
                $this->individu->getField(FieldConstant::LASTNAME),
                $defaultAttributes
            )
            ->addInput(
                FieldConstant::FIRSTNAME,
                FieldConstant::FIRSTNAME,
                LabelConstant::LBL_FIRSTNAME,
                $this->individu->getField(FieldConstant::FIRSTNAME),
                $defaultAttributes
            )
            ->addRow()
            ->addSelect(
                FieldConstant::GENDER,
                FieldConstant::GENDER,
                LabelConstant::LBL_GENDER,
                GenderEnum::cases(),
                $this->individu->getField(FieldConstant::GENDER)
            )
            ->addSelect(
                FieldConstant::NAMESET,
                FieldConstant::NAMESET,
                LabelConstant::LBL_NAMESET,
                RaceEnum::cases(),
                $this->individu->getField(FieldConstant::NAMESET)
            )
            ->addRow()
            ->addInput(
                FieldConstant::WEIGHT,
                FieldConstant::WEIGHT,
                LabelConstant::LBL_WEIGHT,
                $this->individu->getField(FieldConstant::WEIGHT),
                $defaultAttributes
            )
            ->addInput(
                FieldConstant::HEIGHT,
                FieldConstant::HEIGHT,
                LabelConstant::LBL_HEIGHT,
                $this->individu->getField(FieldConstant::HEIGHT),
                $defaultAttributes
            )
            ->addRow()
            ->addInput(
                FieldConstant::BIRTHDAY,
                FieldConstant::BIRTHDAY,
                LabelConstant::LBL_BIRTHDAY,
                $strBirthday,
                $defaultAttributes
            )
            ->addInput(
                '',
                '',
                LabelConstant::LBL_AGE,
                $strAge,
                [ConstantConstant::CST_READONLY=>ConstantConstant::CST_READONLY]
            )
            ->addRow()
            ->addSelect(
                FieldConstant::OCCUPATIONID,
                FieldConstant::OCCUPATIONID,
                LabelConstant::LBL_OCCUPATION,
                OccupationEnum::cases(),
                $this->individu->getField(FieldConstant::OCCUPATIONID)
            )
            ->addRow()
            ->addInput(
                FieldConstant::STREETADDRESS,
                FieldConstant::STREETADDRESS,
                LabelConstant::LBL_ADDRESS,
                $this->individu->getField(FieldConstant::STREETADDRESS),
                $defaultAttributes
            )
            ->addRow()
            ->addInput(
                FieldConstant::CITY,
                FieldConstant::CITY,
                LabelConstant::LBL_CITY,
                $this->individu->getField(FieldConstant::CITY),
                $defaultAttributes
            )
            ->addInput(
                FieldConstant::ZIPCODE,
                FieldConstant::ZIPCODE,
                LabelConstant::LBL_ZIPCODE,
                $this->individu->getField(FieldConstant::ZIPCODE),
                $defaultAttributes
            )
            ->addRow()
            ->addHidden(FieldConstant::ID, FieldConstant::ID, $this->individu->getField(FieldConstant::ID))
            ->addHidden(ConstantConstant::CST_FORMNAME, ConstantConstant::CST_FORMNAME, 'updateIndividu')
            ;
    
        return $this;
    }

    public function controlForm(): void
    {
        ///////////////////////////////////////////////
        // Les Flags
        $blnErrors = false;
        $tabErrors = [];
        ///////////////////////////////////////////////

        ///////////////////////////////////////////////
        // Les champs modifiables
        ///////////////////////////////////////////////
        // lastname
        if ($this->individu->getField(FieldConstant::LASTNAME)=='') {
            $blnErrors = false;
            $tabErrors[] = 'Le champ <<b>Nom</b>> est obligatoire.';
        }
        // firstname
        if ($this->individu->getField(FieldConstant::FIRSTNAME)=='') {
            $blnErrors = false;
            $tabErrors[] = 'Le champ <<b>Prénom</b>> est obligatoire.';
        }
        ///////////////////////////////////////////////

        if (!$blnErrors) {
            $this->individu->update();
        }
    }
}
