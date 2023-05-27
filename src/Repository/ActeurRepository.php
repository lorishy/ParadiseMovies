<?php

namespace App\Repository;

use App\Entity\Acteur;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Model\SearchData;

/**
 * @extends ServiceEntityRepository<Acteur>
 *
 * @method Acteur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Acteur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Acteur[]    findAll()
 * @method Acteur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActeurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginatorInterface)
    {
        parent::__construct($registry, Acteur::class);
    }

    public function save(Acteur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Acteur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
   /**
     * Get published acteurs
     *
     * @param int $page
     * @param ?Categorie $categorie
     * @param ?Acteur $acteur
     * 
     * @return PaginationInterface
     */
    public function findActeurs(
        int $page,
    ): PaginationInterface {
        $data = $this->createQueryBuilder('a')
            ->select('a')
            ->addOrderBy('a.createdAt', 'DESC');

        $data->getQuery()
            ->getResult();

        $acteurs = $this->paginatorInterface->paginate($data, $page, 24);

        return $acteurs;
    }


    /**
     * @param SearchData $searchData
     * @return PaginationInterface
     */
    public function findBySearch(SearchData $searchData): PaginationInterface
    {
        $data = $this->createQueryBuilder('a')
            ->select('a')
            ->addOrderBy('a.createdAt', 'DESC');

        if (!empty($searchData->q)) {
            $data = $data
                ->andWhere('a.nom LIKE :q')
                ->orWhere('a.prenom LIKE :q')
                ->orWhere('a.metier LIKE :q')
                ->setParameter('q', "%{$searchData->q}%");
        }

        $data = $data
            ->getQuery()
            ->getResult();

        $acteurs = $this->paginatorInterface->paginate($data, $searchData->page, 12);

        return $acteurs;
    }

}
