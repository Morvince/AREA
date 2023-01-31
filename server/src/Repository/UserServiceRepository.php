<?php
    namespace App\Repository;

    use App\Entity\UserService;
    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
    use Doctrine\Persistence\ManagerRegistry;

    /**
     * @extends ServiceEntityRepository<UserService>
     *
     * @method UserService|null find($id, $lockMode = null, $lockVersion = null)
     * @method UserService|null findOneBy(array $criteria, array $orderBy = null)
     * @method UserService[]    findAll()
     * @method UserService[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
     */
    class UserServiceRepository extends ServiceEntityRepository
    {
        public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, UserService::class);
        }

        public function add(UserService $entity, bool $flush = false)
        {
            $this->getEntityManager()->persist($entity);
            if ($flush) {
                $this->getEntityManager()->flush();
            }
        }

        public function edit(UserService $entity, bool $flush = false)
        {
            $this->getEntityManager()->persist($entity);
            if ($flush) {
                $this->getEntityManager()->flush();
            }
        }

        public function remove(UserService $entity, bool $flush = false)
        {
            $this->getEntityManager()->remove($entity);
            if ($flush) {
                $this->getEntityManager()->flush();
            }
        }

       public function findByUserIdAndServiceId($user_id, $service_id)
       {
            return $this->createQueryBuilder("user_service")
                ->where("user_service.user_id = :user_id")
                ->andWhere("user_service.service_id = :service_id")
                ->setParameter("user_id", $user_id)
                ->setParameter("service_id", $service_id)
                ->getQuery()
                ->getResult()
            ;
       }

    //    /**
    //     * @return UserService[] Returns an array of UserService objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?UserService
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    }
?>