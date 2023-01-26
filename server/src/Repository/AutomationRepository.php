<?php
    namespace App\Repository;

    use App\Entity\Automation;
    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
    use Doctrine\Persistence\ManagerRegistry;

    /**
     * @extends ServiceEntityRepository<Automation>
     *
     * @method Automation|null find($id, $lockMode = null, $lockVersion = null)
     * @method Automation|null findOneBy(array $criteria, array $orderBy = null)
     * @method Automation[]    findAll()
     * @method Automation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
     */
    class AutomationRepository extends ServiceEntityRepository
    {
        public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, Automation::class);
        }

        public function add(Automation $entity, bool $flush = false): void
        {
            $this->getEntityManager()->persist($entity);

            if ($flush) {
                $this->getEntityManager()->flush();
            }
        }

        public function remove(Automation $entity, bool $flush = false): void
        {
            $this->getEntityManager()->remove($entity);

            if ($flush) {
                $this->getEntityManager()->flush();
            }
        }

    //    /**
    //     * @return Automation[] Returns an array of Automation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Automation
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    }
?>