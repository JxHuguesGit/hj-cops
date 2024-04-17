<?php
namespace src\Controller;

use src\Collection\PlayerCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\Player;
use src\Repository\PlayerRepository;
use src\Utils\TableUtils;
use src\Utils\UrlUtils;

class CopsController extends PageController
{
    public Player $cops;
    public PlayerRepository $repository;
    
    public function __construct(Player $cops=null)
    {
        $this->cops = $cops ?? new Player();
        $this->initRepositories();
    }

    private function initRepositories()
    {
        $this->repository = new PlayerRepository(new PlayerCollection());
    }
    
    public function getContentPage($arrParams): string
    {
        $criteria = [];
        $this->arrParams = $arrParams;
        $table = new TableUtils();
        $table->setTable([ConstantConstant::CST_CLASS=>'table-sm table-striped']);
        $filterSection = $this->arrParams['filtersection'] ?? '';
        $table->setFilter([
            ConstantConstant::CST_LABEL => LabelConstant::LBL_GROUP,
            ConstantConstant::CST_FIELD => FieldConstant::SECTION,
            ConstantConstant::CST_VALUES => $this->repository->getDistinct(FieldConstant::SECTION),
            ConstantConstant::CST_COL => 4,
            ConstantConstant::CST_SELECTED => $filterSection,
        ]);
        if ($filterSection!='') {
            $criteria = [FieldConstant::SECTION=>$filterSection];
        }
        
        $this->initRepositories();
        $players = $this->repository->findAllCopsBy($criteria);

        $table->setPaginate([
            ConstantConstant::PAGE_OBJS => $players,
            ConstantConstant::CST_CURPAGE => $this->arrParams[ConstantConstant::CST_CURPAGE] ?? 1,
        ]);

        $table->addHeaderRow()
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>LabelConstant::LBL_MATRICULE])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>LabelConstant::LBL_NAME])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>LabelConstant::LBL_GRADE])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>LabelConstant::LBL_GROUP])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>LabelConstant::LBL_BINOME]);

        $table->addBodyRows($players, 5, $arrParams);

        $attributes = [
            LabelConstant::LBL_COPS,
            $table->display(),
            '',
        ];
        return $this->getRender(TemplateConstant::TPL_LIBRARY_SUBPANEL, $attributes);
    }

    public function addBodyRow(TableUtils &$table, array $arrParams=[]): void
    {
        $matricule = substr($this->cops->getField(FieldConstant::SERIALNUMBER), -3);
        $strName = $this->cops->getFullName(true);
        $rank = $this->cops->getField(FieldConstant::RANK);
        $section = ucwords($this->cops->getField(FieldConstant::SECTION));

        $table->addBodyRow($arrParams)
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$matricule])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$strName])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$rank])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$section])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>'']);
    }

}
