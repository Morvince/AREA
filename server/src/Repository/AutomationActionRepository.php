<?php
    namespace App\Repository;

    use App\Entity\AutomationAction;
    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
    use Doctrine\Persistence\ManagerRegistry;

    /**
     * @extends ServiceEntityRepository<AutomationAction>
     *
     * @method AutomationAction|null find($id, $lockMode = null, $lockVersion = null)
     * @method AutomationAction|null findOneBy(array $criteria, array $orderBy = null)
     * @method AutomationAction[]    findAll()
     * @method AutomationAction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
     */
    class AutomationActionRepository extends ServiceEntityRepository
    {
        public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, AutomationAction::class);
        }

        public function add(AutomationAction $entity, bool $flush = false): void
        {
            $this->getEntityManager()->persist($entity);

            if ($flush) {
                $this->getEntityManager()->flush();
            }
        }

        public function remove(AutomationAction $entity, bool $flush = false): void
        {
            $this->getEntityManager()->remove($entity);

            if ($flush) {
                $this->getEntityManager()->flush();
            }
        }

    //    /**
    //     * @return AutomationAction[] Returns an array of AutomationAction objects
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

    //    public function findOneBySomeField($value): ?AutomationAction
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