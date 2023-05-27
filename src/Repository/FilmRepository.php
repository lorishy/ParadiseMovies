<?php

namespace App\Repository;

use App\Entity\Acteur;
use App\Entity\Film;
use App\Entity\Categorie;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Model\SearchData;

/**
 * @extends ServiceEntityRepository<Film>
 *
 * @method Film|null find($id, $lockMode = null, $lockVersion = null)
 * @method Film|null findOneBy(array $criteria, array $orderBy = null)
 * @method Film[]    findAll()
 * @method Film[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FilmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginatorInterface)
    {
        parent::__construct($registry, Film::class);
        
    }

    public function save(Film $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Film $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    /**
     * Get published films
     *
     * @param int $page
     * @param ?Categorie $categorie
     * @param ?Acteur $acteur
     * 
     * @return PaginationInterface
     */
    public function findFilms(
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
                ->setParameter('category', $categorie);
        }

        $data->getQuery()
            ->getResult();

        $films = $this->paginatorInterface->paginate($data, $page, 24);

        return $films;
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

        $films = $this->paginatorInterface->paginate($data, $searchData->page, 24);

        return $films;
    }

}
