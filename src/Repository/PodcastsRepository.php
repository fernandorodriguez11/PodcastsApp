<?php

namespace App\Repository;

use App\Entity\Podcasts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Podcasts>
 *
 * @method Podcasts|null find($id, $lockMode = null, $lockVersion = null)
 * @method Podcasts|null findOneBy(array $criteria, array $orderBy = null)
 * @method Podcasts[]    findAll()
 * @method Podcasts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PodcastsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Podcasts::class);
    }

    public function add(Podcasts $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Podcasts $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function allPodcasts(){
        
        return $this->getEntityManager()
            ->createQuery('
                Select podcast.id, podcast.titulo, podcast.descripcion, podcast.fechaSubida, podcast.imagen, podcast.audio, 
                user.nombre, user.id as usuario
                From App:Podcasts podcast 
                Join podcast.autor user');

    }

    public function podcastUsuario($id){
        
        return $this->getEntityManager()
            ->createQuery('
                Select podcast.id, podcast.titulo, podcast.descripcion, podcast.fechaSubida, podcast.imagen, podcast.audio, 
                user.nombre, user.id as usuario, user.apellidos, user.email, user.password, user.roles
                From App:Podcasts podcast 
                Join podcast.autor user
                where podcast.id = :i')
            ->setParameter('i', $id)
            ->getResult();

    }

    public function eliminarPodcast($id){
        
        return $this->getEntityManager()
            ->createQuery('
                Select user.id as usuario, podcast.id, podcast.titulo
                From App:Podcasts podcast 
                Join podcast.autor user
                where podcast.id = :i')
            ->setParameter('i', $id)
            ->getResult();

    }
    /*public function todo(){
        
        return $this->getEntityManager()
            ->createQuery('
                Select podcast.id, podcast.titulo, podcast.descripcion, podcast.fechaSubida, podcast.imagen, podcast.audio, 
                user.nombre, user.id as usuario, user.apellidos, user.roles, user.password, user.email
                From App:Podcasts podcast 
                left Join podcast.autor user
                order by user.nombre ASC')
            ->getResult();

    }*/

//    /**
//     * @return Podcasts[] Returns an array of Podcasts objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Podcasts
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
