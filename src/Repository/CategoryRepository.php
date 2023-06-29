<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function save(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     *  @return Category[] Returns an array of Category objects
     */
    public function findAllOrdered(): array
    {
        //$dql = 'SELECT category FROM App\Entity\Category as category ORDER BY category.name DESC';

        $qb = $this->addOrderByCategoryName();
        
        $query = $qb->getQuery();

        //dd($query->getDQL());

        return $query->getResult();
    }

    /**
     *  @param string $search Search word  
     *  @return Category[] Returns an array of Category objects
     */
    public function search(string $search): array{

        $searchTermList = explode(' ', $search);

        $qb = $this->addOrderByCategoryName();

        $query = $this->addFortuneCookieJoinAndSelect($qb)
            ->andWhere('category.name LIKE :searchword OR category.name IN (:searchList) OR category.iconKey LIKE :searchword OR fortuneCookie.fortune LIKE :searchword')
            ->setParameter('searchword', '%'.$search.'%')
            ->setParameter('searchList', $searchTermList)
            ->getQuery();
        //dd($query->getDQL());
        return $query->getResult();
    }

    public function findWithFortunesJoin(int $id): ?Category
    {

        $query = $this->addFortuneCookieJoinAndSelect()
                    ->andWhere('category.id = :id')
                    ->setParameter('id',$id)
                    ->orderBy('RAND()')
                    ->getQuery();
        return $query->getOneOrNullResult();
                
    }


    private function addFortuneCookieJoinAndSelect(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return ($queryBuilder ?? $this->createQueryBuilder('category'))
            ->addSelect('fortuneCookie')
            ->leftJoin('category.fortuneCookies','fortuneCookie');
    }

    private function addOrderByCategoryName(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return ($queryBuilder ?? $this->createQueryBuilder('category'))
            ->addOrderBy('category.name',Criteria::DESC);
    }
    
//    /**
//     * @return Category[] Returns an array of Category objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Category
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
