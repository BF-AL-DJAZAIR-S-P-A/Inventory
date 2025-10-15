<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $codeArticle = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $codeBarre = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    private ?string $unite = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $prixAchat = null;

    // 🔹 Ce stock global peut être utile à titre indicatif (somme des stocks par entrepôt)
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $stockActuel = "0";

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $stockMinimum = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $datePeremption = null;

    #[ORM\Column]
    private ?bool $actif = true;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    private ?Categorie $categorie = null;

    #[ORM\Column(nullable: true)]
    private ?float $poids = null;

    #[ORM\OneToMany(targetEntity: InventaireItem::class, mappedBy: 'produit')]
    private Collection $inventaireItems;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: ProduitEntrepot::class, cascade: ['persist', 'remove'])]
    private Collection $produitEntrepots;

    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    public function __construct()
    {
        $this->actif = true;
        //$this->codeArticle = uniqid("PRD-");
        $this->inventaireItems = new ArrayCollection();
        $this->produitEntrepots = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    // --- GETTERS & SETTERS ---

    public function getId(): ?int { return $this->id; }

    public function getCodeArticle(): ?string { return $this->codeArticle; }
    public function setCodeArticle(?string $codeArticle): static { $this->codeArticle = $codeArticle; return $this; }

    public function getCodeBarre(): ?string { return $this->codeBarre; }
    public function setCodeBarre(?string $codeBarre): static { $this->codeBarre = $codeBarre; return $this; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    public function getUnite(): ?string { return $this->unite; }
    public function setUnite(string $unite): static { $this->unite = $unite; return $this; }

    public function getPrixAchat(): ?string { return $this->prixAchat; }
    public function setPrixAchat(?string $prixAchat): static { $this->prixAchat = $prixAchat; return $this; }

    public function getStockActuel(): ?string { return $this->stockActuel; }
    public function setStockActuel(?string $stockActuel): static { $this->stockActuel = $stockActuel; return $this; }

    public function getStockMinimum(): ?string { return $this->stockMinimum; }
    public function setStockMinimum(?string $stockMinimum): static { $this->stockMinimum = $stockMinimum; return $this; }

    public function getDatePeremption(): ?\DateTime { return $this->datePeremption; }
    public function setDatePeremption(?\DateTime $datePeremption): static { $this->datePeremption = $datePeremption; return $this; }

    public function isActif(): ?bool { return $this->actif; }
    public function setActif(bool $actif): static { $this->actif = $actif; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(?\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }

    public function getCategorie(): ?Categorie { return $this->categorie; }
    public function setCategorie(?Categorie $categorie): static { $this->categorie = $categorie; return $this; }

    public function getPoids(): ?float { return $this->poids; }
    public function setPoids(?float $poids): static { $this->poids = $poids; return $this; }

    // --- InventaireItems ---
    public function getInventaireItems(): Collection { return $this->inventaireItems; }
    public function addInventaireItem(InventaireItem $inventaireItem): static {
        if (!$this->inventaireItems->contains($inventaireItem)) {
            $this->inventaireItems->add($inventaireItem);
            $inventaireItem->setProduit($this);
        }
        return $this;
    }
    public function removeInventaireItem(InventaireItem $inventaireItem): static {
        if ($this->inventaireItems->removeElement($inventaireItem)) {
            if ($inventaireItem->getProduit() === $this) {
                $inventaireItem->setProduit(null);
            }
        }
        return $this;
    }

    // --- ProduitEntrepots ---
    public function getProduitEntrepots(): Collection { return $this->produitEntrepots; }
    public function addProduitEntrepot(ProduitEntrepot $produitEntrepot): static {
        if (!$this->produitEntrepots->contains($produitEntrepot)) {
            $this->produitEntrepots->add($produitEntrepot);
            $produitEntrepot->setProduit($this);
        }
        return $this;
    }
    public function removeProduitEntrepot(ProduitEntrepot $produitEntrepot): static {
        if ($this->produitEntrepots->removeElement($produitEntrepot)) {
            if ($produitEntrepot->getProduit() === $this) {
                $produitEntrepot->setProduit(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->nom ?? 'Produit #' . $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }
}
