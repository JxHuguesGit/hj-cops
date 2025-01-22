<?php
namespace src\Repository;

use src\Collection\Collection;
use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Entity\Entity;

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

    public function createQueryBuilder(): self
    {
        $this->baseQuery = "SELECT `".implode('`, `', $this->field)."` FROM ".$this->table." ";
        return $this;
    }

    public function createDistinctQueryBuilder(string $field): self
    {
        $this->baseQuery = "SELECT DISTINCT $field FROM ".$this->table;
        return $this;
    }

    public function find($id): ?Entity
    {
        $this->collection->empty();
        return $this->createQueryBuilder()
            ->setCriteria([FieldConstant::ID=>$id])
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findBy(array $criteria, array $orderBy=[], int $limit=-1): Collection
    {
        return $this->createQueryBuilder()
            ->setCriteria($criteria)
            ->orderBy($orderBy)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findOneBy(array $criteria, array $orderBy=[]): ?Entity
    {
        $collection = $this->findBy($criteria, $orderBy, 1);
        return $collection->valid() ? $collection->current() : null;
    }

    public function findAll(array $orderBy=[FieldConstant::ID=>ConstantConstant::CST_ASC]): Collection
    {
        return $this->findBy([], $orderBy);
    }

    public function getDistinct(string $field): array
    {
        return $this->createDistinctQueryBuilder($field)
            ->orderBy([$field=>ConstantConstant::CST_ASC])
            ->getQuery()
            ->getDistinctResult($field);
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
                $first = false;
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

    public function getDistinctResult(string $field): array
    {
        global $wpdb;

        $args = [];
        if (isset($this->params[ConstantConstant::CST_ORDERBY])) {
            array_push($args, $this->params[ConstantConstant::CST_ORDERBY]);
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

    public function update(Entity $entity): void
    {
        $this->strLimit = '';
        $this->baseQuery = "UPDATE ".$this->table." SET ";
        foreach ($this->field as $field) {
            if ($field==FieldConstant::ID) {
                $id = $entity->getField($field);
            } else {
                $this->baseQuery .= "`$field`='%s', ";
                $this->params['where'][] = $entity->getField($field);
            }
        }
        $this->baseQuery = substr($this->baseQuery, 0, -2);
        $this->strWhere = " WHERE id=%s";
        $this->params['where'][] = $id;

        $this->getQuery()
            ->execQuery();
    }

    public function insert(Entity $entity): void
    {
        $this->strLimit = '';
        $this->baseQuery = "INSERT INTO ".$this->table." (";
        $this->strWhere = "(";
        foreach ($this->field as $field) {
            if ($field!=FieldConstant::ID) {
                $this->baseQuery .= "`$field`, ";
                $this->strWhere  .= "'%s', ";
                $this->params['where'][] = $entity->getField($field);
            }
        }
        $this->baseQuery = substr($this->baseQuery, 0, -2).") VALUES ";
        $this->strWhere = substr($this->strWhere, 0, -2).")";

        $this->getQuery()
            ->execQuery();
    }

    public function delete(Entity $entity): void
    {
        $this->baseQuery = "DELETE FROM ".$this->table." ";
        foreach ($this->field as $field) {
            $this->params['where'][] = $entity->getField($field);
        }
        $this->getQuery()
            ->execQuery();
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






    public function setCriteriaComplex(array $criteria=[]): self
    {
        if (!empty($criteria)) {
            if ($this->strWhere=='') {
                $this->strWhere = " WHERE 1=1";
                $this->params['where'] = [];
            }
            foreach ($criteria as $crit) {
                $this->strWhere .= " AND `".$crit[ConstantConstant::CST_FIELD]."`".$crit['operand']."'%s'";
                $this->params['where'][] = $crit[ConstantConstant::CST_VALUE];
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
                if ($field=='-----') {
                    $this->strWhere .= $value;
                } else {
                    $this->strWhere .= " AND `$field`='%s'";
                    $this->params['where'][] = $value;
                }
            }
        }
        return $this;
    }


    public function getOneOrNullResult(): mixed
    {
        $this->getResult();
        return $this->collection->length()==1 ? $this->collection->current() : null;
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

    public function convertElement(array $row): mixed
    {
        return $row;
    }
}
