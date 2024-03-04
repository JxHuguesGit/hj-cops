<?php
namespace src\Utils;

use src\Constant\ConstantConstant;
use src\Utils\PaginateUtils;

class TableUtils
{
    private PaginateUtils $paginate;
    private bool $blnPaginate = false;
    private array $attributes = [];
    /**
     * $header = [
     *      'attributes' => [],
     *      'rows'       => [
     *          0 => [
     *              'attributes' => [],
     *              'cells' => [
     *                  0 => [
     *                      'attributes'=>[]
     *                      'content'=>'',
     *                      'type'=>'',
     *                  ]
     *              ]
     *          ]
     *      ]
     * ];
     */
    private array $header = [];
    private int $nbHeaderRows = -1;
    private array $body = [];
    private int $nbBodyRows = -1;
    private array $foot = [];
    private int $nbFootRows = -1;

    public function __construct()
    {
        $this->attributes = [
            'class'=>'table',// table-sm table-striped m-0 sortableTable text-center',
            'aria-describedby' => '',//'Liste des missions',
        ];
    }

    public function display(): string
    {
        $headContent = '';
        foreach ($this->header['rows'] as $row) {
            $rowContent = '';
            foreach ($row['cells'] as $cell) {
                $cellContent = $cell['content'];
                $cellType = $cell['type'];
                $cellAttributes = $cell['attributes'];
                $rowContent .= HtmlUtils::getBalise($cellType, $cellContent, $cellAttributes);
            }
            $headContent .= HtmlUtils::getBalise('tr', $rowContent, $row['attributes']);
        }

        $bodyContent = '';
        foreach ($this->body['rows'] as $row) {
            $rowContent = '';
            foreach ($row['cells'] as $cell) {
                $cellContent = $cell['content'];
                $cellType = $cell['type'];
                $cellAttributes = $cell['attributes'];
                $rowContent .= HtmlUtils::getBalise($cellType, $cellContent, $cellAttributes);
            }
            $bodyContent .= HtmlUtils::getBalise('tr', $rowContent, $row['attributes']);
        }

        $footContent = '';
        if (!empty($this->foot['rows'])) {
            foreach ($this->foot['rows'] as $row) {
                $rowContent = '';
                foreach ($row['cells'] as $cell) {
                    $cellContent = $cell['content'];
                    $cellType = $cell['type'];
                    $cellAttributes = $cell['attributes'];
                    $rowContent .= HtmlUtils::getBalise($cellType, $cellContent, $cellAttributes);
                }
                $footContent .= HtmlUtils::getBalise('tr', $rowContent, $row['attributes']);
            }
        }

        return HtmlUtils::getBalise(
            'table',
            HtmlUtils::getBalise('thead', $headContent).
            HtmlUtils::getBalise('tbody', $bodyContent).
            HtmlUtils::getBalise('tfoot', $footContent),
            $this->attributes
        );
    }

    public function addHeaderRow(): self
    {
        if ($this->nbHeaderRows==-1) {
            $this->nbHeaderRows = 0;
        } else {
            ++$this->nbHeaderRows;
        }
        $this->header['rows'][$this->nbHeaderRows] = ['attributes'=>[], 'cells'=>[]];
        return $this;
    }
    
    public function addHeaderCell(array $cell): self
    {
        if (!isset($cell['attributes'])) {
            $cell['attributes'] = [];
        }
        if (!isset($cell['type'])) {
            $cell['type'] = 'th';
        }
        array_push($this->header['rows'][$this->nbHeaderRows]['cells'], $cell);
        return $this;
    }

    public function addBodyRows(mixed $objs, int $colspan=1, array $arrParams=[]): self
    {
        if ($this->blnPaginate) {
            $paginateBlock = $this->paginate->getPaginationBlock();
            if ($paginateBlock!='') {
                $this->addFootRow()
                    ->addFootCell(['attributes'=>['colspan'=>$colspan], 'content'=>$paginateBlock ]);
            }
        }

        while ($objs->valid()) {
            $obj = $objs->current();
            $obj->getController()->addBodyRow($this, $arrParams);
            $objs->next();
        }
        return $this;
    }

    public function addBodyRow(): self
    {
        if ($this->nbBodyRows==-1) {
            $this->nbBodyRows = 0;
        } else {
            ++$this->nbBodyRows;
        }
        $this->body['rows'][$this->nbBodyRows] = ['attributes'=>[], 'cells'=>[]];
        return $this;
    }
    
    public function addBodyCell(array $cell): self
    {
        if (!isset($cell['attributes'])) {
            $cell['attributes'] = [];
        }
        if (!isset($cell['type'])) {
            $cell['type'] = 'td';
        }
        array_push($this->body['rows'][$this->nbBodyRows]['cells'], $cell);
        return $this;
    }

    public function addFootRow(): self
    {
        if ($this->nbFootRows==-1) {
            $this->nbFootRows = 0;
        } else {
            ++$this->nbFootRows;
        }
        $this->foot['rows'][$this->nbFootRows] = ['attributes'=>[], 'cells'=>[]];
        return $this;
    }

    public function addFootCell(array $cell): self
    {
        if (!isset($cell['attributes'])) {
            $cell['attributes'] = [];
        }
        if (!isset($cell['type'])) {
            $cell['type'] = 'th';
        }
        array_push($this->foot['rows'][$this->nbFootRows]['cells'], $cell);
        return $this;
    }

    public function setPaginate(array $paginate=[]): self
    {
        $this->blnPaginate = true;
        $this->paginate = new PaginateUtils($paginate);
        return $this;
    }

    public function setTable(array $extraAttributes): self
    {
        if (isset($extraAttributes[ConstantConstant::CST_CLASS])) {
            $this->attributes[ConstantConstant::CST_CLASS] .= ' '.$extraAttributes[ConstantConstant::CST_CLASS];
            unset($extraAttributes[ConstantConstant::CST_CLASS]);
        }
        $this->attributes = array_merge($this->attributes, $extraAttributes);
        return $this;
    }
}
