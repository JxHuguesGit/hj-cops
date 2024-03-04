<?php
namespace src\Repository;

use src\Collection\Collection;
use src\Constant\FieldConstant;

class Repository
{
    protected $query      = '';

    protected $baseQuery  = '';
    protected $strWhere   = '';
    protected $strOrderBy = '';
    protected $strLimit   = '';
    protected $params     = [];

    protected $field = [];
    protected $table = '';
    protected $collection;

    public function createQueryBuilder(string $alias=''): self
    {
        $this->baseQuery = "SELECT `".implode('`, `', $this->field)."` FROM ".$this->table." $alias";
        return $this;
    }

    public function updateQueryBuilder($obj): self
    {
        $this->strLimit = '';
        $this->baseQuery = "UPDATE ".$this->table." SET ";
        foreach ($this->field as $field) {
            if ($field==FieldConstant::ID) {
                $id = $obj->getField($field);
            } else {
                $this->baseQuery .= "`$field`='%s', ";
                $this->params['where'][] = $obj->getField($field);
            }
        }
        $this->baseQuery = substr($this->baseQuery, 0, -2);
        $this->strWhere = " WHERE id=%s";
        $this->params['where'][] = $id;

        return $this;
    }

    public function insertQueryBuilder($obj): self
    {
        $this->strLimit = '';
        $this->baseQuery = "INSERT INTO ".$this->table." (";
        $this->strWhere = "(";
        foreach ($this->field as $field) {
            if ($field!=FieldConstant::ID) {
                $this->baseQuery .= "`$field`, ";
                $this->strWhere  .= "'%s', ";
                $this->params['where'][] = $obj->getField($field);
            }
        }
        $this->baseQuery = substr($this->baseQuery, 0, -2).") VALUES ";
        $this->strWhere = substr($this->strWhere, 0, -2).")";

        return $this;
    }

    public function setCriteriaComplex(array $criteria=[]): self
    {
        if (!empty($criteria)) {
            $this->strWhere = " WHERE 1=1";
            $this->params['where'] = [];
            foreach ($criteria as $crit) {
                $this->strWhere .= " AND ".$crit['field'].$crit['operand']."'%s'";
                $this->params['where'][] = $crit['value'];
            }
        }
        return $this;
    }

    public function setCriteria(array $criteria=[]): self
    {
        if (!empty($criteria)) {
            $this->strWhere = " WHERE 1=1";
            $this->params['where'] = [];
            foreach ($criteria as $field => $value) {
                $this->strWhere .= " AND `$field`='%s'";
                $this->params['where'][] = $value;
            }
        }
        return $this;
    }

    public function orderBy(array $orderBy=[]): self
    {
        if (!empty($orderBy)) {
            $this->strOrderBy = " ORDER BY ";
            $first = true;
            foreach ($orderBy as $key=>$value) {
                if (!$first) {
                    $this->strOrderBy .= ', ';
                }
                $this->strOrderBy .= $key.' '.$value;
            }
        }
        return $this;
    }

    public function setMaxResults(int $limit=-1): self
    {
        if ($limit>0) {
            $this->strLimit = " LIMIT $limit";
        }
        return $this;
    }

    public function getQuery(): self
    {
        $this->query = $this->baseQuery.$this->strWhere.$this->strOrderBy.$this->strLimit;
        return $this;
    }

    public function getOneOrNullResult(): mixed
    {
        $this->getResult();
        return $this->collection->length()==1 ? $this->collection->current() : null;
    }

    public function execQuery(): void
    {
        global $wpdb;
        $args = [];
        if (isset($this->params['where'])) {
            while (!empty($this->params['where'])) {
                $constraint = array_shift($this->params['where']);
                array_push($args, $constraint);
            }
        }
        $query = vsprintf($this->query, $args);
        $wpdb->query($query);
    }

    public function getResult(): Collection
    {
        global $wpdb;
        $args = [];
        if (isset($this->params['where'])) {
            while (!empty($this->params['where'])) {
                $constraint = array_shift($this->params['where']);
                array_push($args, $constraint);
            }
        }
        $query = $wpdb->prepare($this->query, $args);
        $rows  = $wpdb->get_results($query);

        while (!empty($rows)) {
            $row = array_shift($rows);
            $this->collection->addItem($this->convertElement($row));
        }
        return $this->collection;
    }

    public function getDistinctResult(string $field): array
    {
        global $wpdb;

        $args = [];
        if (isset($this->params['orderBy'])) {
            array_push($args, $this->params['orderBy']);
        }
        $query = vsprintf($this->query, $args);
        $rows  = $wpdb->get_results($query);

        $results = [];
        while (!empty($rows)) {
            $row = array_shift($rows);
            array_push($results, $row->{$field});
        }
        return $results;
    }

    /*
    ->andWhere('m.id = :val')
    ->setParameter('val', $id)
    ->getOneOrNullResult()
*/
}
