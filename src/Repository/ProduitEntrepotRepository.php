<?php

namespace App\Repository;

use App\Entity\ProduitEntrepot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProduitEntrepot>
 *
 * @method ProduitEntrepot|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProduitEntrepot|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProduitEntrepot[]    findAll()
 * @method ProduitEntrepot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitEntrepotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProduitEntrepot::class);
    }

    /**
     * 🔍 Trouve le stock d’un produit spécifique dans un entrepôt donné.
     */
    public function findOneByProduitEtEntrepot(int $produitId, int $entrepotId): ?ProduitEntrepot
    {
        return $this->createQueryBuilder('pe')
            ->andWhere('pe.produit = :produit')
            ->andWhere('pe.entrepot = :entrepot')
            ->setParameter('produit', $produitId)
            ->setParameter('entrepot', $entrepotId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * 📊 Retourne la liste des produits avec stock inférieur au stock minimum.
     */
    public function findProduitsSousSeuil(): array
    {
        return $this->createQueryBuilder('pe')
            ->andWhere('pe.stockMinimum IS NOT NULL')
            ->andWhere('pe.quantite < pe.stockMinimum')
            ->andWhere('pe.actif = true')
            ->orderBy('pe.entrepot', 'ASC')
            ->addOrderBy('pe.produit', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 💡 Exemple : total du stock d’un produit sur tous les entrepôts.
     */
    public function getStockTotalParProduit(int $produitId): float
    {
        return (float) $this->createQueryBuilder('pe')
            ->select('SUM(pe.quantite)')
            ->andWhere('pe.produit = :produit')
            ->setParameter('produit', $produitId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
