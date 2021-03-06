<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\DriverManager;

use App\Entity\Job;

/**
 * @method Job|null find($id, $lockMode = null, $lockVersion = null)
 * @method Job|null findOneBy(array $criteria, array $orderBy = null)
 * @method Job[]    findAll()
 * @method Job[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Job::class);
    }

    public function findAllJobsPossibilitiesToDelete(int $page)
    {
        $value = ($page - 1) * 10;
        $sql = ('SELECT *, (SELECT COUNT(*) FROM employee WHERE job_id = Job.id) as numberJobUse
          FROM JOB LIMIT 10 OFFSET '.$value);

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $result = $stmt->executeQuery();
        $jobs = $result->fetchAllAssociative();
        return $jobs;
    }

    public function countJobs()
    {
        $queryb = $this->createQueryBuilder('j')
            ->select('count(j)');
        return $queryb->getQuery()->getOneOrNullResult();
    }
    
}
