<?php
namespace src\Repository;

use src\Constant\ConstantConstant;
use src\Constant\FieldConstant;
use src\Collection\BinomeCollection;
use src\Entity\Binome;

class BinomeRepository extends Repository
{
    public function __construct(BinomeCollection $collection)
    {
        $this->table = 'copsBinome';
        $this->collection = $collection;
        $this->field = Binome::getFields();
    }

    public function convertElement($row): Binome
    {
        return Binome::initFromRow($row);
    }

    public function findBinome(array $criteria, array $orderBy=[FieldConstant::STARTDATE=>ConstantConstant::CST_ASC], int $limit=-1): BinomeCollection
    {
        $this->baseQuery  = "SELECT `".implode('`, `', $this->field)."` ";
        $this->baseQuery .= "FROM ".$this->table." ";
        $this->baseQuery .= "WHERE (leaderId = '%s' OR binomeId = '%s') ";

        $this->params['where'] = [
            $criteria[FieldConstant::ID],
            $criteria[FieldConstant::ID],
        ];

        if (isset($criteria[ConstantConstant::CST_CURDATE])) {
            $this->baseQuery .= "AND %s BETWEEN startDate AND endDate ";
            $this->params['where'][] = $criteria[ConstantConstant::CST_CURDATE];
        }

        return $this->orderBy($orderBy)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
