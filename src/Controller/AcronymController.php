<?php
namespace src\Controller;

use src\Collection\AcronymCollection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Constant\LabelConstant;
use src\Constant\TemplateConstant;
use src\Entity\Acronym;
use src\Repository\AcronymRepository;
use src\Utils\TableUtils;
use src\Utils\UrlUtils;

class AcronymController extends PageController
{
    private Acronym $acronym;
    
    public function __construct(Acronym $acronym=null)
    {
        $this->acronym = $acronym ?? new Acronym();
    }
    
    public function getContentPage($arrParams): string
    {
        $this->arrParams = $arrParams;
        
        $repository = new AcronymRepository(new AcronymCollection());
        $orders = [FieldConstant::CODE=>ConstantConstant::CST_ASC];
        $acronyms = $repository->findAll($orders);

        $table = new TableUtils();
        $table->setTable([ConstantConstant::CST_CLASS=>'table-sm table-striped']);
        $table->setPaginate([
            ConstantConstant::PAGE_OBJS => $acronyms,
            ConstantConstant::CST_CURPAGE => $this->arrParams[ConstantConstant::CST_CURPAGE] ?? 1,
            ConstantConstant::CST_URL => UrlUtils::getPublicUrl(
                ConstantConstant::CST_LIBRARY,
                [ConstantConstant::CST_PAGE=>ConstantConstant::CST_ACRONYMS]
            ),
        ]);

        $table->addHeaderRow()
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>'#'])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>LabelConstant::LBL_CODE])
            ->addHeaderCell([ConstantConstant::CST_CONTENT=>LabelConstant::LBL_NAME]);

        $table->addBodyRows($acronyms, 3, $arrParams);

        $attributes = [
            LabelConstant::LBL_ACRONYMS,
            $table->display(),
            '',
        ];
        return $this->getRender(TemplateConstant::TPL_LIBRARY_SUBPANEL, $attributes);
    }

    public function addBodyRow(TableUtils &$table, array $arrParams=[]): void
    {
        $id = $this->acronym->getField(FieldConstant::ID);
        $strCode = $this->acronym->getField(FieldConstant::CODE);
        $strName = $this->acronym->getField(FieldConstant::NAME);

        $table->addBodyRow()
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$id])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$strCode])
            ->addBodyCell([ConstantConstant::CST_CONTENT=>$strName]);
    }

}
