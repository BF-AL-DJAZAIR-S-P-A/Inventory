<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $nomComplet = null;

    // OneToMany avec InventaireHistorique
    #[ORM\OneToMany(targetEntity: InventaireHistorique::class, mappedBy: 'utilisateur')]
    private Collection $inventaireHistoriques;

    // ManyToMany avec Entrepot (côté propriétaire)
    #[ORM\ManyToMany(targetEntity: Entrepot::class, inversedBy: 'utilisateurs')]
    #[ORM\JoinTable(name: 'user_entrepot')]
    private Collection $entrepots;

    public function __construct()
    {
        $this->inventaireHistoriques = new ArrayCollection();
        $this->entrepots = new ArrayCollection();
    }

    // === Getters et setters ===
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    #[\Deprecated]
    public function eraseCredentials(): void {}

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function getNomComplet(): ?string
    {
        return $this->nomComplet;
    }

    public function setNomComplet(string $nomComplet): static
    {
        $this->nomComplet = $nomComplet;
        return $this;
    }

    // === InventaireHistoriques ===
    public function getInventaireHistoriques(): Collection
    {
        return $this->inventaireHistoriques;
    }

    public function addInventaireHistorique(InventaireHistorique $inventaireHistorique): static
    {
        if (!$this->inventaireHistoriques->contains($inventaireHistorique)) {
            $this->inventaireHistoriques->add($inventaireHistorique);
            $inventaireHistorique->setUtilisateur($this);
        }
        return $this;
    }

    public function removeInventaireHistorique(InventaireHistorique $inventaireHistorique): static
    {
        if ($this->inventaireHistoriques->removeElement($inventaireHistorique)) {
            if ($inventaireHistorique->getUtilisateur() === $this) {
                $inventaireHistorique->setUtilisateur(null);
            }
        }
        return $this;
    }

    // === Entrepots ===
    public function getEntrepots(): Collection
    {
        return $this->entrepots;
    }

    public function addEntrepot(Entrepot $entrepot): static
    {
        if (!$this->entrepots->contains($entrepot)) {
            $this->entrepots->add($entrepot);
            $entrepot->addUtilisateur($this); // côté inverse
        }
        return $this;
    }

    public function removeEntrepot(Entrepot $entrepot): static
    {
        if ($this->entrepots->removeElement($entrepot)) {
            $entrepot->removeUtilisateur($this); // côté inverse
        }
        return $this;
    }
}
