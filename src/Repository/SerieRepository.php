<?php

namespace App\Repository;

use App\Entity\Serie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Acteur;
use App\Entity\Categorie;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use App\Model\SearchData;

/**
 * @extends ServiceEntityRepository<Serie>
 *
 * @method Serie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Serie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Serie[]    findAll()
 * @method Serie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SerieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginatorInterface )
    {
        parent::__construct($registry, Serie::class);
    }

    public function save(Serie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Serie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Get published series
     *
     * @param int $page
     * @param ?Categorie $categorie
     * @param ?Acteur $acteur
     * 
     * @return PaginationInterface
     */
    public function findseries(
        int $page,
        ?Categorie $categorie = null,
    ): PaginationInterface {
        $data = $this->createQueryBuilder('f')
            ->select('f')
            ->addOrderBy('f.createdAt', 'DESC');

        if (isset($categorie)) {
            $data = $data
                ->join('f.categories', 'c')
                ->andWhere(':categorie IN (c)')
                ->setParameter('categorie', $categorie);
        }

        $data->getQuery()
            ->getResult();

        $series = $this->paginatorInterface->paginate($data, $page, 24);

        return $series;
    }


    /**
     * @param SearchData $searchData
     * @return PaginationInterface
     */
    public function findBySearch(SearchData $searchData): PaginationInterface
    {
        $data = $this->createQueryBuilder('f')
            ->select('f')
            ->addOrderBy('f.createdAt', 'DESC');

        if (!empty($searchData->q)) {
            $data = $data
                ->andWhere('f.titre LIKE :q')
                ->orWhere('f.description LIKE :q')
                ->setParameter('q', "%{$searchData->q}%");
        }

        if (!empty($searchData->categorie)) {
            $data = $data
                ->join('f.categorie', 'c')
                ->andWhere('c.id IN (:categorie)')
                ->setParameter('categorie', $searchData->categorie);
        }

        $data = $data
            ->getQuery()
            ->getResult();

        $series = $this->paginatorInterface->paginate($data, $searchData->page, 24);

        return $series;
    }
}
