<?php
namespace src\Repository;

use src\Collection\PlayerCollection;
use src\Entity\Player;

/**
 * @method Player|null find($id)
 * @method Player|null findOneBy(array $criteria, array $orderBy=null)
 * @method Player[]    findAll()
 * @method Player[]    findBy(array $criteria, array $orderBy=null, $limit=null, $offset=null)
 */
class PlayerRepository extends Repository
{
    public function __construct(PlayerCollection $collection)
    {
        $this->table = 'copsPlayer';
        $this->collection = $collection;
    }

    public function createQueryBuilder(string $alias=''): self
    {
        $this->field = Player::getFields();
        return parent::createQueryBuilder($alias);
    }

    public function createDistinctQueryBuilder(string $field): self
    {
        $this->baseQuery = "SELECT DISTINCT $field FROM ".$this->table;
        return $this;
    }

    public function convertElement($row): Player
    {
        return Player::initFromRow($row);
    }

    public function find($id): ?Player
    {
        return $this->createQueryBuilder('p')
            ->setCriteria(['p.id'=>$id])
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneBy(array $criteria, array $orderBy=[]): ?Player
    {
        $collection = $this->findBy($criteria, $orderBy, 1);
        return $collection->valid() ? $collection->current() : null;
    }

    public function findAll(array $orderBy=['lastname'=>'ASC']): PlayerCollection
    {
        return $this->findBy([], $orderBy);
    }

    public function findBy(array $criteria, array $orderBy=[], int $limit=-1, int $offset=0): PlayerCollection
    {
        return $this->createQueryBuilder('p')
            ->setCriteria($criteria)
            ->orderBy($orderBy)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getDistinct(string $field): array
    {
        return $this->createDistinctQueryBuilder($field)
            ->orderBy([$field=>'asc'])
            ->getQuery()
            ->getDistinctResult($field);
    }

    public function update(Player $player): void
    {
        $this->field = Player::getFields();
        $this->updateQueryBuilder($player)
            ->getQuery()
            ->execQuery();
    }
}
