<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserFriends;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserFriends|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserFriends|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserFriends[]    findAll()
 * @method UserFriends[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserFriendsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserFriends::class);
    }



   
    // Know if a relationship exists
    public function getFriendship($user, $friend)
    {
        
        // je crée un querybuilder sur l'objet User avec l'alias 'user'
        $builder = $this->createQueryBuilder('Friendship');
     
        $builder->where("Friendship.user = :user");
        $builder->andWhere("Friendship.friend = :friend");
        $builder->setParameter('user', $user);
        $builder->setParameter('friend', $friend);
        $query = $builder->getQuery();
      
        $result = $query->getOneOrNullResult();
        return $result;
    }

    //Get Friendship (avoid circular reference)
    public function getFriend(User $user)
    {
        $em = $this->getEntityManager();
        $query =  $em->createQuery('
        SELECT f, u, us
        FROM App\Entity\UserFriends as  f
        JOIN f.user as u
        JOIN f.friend as us
        WHERE u.id = :user
        ')
            ->setParameter('user', $user->getId());

        return $result = $query->getResult();
    }

    // /**
    //  * @return UserFriends[] Returns an array of UserFriends objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserFriends
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
