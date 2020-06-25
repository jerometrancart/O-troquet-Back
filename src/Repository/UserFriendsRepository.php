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



    public function getFriendship(User $user,  $friend)
    {

      //  dd($user, $friend);

        // je crée un querybuilder sur l'objet User avec l'alias 'user'
        $builder = $this->createQueryBuilder('Friendship');
        // je met ma condition de recherche
        $builder->where("Friendship.user = :user");
        $builder->andWhere("Friendship.friend = :friend");
        // J'ajoute la valeur du parametre utilisé dans ma condition
        $builder->setParameter('user', $user->getId());
        $builder->setParameter('friend', $friend);
        // je crée une jointure avec la table play
        // $builder->leftJoin('user.plays', 'play');
        // // J'ajoute les stats au select pour que doctrine alimente les objets associés
        // $builder->addSelect('play');

        
        // j'execute la requete
        $query = $builder->getQuery();
       // dd($query);
        // je recupére le resultat non pas sous la forme d'un tableau mais un ou 0 objets
        $result = $query->getSingleResult();
        return $result;
    }


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



        /* 
            SELECT u.id, u.username , f.is_accepted, f.is_contested  ,us.id , us.username
            FROM user_friends as  f
            JOIN user as u ON (f.user_id = u.id)
            JOIN user as us ON (f.friend_id = us.id )
            WHERE user_id = 68
 */
        /*


         SELECT f
        FROM App\Entity\UserFriends as f
        WHERE f.user = :user
        
        SELECT u.id, u.username , f.is_accepted, f.is_contested  ,us.id , us.username
FROM user_friends as  f
JOIN user as u ON (f.user_id = u.id)
JOIN user as us ON (f.friend_id = us.id )
WHERE user_id = 68

        SELECT u.username
        FROM App\Entity\UserFriends as f
        INNER JOIN App\Entity\User as u 
        ON (f.user.id = u.id)
        WHERE user = :user

            SELECT u.username
            FROM App\Entity\UserFriends f
            INNER JOIN App\Entity\User u ON (f.user.id = u.id)
            WHERE user = :user

            SELECT u.username
FROM App\Entity\UserFriends as  f
INNER JOIN App\Entity\User as u ON (f.user_id = u.id)
WHERE user_id = user

            // Requete si problematique circular reference

            SELECT u1.username
            FROM App\Entity\User as u1
            WHERE u1.id  = :user

            AND u.friend IN (
                SELECT uf2.id
                FROM App\Entity\UserFriends uf2
                WHERE uf2.user = :user
            )
        
        */
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
