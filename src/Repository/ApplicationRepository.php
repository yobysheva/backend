<?php

namespace App\Repository;

use App\Entity\Application;
use App\Entity\User;
use App\Entity\House;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Application>
 */
class ApplicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Application::class);
    }

    public function save(Application $application, bool $flush = true): void
    {
        $em = $this->getEntityManager();
        $em->persist($application);

        if ($flush) {
            $em->flush();
        }
    }

    public function remove(Application $application, bool $flush = true): void
    {
        $em = $this->getEntityManager();
        $em->remove($application);

        if ($flush) {
            $em->flush();
        }
    }

    public function createForUserAndHouse(User $user, House $house, bool $flush = true): Application
    {
        $application = new Application();
        $application->setApplicant($user);
        $application->setWantedHouse($house);

        $user->setCurrentHouse($house);
        $house->setUserForHouse($user);

        $em = $this->getEntityManager();

        $em->persist($application);
        $em->persist($user);
        $em->persist($house);

        if ($flush) {
            $em->flush();
        }

        return $application;
    }

    //    /**
    //     * @return Request[] Returns an array of Request objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Request
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
