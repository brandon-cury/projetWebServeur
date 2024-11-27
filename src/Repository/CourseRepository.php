<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Course;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Course>
 */
class CourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Course::class);
    }

//    /**
//     * @return Course[] Returns an array of Course objects
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

//    public function findOneBySomeField($value): ?Course
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function paginateCourse(int $page, int $limit, Category $category = null) : PaginationInterface
    {
        $query = $this->createQueryBuilder('r')
            ->where('r.is_published = true');
        if(!empty($category)){
            $query->andWhere('r.category = :category')
                ->setParameter('category', $category);
        }
        $query->orderBy('r.created_at', 'DESC');
        return $this->paginator->paginate($query, $page, $limit
        );
    }

    public function findCoursesNotDelete(): array
    {
        return $this->createQueryBuilder('c')
                    ->andWhere('c.name IS NOT NULL')
                    ->orderBy('c.created_at', 'DESC')
                    ->getQuery()
                    ->getResult();
    }

    public function isUserEnrolledInCourse(User $user, Course $course): bool
    {
        $qb = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->innerJoin('c.users', 'u')
            ->where('c.id = :courseId')
            ->andWhere('u.id = :userId')
            ->setParameter('courseId', $course->getId())
            ->setParameter('userId', $user->getId());
        $count = $qb->getQuery()->getSingleScalarResult();
        return $count > 0;
    }
    public function getCoursesWithUserCount() {
        $qb = $this->createQueryBuilder('c')
            ->select('c', 'COUNT(u.id) as user_count')
            ->innerJoin('c.users', 'u')
            ->groupBy('c.id')
            ->having('user_count > 0');
        // Assurez-vous de n'obtenir que les cours avec des utilisateurs inscrits
        return $qb->getQuery()->getResult();
    }
}
