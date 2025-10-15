<?php

namespace App\Entity;

use App\Repository\InventaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InventaireRepository::class)]
class Inventaire
{
    public const STATUT_EN_COURS = 'en_cours';
    public const STATUT_TERMINE = 'terminé';
    public const STATUT_VALIDE = 'validé';
    public const STATUT_ANNULE = 'annulé';
    public const STATUT_APPLIQUE = 'appliqué'; 

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateDebut = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateFin = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateValidation = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = self::STATUT_EN_COURS;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: 'boolean')]
    private bool $applique = false;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $appliquePar = null;

    #[ORM\ManyToOne(targetEntity: Entrepot::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Entrepot $entrepot = null;

    #[ORM\OneToMany(
        targetEntity: InventaireItem::class,
        mappedBy: 'inventaire',
        cascade: ['persist'] // ✅ uniquement persist pour éviter la suppression accidentelle
    )]
    private Collection $inventaireItems;

    #[ORM\OneToMany(
        targetEntity: InventaireHistorique::class,
        mappedBy: 'inventaire',
        cascade: ['persist']
    )]
    private Collection $inventaireHistoriques;

    public function __construct()
    {
        $this->inventaireItems = new ArrayCollection();
        $this->inventaireHistoriques = new ArrayCollection();
        $this->dateDebut = new \DateTimeImmutable();
        $this->statut = self::STATUT_EN_COURS;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebut(): ?\DateTimeImmutable
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeImmutable $dateDebut): self
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeImmutable
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeImmutable $dateFin): self
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    public function getDateValidation(): ?\DateTimeImmutable
    {
        return $this->dateValidation;
    }

    public function setDateValidation(?\DateTimeImmutable $dateValidation): self
    {
        $this->dateValidation = $dateValidation;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function isApplique(): bool
    {
        return $this->applique;
    }

    public function setApplique(bool $applique): self
    {
        $this->applique = $applique;
        return $this;
    }

    public function getAppliquePar(): ?User
    {
        return $this->appliquePar;
    }

    public function setAppliquePar(?User $appliquePar): self
    {
        $this->appliquePar = $appliquePar;
        return $this;
    }

    public function getEntrepot(): ?Entrepot
    {
        return $this->entrepot;
    }

    public function setEntrepot(?Entrepot $entrepot): self
    {
        $this->entrepot = $entrepot;
        return $this;
    }

    public function getInventaireItems(): Collection
    {
        return $this->inventaireItems;
    }

    public function addInventaireItem(InventaireItem $item): self
    {
        if (!$this->inventaireItems->contains($item)) {
            $this->inventaireItems->add($item);
            $item->setInventaire($this);
        }
        return $this;
    }

    public function removeInventaireItem(InventaireItem $item): self
    {
        if ($this->inventaireItems->removeElement($item)) {
            if ($item->getInventaire() === $this) {
                $item->setInventaire(null);
            }
        }
        return $this;
    }

    public function getInventaireHistoriques(): Collection
    {
        return $this->inventaireHistoriques;
    }

    public function addInventaireHistorique(InventaireHistorique $historique): self
    {
        if (!$this->inventaireHistoriques->contains($historique)) {
            $this->inventaireHistoriques->add($historique);
            $historique->setInventaire($this);
        }
        return $this;
    }

    public function removeInventaireHistorique(InventaireHistorique $historique): self
    {
        if ($this->inventaireHistoriques->removeElement($historique)) {
            if ($historique->getInventaire() === $this) {
                $historique->setInventaire(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->nom ?? 'Inventaire #' . $this->id;
    }
}
