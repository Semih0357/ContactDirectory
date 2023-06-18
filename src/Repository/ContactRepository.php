<?php

namespace App\Repository;
use App\Entity\User;
use App\Entity\Contact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @extends ServiceEntityRepository<Contact>
 *
 * @method Contact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contact|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contact[]    findAll()
 * @method Contact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    public function save(Contact $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Contact $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

      /**
       * @return Contact[] Returns an array of Contact objects
       */
        public function findByUser($value): array
      {
          return $this->createQueryBuilder('c')
              ->andWhere('c.user = :val')
              ->setParameter('val', $value)
              ->orderBy('c.id', 'ASC')
              ->setMaxResults(10)
              ->getQuery()
              ->getResult()
          ;
      }

//    public function findOneBySomeField($value): ?Contact
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     * @return ArrayCollection Returns an array of Task objects
     */
    public function findByContactOf(User $user) {

        $contacts_created = new ArrayCollection($this->findByCreatedBy($user->getId()));

        return $contacts_created;
    }

    public function findContactById(int $id): ?Contact
    {
    return $this->createQueryBuilder('c')
        ->andWhere('c.id = :id')
        ->setParameter('id', $id)
        ->getQuery()
        ->getOneOrNullResult();
    }

    public function findByFilters(array $filters): array
    {
        $qb = $this->createQueryBuilder('c');
        if (isset($filters['firstname'])) {
            $qb->andWhere('c.firstname = :firstname')
                ->setParameter('firstname', $filters['firstname']);
        }

        if (isset($filters['lastname'])) {
            $qb->andWhere('c.lastname = :lastname')
                ->setParameter('lastname', $filters['lastname']);
        }

        if (isset($filters['phoneNumber'])) {
            $qb->andWhere('c.phoneNumber = :phoneNumber')
                ->setParameter('phoneNumber', $filters['phoneNumber']);
        }
        $query = $qb->getQuery();

        return $query->getResult();
    }

}
