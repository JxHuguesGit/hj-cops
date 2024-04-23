<?php
namespace src\Controller;

use src\Collection\PlayerCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Constant\IconConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\Player;
use src\Enum\RankEnum;
use src\Enum\SectionEnum;
use src\Form\BinomeForm;
use src\Form\PlayerForm;
use src\Repository\PlayerRepository;
use src\Utils\HtmlUtils;
use src\Utils\SessionUtils;
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
        if ($arrParams['isAdmin']) {
            $matricule = $this->cops->getField(FieldConstant::SERIALNUMBER);
            $aContent = $this->cops->getFullName(true);
            $href = add_query_arg(['action'=>'view', 'id'=>$this->cops->getField(FieldConstant::ID)]);
            $strName = HtmlUtils::getLink($aContent, $href);
        } else {
            $matricule = substr($this->cops->getField(FieldConstant::SERIALNUMBER), -3);
            $strName = $this->cops->getFullName(true);
        }

        $rank = $this->cops->getField(FieldConstant::RANK);
        $strRank = ($rank>=1&&$rank<=99) ? RankEnum::fromDb($rank) : '';
        $section = ucwords($this->cops->getField(FieldConstant::SECTION));
        $strSection = ($section>=1&&$section<=99) ? SectionEnum::fromDb($section) : '';

        $binome = $this->cops->getBinome();

        $table->addBodyRow($arrParams);
        if ($arrParams['isAdmin']) {
            $table->addBodyCell([ConstantConstant::CST_CONTENT=>'&nbsp;']);
        }
        $table->addBodyCell([ConstantConstant::CST_CONTENT=>$matricule])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$strName])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$strRank])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$strSection])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$binome==null ? '' : $binome->getFullName(true)]);
        if ($arrParams['isAdmin']) {
            $table->addBodyCell([ConstantConstant::CST_CONTENT=>'&nbsp;']);
        }
    }

    public function getAdminContentPage(): string
    {
        $action = $this->arrParams['action'] ?? 'list';
        if ($action=='view') {
            $id = $this->arrParams['id'] ?? 0;
            $cops = $this->repository->find($id);
            if ($cops!=null) {
                return $cops->getController()->getAdminEditContentPage();
            }
        }
        return $this->getAdminListContentPage();
    }

    private function getAdminListContentPage(): string
    {
        //////////////////////////////////////////
        // BreadCrumbs
        $contentHeader = $this->setBreadCrumbsContent();
        //////////////////////////////////////////

        //////////////////////////////////////////
        // Contenu du corps
        $this->initRepositories();
        // Initialisation de la table
        $table = new TableUtils();
        $table->setTable([ConstantConstant::CST_CLASS=>'table-sm table-striped']);
        $players = $this->repository->findAllCopsBy();
        // Pagination
        $table->setPaginate([
            ConstantConstant::PAGE_OBJS => $players,
            ConstantConstant::CST_CURPAGE => $this->arrParams[ConstantConstant::CST_CURPAGE] ?? 1,
            ConstantConstant::CST_URL => UrlUtils::getAdminUrl([ConstantConstant::CST_ONGLET=>'player']),
        ]);
        // Définition du header de la table
        $table->addHeaderRow()
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>ConstantConstant::CST_NBSP])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>LabelConstant::LBL_MATRICULE])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>LabelConstant::LBL_NAME])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>LabelConstant::LBL_GRADE])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>LabelConstant::LBL_GROUP])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>LabelConstant::LBL_BINOME])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>ConstantConstant::CST_NBSP]);

        $table->addBodyRows($players, 7, ['isAdmin'=>true]);
        //////////////////////////////////////////

        $attributes = [
            $contentHeader,
            'Liste des Cops',
            $table->display(),
        ];
        return $this->getRender(TemplateConstant::TPL_ADMIN_CONTENT_WRAP, $attributes);
    }

    public function setBreadCrumbsContent(): string
    {
        return $this->getRender(
            TemplateConstant::TPL_CONTENT_HEADER,
            [
                'COPS',
                parent::setBreadCrumbsContent()
                    . ' ' . HtmlUtils::getIcon(IconConstant::I_CARETRIGHT)
                    . ' ' . HtmlUtils::getButton(
                        'COPS',
                        [ConstantConstant::CST_CLASS=>'btn-secondary disabled']
                    )
            ]
        );
    }

    public function getAdminEditContentPage(): string
    {
        $postedFormName = $_POST['formName'] ?? null;
        if ($postedFormName==null) {
            $postedFormName = $_GET['formName'] ?? null;
        }

        $formPlayer = new PlayerForm($this->cops);
        $formBinome = new BinomeForm();
        $formBinome->setPlayer($this->cops);

        if ($postedFormName=='copsPlayer') {
            $formPlayer->controlForm();
// On est en train de mofifier une entrée de copsPlayer.
// On va vérifier que les données saisies sont cohérentes.
// Ensuite, on sauvegardera les données
// Et sinon, on affichera des erreurs
        } elseif ($postedFormName=='copsPlayerBinome') {
            $formBinome->controlForm();
        }

        $formPlayer->buildForm();
        $formBinome->buildForm();

        return HtmlUtils::getDiv($formPlayer->getFormContent().$formBinome->getFormContent(), [ConstantConstant::CST_CLASS=>'row mx-3']);
    }
}
