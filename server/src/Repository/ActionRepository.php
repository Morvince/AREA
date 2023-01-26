<?php
    namespace App\Repository;

    use App\Entity\Action;
    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
    use Doctrine\Persistence\ManagerRegistry;

    /**
     * @extends ServiceEntityRepository<Action>
     *
     * @method Action|null find($id, $lockMode = null, $lockVersion = null)
     * @method Action|null findOneBy(array $criteria, array $orderBy = null)
     * @method Action[]    findAll()
     * @method Action[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
     */
    class ActionRepository extends ServiceEntityRepository
    {
        public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, Action::class);
        }

        public function add(Action $entity, bool $flush = false): void
        {
            $this->getEntityManager()->persist($entity);

            if ($flush) {
                $this->getEntityManager()->flush();
            }
        }

        public function remove(Action $entity, bool $flush = false): void
        {
            $this->getEntityManager()->remove($entity);

            if ($flush) {
                $this->getEntityManager()->flush();
            }
        }

        public function findById($action_id)
        {
            return $this->createQueryBuilder("action")
                ->where("action.id = :id")
                ->setParameter("id", $action_id)
                ->getQuery()
                ->getResult()
            ;
        }

    //    /**
    //     * @return Action[] Returns an array of Action objects
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

    //    public function findOneBySomeField($value): ?Action
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