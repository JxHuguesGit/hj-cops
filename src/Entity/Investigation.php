<?php
namespace src\Entity;

use src\Collection\IndividuCollection;
use src\Collection\ChronologieCollection;
use src\Collection\InvestigationCollection;
use src\Collection\PlayerCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Controller\InvestigationController;
use src\Entity\Individu;
use src\Entity\Player;
use src\Form\InvestigationForm;
use src\Repository\ChronologieRepository;
use src\Repository\IndividuRepository;
use src\Repository\InvestigationRepository;
use src\Repository\PlayerRepository;

class Investigation extends Entity
{
    //////////////////////////////////////////////////
    // ATTRIBUTES
    //////////////////////////////////////////////////
    protected int $id;
    protected string $name;
    protected int $firstDetective;
    protected int $districtAttorney;
    protected string $galResume;
    protected string $sdcDescription;
    protected string $sdcClues;
    protected string $pdContent;

    protected InvestigationRepository $repository;

    //////////////////////////////////////////////////
    // CONSTRUCT
    //////////////////////////////////////////////////
    public function __construct(array $attributes=[])
    {
        parent::__construct($attributes);
        //$this->initRepositories();
    }

    private function initRepositories()
    {
        $this->repository = new InvestigationRepository(new InvestigationCollection());
    }

    public static function initFromRow($row): Investigation
    {
        $obj = new Investigation();
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
            FieldConstant::NAME,
            FieldConstant::FIRSTDETECTIVE,
            FieldConstant::DISTRICTATTORNEY,
            FieldConstant::GAL_RESUME,
            FieldConstant::SDC_DESCRIPTION,
            FieldConstant::SDC_CLUES,
            FieldConstant::PD_CONTENT,
        ];
    }

    public function update(): void
    {
        $this->initRepositories();
        $this->repository->update($this);
    }

    public function insert(): void
    {
        $this->initRepositories();
        $this->repository->insert($this);
    }

    public function getController(): InvestigationController
    {
        $investigationController = new InvestigationController();
        $investigationController->setInvestigation($this);
        return $investigationController;
    }

    public function getForm(): InvestigationForm
    {
        return new InvestigationForm($this);
    }

    public function getFirstDetective(): ?Player
    {
        $repository = new PlayerRepository(new PlayerCollection());
        return $repository->find($this->firstDetective);
    }

    public function getDistrictAttorney(): ?Individu
    {
        $repository = new IndividuRepository(new IndividuCollection());
        return $repository->find($this->districtAttorney);
    }

    public function getChronologies(): ChronologieCollection
    {
        $repository = new ChronologieRepository(new ChronologieCollection());
        return $repository->findBy([
                FieldConstant::INVESTIGATIONID=>$this->id,
            ], [
                FieldConstant::DATE_EVENT=>ConstantConstant::CST_ASC,
                FieldConstant::HOUR_EVENT=>ConstantConstant::CST_ASC
            ]
        );
    }

}
