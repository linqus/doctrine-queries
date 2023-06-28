<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\FortuneCookie;
use App\Model\CategoryFortuneStats;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FortuneCookie>
 *
 * @method FortuneCookie|null find($id, $lockMode = null, $lockVersion = null)
 * @method FortuneCookie|null findOneBy(array $criteria, array $orderBy = null)
 * @method FortuneCookie[]    findAll()
 * @method FortuneCookie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FortuneCookieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FortuneCookie::class);
    }

    public function save(FortuneCookie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FortuneCookie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function fortunePrinted(Category $category): ?CategoryFortuneStats
    {
        /*         
        $query = $this->createQueryBuilder('fortuneCookie')
                ->select(sprintf(
                    'NEW %s(
                        SUM(fortuneCookie.numberPrinted),
                        AVG(fortuneCookie.numberPrinted),
                        category.name
                    ) ',CategoryFortuneStats::class))
                ->join('fortuneCookie.category','category')
                ->andWhere('fortuneCookie.category = :category')
                ->setParameter('category',$category)
                ->getQuery()
                ;
        //dd($query->getSingleResult()); 
        */

        $conn = $this->getEntityManager()->getConnection();
        //$sql = 'SELECT * from fortune_cookie';
        $sql = 'SELECT SUM(fortune_cookie.number_printed) AS fortunePrinted, AVG(fortune_cookie.number_printed) averagePrinted, category.name categoryName FROM fortune_cookie INNER JOIN category ON category.id = fortune_cookie.category_id WHERE fortune_cookie.category_id = :category';

        $statement = $conn->prepare($sql);
        $statement->bindValue('category',$category->getId(),ParameterType::INTEGER);

        $result = $statement->executeQuery();

        
        //dd(...$result->fetchAssociative());
        return new CategoryFortuneStats(...$result->fetchAssociative());
    }

    public static function createNotDiscontinuedFortunesCriteria(): Criteria
    {
        return Criteria::create()
            ->andWhere(Criteria::expr()->eq('discontinued',false));
    }
//    /**
//     * @return FortuneCookie[] Returns an array of FortuneCookie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FortuneCookie
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
