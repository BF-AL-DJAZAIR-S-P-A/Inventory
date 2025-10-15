<?php

namespace App\Entity;

use App\Repository\ProduitEntrepotRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use App\Entity\User;

#[ORM\Entity(repositoryClass: ProduitEntrepotRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ProduitEntrepot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Produit::class, inversedBy: 'produitEntrepots')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produit $produit = null;

    #[ORM\ManyToOne(targetEntity: Entrepot::class, inversedBy: 'produitEntrepots')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Entrepot $entrepot = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $quantite = "0";

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $stockMinimum = null;

    #[ORM\Column(type: 'boolean')]
    private bool $actif = true;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null; // 🟢 soft delete

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $utilisateur = null;

    public function __construct()
    {
        $this->quantite = "0";
        $this->actif = true;
    }

    // ------------------------
    // 🔹 Gestion automatique des dates
    // ------------------------

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    // ------------------------
    // Getters / Setters
    // ------------------------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): static
    {
        $this->produit = $produit;
        return $this;
    }

    public function getEntrepot(): ?Entrepot
    {
        return $this->entrepot;
    }

    public function setEntrepot(?Entrepot $entrepot): static
    {
        $this->entrepot = $entrepot;
        return $this;
    }

    public function getQuantite(): float
    {
        return (float) $this->quantite;
    }

    public function setQuantite(float $quantite): static
    {
        $this->quantite = (string) $quantite;
        return $this;
    }

    public function getStockMinimum(): ?float
    {
        return $this->stockMinimum !== null ? (float) $this->stockMinimum : null;
    }

    public function setStockMinimum(?float $stockMinimum): static
    {
        $this->stockMinimum = $stockMinimum !== null ? (string) $stockMinimum : null;
        return $this;
    }

    public function isActif(): bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): static
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    public function getUtilisateur(): ?User
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?User $utilisateur): static
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    // ------------------------
    // Méthodes utilitaires
    // ------------------------

    /** 🔹 Ajuste la quantité (ajout ou retrait) */
    public function ajusterQuantite(float $delta): static
    {
        $nouvelleQuantite = (float) $this->quantite + $delta;
        if ($nouvelleQuantite < 0) {
            $nouvelleQuantite = 0;
        }
        $this->quantite = (string) $nouvelleQuantite;
        return $this;
    }

    /** 🔹 Désactive (soft delete) sans supprimer */
    public function desactiver(): static
    {
        $this->actif = false;
        $this->deletedAt = new \DateTimeImmutable();
        return $this;
    }

    /** 🔹 Réactive si besoin */
    public function reactiver(): static
    {
        $this->actif = true;
        $this->deletedAt = null;
        return $this;
    }

    /** 🔹 Affichage utile dans EasyAdmin ou les listes */
    public function __toString(): string
    {
        return sprintf(
            '%s (%s) - %.2f unités',
            $this->produit?->getNom() ?? 'Produit inconnu',
            $this->entrepot?->getNom() ?? 'Entrepôt inconnu',
            $this->getQuantite()
        );
    }
}
