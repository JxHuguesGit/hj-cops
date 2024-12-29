<?php
namespace src\Controller;

use src\Collection\IndividuCollection;
use src\Constant\ConstantConstant;
use src\Constant\CssConstant;
use src\Constant\FieldConstant;
use src\Constant\IconConstant;
use src\Constant\TemplateConstant;
use src\Entity\Individu;
use src\Enum\GenderEnum;
use src\Enum\OccupationEnum;
use src\Enum\RaceEnum;
use src\Form\Form;
use src\Repository\IndividuRepository;
use src\Utils\HtmlUtils;
use src\Utils\SessionUtils;
use src\Utils\TableUtils;
use src\Utils\UrlUtils;

class AdminRandomGuyPageController extends AdminPageController
{
    public function getAdminContentPage(): string
    {
        //////////////////////////////////////////
        // BreadCrumbs
        $this->contentHeader = $this->setBreadCrumbsContent();
        //////////////////////////////////////////

        //////////////////////////////////////////
        // Onglets de navigation
        $this->arrTabs = [
            ConstantConstant::CST_RANDOMGUY  => [ConstantConstant::CST_LABEL=>'Individus'],
        ];
        $this->defaultTab = ConstantConstant::CST_BDD;
        
        $this->tabsBar = $this->getTabsBar();
        //////////////////////////////////////////

        //////////////////////////////////////////
        // Contenu
        $postedFormName = SessionUtils::fromPost(ConstantConstant::CST_FORMNAME, '');
        if ($postedFormName=='') {
            $postedFormName = SessionUtils::fromGet(ConstantConstant::CST_FORMNAME, '');
        }

        if ($postedFormName=='searchIndividu') {
            $strContent = $this->getResultPage();
        } elseif ($postedFormName=='updateIndividu') {
            $individu = new Individu($_POST);
            $individu->getForm()->controlForm();
            $form = $individu->getForm()->buildForm();
            $strContent = $form->getFormContent();
        } else {
            $id = SessionUtils::fromGet(ConstantConstant::CST_ID, 0);
            if ($id!=0) {
                $strContent = $this->getEditPage($id);
            } else {
                $strContent = $this->getSearchPage();
            }
        }
        //////////////////////////////////////////

        $attributes = [
            $this->contentHeader,
            $this->tabsBar,
            $strContent,
        ];
        return $this->getRender(TemplateConstant::TPL_ADMIN_CONTENT_WRAP, $attributes);
    }

    private function getEditPage(int $id): string
    {
        $repository = new IndividuRepository(new IndividuCollection());
        $individu = $repository->find($id);
        $form = $individu->getForm()->buildForm();
        return $form->getFormContent();
    }

    private function getResultPage(): string
    {
        $criteria = [];
        $gender = SessionUtils::fromPost(FieldConstant::GENDER);
        if ($gender!=-1) {
            $criteria[FieldConstant::GENDER] = $gender;
        }
        $nameset = SessionUtils::fromPost(FieldConstant::NAMESET);
        if ($nameset!=-1) {
            $criteria[FieldConstant::NAMESET] = $nameset;
        }
        $occupationId = SessionUtils::fromPost(FieldConstant::OCCUPATIONID);
        if ($occupationId!=-1) {
            $criteria[FieldConstant::OCCUPATIONID] = $occupationId;
        }
        $limit = SessionUtils::fromPost('limit');


        $repository = new IndividuRepository(new IndividuCollection());
        $individus = $repository->findBy($criteria, [''=>'RAND()'], $limit);

        $table = new TableUtils();
        $table->setTable([ConstantConstant::CST_CLASS=>CssConstant::CSS_TABLE_SM.' '.CssConstant::CSS_TABLE_STRIPED])
            ->setPaginate([
                ConstantConstant::PAGE_OBJS => $individus,
                ConstantConstant::CST_CURPAGE => $this->arrParams[ConstantConstant::CST_CURPAGE] ?? 1,
                ConstantConstant::CST_URL => UrlUtils::getAdminUrl([
                    ConstantConstant::CST_ONGLET => ConstantConstant::CST_RANDOMGUY,
                    ConstantConstant::CST_FORMNAME => 'searchIndividu',
                ]),
            ])
            ->addHeaderRow()
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'#'])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'Prénom'])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'Nom'])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'Genre'])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'Nationalité'])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'Métier'])
            ->addBodyRows($individus, 6);

        return $table->display();
    }

    private function getSearchPage(): string
    {
        $selectList = [];
        for ($i=1; $i<=10; $i++) {
            $selectList[] = [ConstantConstant::CST_VALUE=>$i, ConstantConstant::CST_LABEL=>$i];
        }

        $form = new Form();
        $form->setContentHeader('Recherche d\'individus')
            ->addRow()
            ->addSelect(FieldConstant::GENDER, FieldConstant::GENDER, 'Genre', GenderEnum::cases(), -1)
            ->addSelect(FieldConstant::NAMESET, FieldConstant::NAMESET, 'Nationalité', RaceEnum::cases(), -1)
            ->addRow()
            ->addSelect(FieldConstant::OCCUPATIONID, FieldConstant::OCCUPATIONID, 'Métier', OccupationEnum::cases(), -1)
            ->addRow()
            ->addSelect('limit', 'limit', 'Quantité', $selectList, -1)
            ->addHidden(ConstantConstant::CST_FORMNAME, ConstantConstant::CST_FORMNAME, 'searchIndividu');

        return $form->getFormContent();
    }

    public function setBreadCrumbsContent(): string
    {
        return $this->getRender(
            TemplateConstant::TPL_CONTENT_HEADER,
            [
                'Recherche d\'individus',
                parent::setBreadCrumbsContent()
                    . ' ' . HtmlUtils::getIcon(IconConstant::I_CARETRIGHT)
                    . ' ' . HtmlUtils::getButton(
                        'Individus',
                        [ConstantConstant::CST_CLASS=>'btn-secondary disabled']
                    )
            ]
        );
    }
}
