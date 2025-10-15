<?php

namespace App\Entity;

use App\Repository\InventaireHistoriqueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InventaireHistoriqueRepository::class)]
#[ORM\HasLifecycleCallbacks]
class InventaireHistorique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // 🔒 On garde les historiques même si l'inventaire est supprimé (traçabilité)
    #[ORM\ManyToOne(inversedBy: 'inventaireHistoriques')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Inventaire $inventaire = null;

    // 🔒 Même logique ici : si l'utilisateur est supprimé, on garde l’historique
    #[ORM\ManyToOne(inversedBy: 'inventaireHistoriques')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $utilisateur = null;

    // Exemple : "Création", "Validation", "Application", "Annulation"
    #[ORM\Column(length: 255)]
    private ?string $action = null;

    // Détails supplémentaires sur l’action
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $dateAction = null;

    public function __construct()
    {
        // Par défaut, on enregistre la date au moment de la création
        $this->dateAction = new \DateTimeImmutable();
    }

    // 🔹 Lifecycle pour s’assurer que la date est toujours remplie
    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        if (!$this->dateAction) {
            $this->dateAction = new \DateTimeImmutable();
        }
    }

    // --- Getters / Setters ---

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUtilisateur(): ?User
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?User $utilisateur): static
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): static
    {
        $this->action = $action;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;
        return $this;
    }

    public function getDateAction(): ?\DateTimeImmutable
    {
        return $this->dateAction;
    }

    public function setDateAction(\DateTimeImmutable $dateAction): static
    {
        $this->dateAction = $dateAction;
        return $this;
    }

    public function __toString(): string
    {
        return sprintf(
            '[%s] %s par %s',
            $this->dateAction?->format('Y-m-d H:i:s') ?? 'N/A',
            $this->action ?? 'Action inconnue',
            $this->utilisateur?->getNomComplet() ?? 'Utilisateur supprimé'
        );
    }
}
