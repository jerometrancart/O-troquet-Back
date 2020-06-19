<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }


    public function findStatUser($id)
    {

        // je crée un querybuilder sur l'objet User avec l'alias 'user'
        $builder = $this->createQueryBuilder('user');
        // je met ma condition de recherche
        $builder->where("user.id = :id");
        // J'ajoute la valeur du parametre utilisé dans ma condition
        $builder->setParameter('id', $id);
        // je crée une jointure avec la table play
        $builder->leftJoin('user.plays', 'play');
        // J'ajoute les stats au select pour que doctrine alimente les objets associés
        $builder->addSelect('play');



        // j'execute la requete
        $query = $builder->getQuery();
        // je recupére le resultat non pas sous la forme d'un tableau mais un ou 0 objets
        $result = $query->getScalarResult();

        return $result;
    }
    // /**
    //  * @return User[] Returns an array of User objects
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
    public function findOneBySomeField($value): ?User
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
