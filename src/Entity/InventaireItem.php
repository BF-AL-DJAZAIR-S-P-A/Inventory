<?php

namespace App\Entity;

use App\Repository\InventaireItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InventaireItemRepository::class)]
#[ORM\HasLifecycleCallbacks]
class InventaireItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // 🔹 Quantité attendue selon le système (avant inventaire)
    #[ORM\Column]
    private ?float $quantiteTheorique = null;

    // 🔹 Quantité réellement comptée lors de l’inventaire
    #[ORM\Column(nullable: true)]
    private ?float $quantiteReelle = null;

    // 🔹 Différence calculée automatiquement
    #[ORM\Column(nullable: true)]
    private ?float $ecart = null;

    // ✅ Relation avec Inventaire
    #[ORM\ManyToOne(inversedBy: 'inventaireItems')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private ?Inventaire $inventaire = null;

    // ✅ Relation avec Produit
    #[ORM\ManyToOne(inversedBy: 'inventaireItems')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private ?Produit $produit = null;

    // ✅ Entrepôt concerné (lié automatiquement via ProduitEntrepot)
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Entrepot $entrepot = null;

    // ✅ Lien direct avec ProduitEntrepot (stock réel dans un entrepôt)
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?ProduitEntrepot $produitEntrepot = null;

    // ✅ Optionnel : utilisateur ayant saisi la ligne (traçabilité)
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $saisiPar = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    // ------------------------------------------------------------
    // 🔹 Gestion automatique des dates
    // ------------------------------------------------------------
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

    // ------------------------------------------------------------
    // 🔹 Getters / Setters
    // ------------------------------------------------------------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantiteTheorique(): ?float
    {
        return $this->quantiteTheorique;
    }

    public function setQuantiteTheorique(float $quantiteTheorique): static
    {
        $this->quantiteTheorique = $quantiteTheorique;
        $this->updateEcart();
        return $this;
    }

    public function getQuantiteReelle(): ?float
    {
        return $this->quantiteReelle;
    }

    public function setQuantiteReelle(?float $quantiteReelle): static
    {
        $this->quantiteReelle = $quantiteReelle;
        $this->updateEcart();
        return $this;
    }

    public function getEcart(): ?float
    {
        return $this->ecart;
    }

    private function updateEcart(): void
    {
        if ($this->quantiteTheorique !== null && $this->quantiteReelle !== null) {
            $this->ecart = round($this->quantiteReelle - $this->quantiteTheorique, 2);
        }
    }

    public function getInventaire(): ?Inventaire
    {
        return $this->inventaire;
    }

    public function setInventaire(?Inventaire $inventaire): static
    {
        $this->inventaire = $inventaire;
        return $this;
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

    public function getProduitEntrepot(): ?ProduitEntrepot
    {
        return $this->produitEntrepot;
    }

    public function setProduitEntrepot(?ProduitEntrepot $produitEntrepot): static
    {
        $this->produitEntrepot = $produitEntrepot;

        if ($produitEntrepot) {
            // ⚙️ Définir automatiquement la quantité théorique et l’entrepôt associé
            $this->quantiteTheorique = $produitEntrepot->getQuantite() ?? 0;
            $this->entrepot = $produitEntrepot->getEntrepot();
        }

        return $this;
    }

    public function getSaisiPar(): ?User
    {
        return $this->saisiPar;
    }

    public function setSaisiPar(?User $saisiPar): static
    {
        $this->saisiPar = $saisiPar;
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

    // ------------------------------------------------------------
    // 🔹 Utilitaire pour affichage
    // ------------------------------------------------------------
    public function __toString(): string
    {
        return sprintf(
            '%s - %s (%s) : %.2f / %.2f',
            $this->produit?->getNom() ?? 'Produit inconnu',
            $this->entrepot?->getNom() ?? 'Entrepôt inconnu',
            $this->inventaire?->getNom() ?? 'Inventaire inconnu',
            $this->quantiteReelle ?? 0,
            $this->quantiteTheorique ?? 0
        );
    }
}
