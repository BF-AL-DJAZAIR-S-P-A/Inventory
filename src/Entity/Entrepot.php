<?php

namespace App\Entity;

use App\Repository\EntrepotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntrepotRepository::class)]
class Entrepot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    // 🔹 Relation ManyToMany avec User (inverse side)
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'entrepots')]
    private Collection $utilisateurs;

    // 🔹 Relation OneToMany avec ProduitEntrepot
    #[ORM\OneToMany(mappedBy: 'entrepot', targetEntity: ProduitEntrepot::class, cascade: ['persist', 'remove'])]
    private Collection $produitEntrepots;

    public function __construct()
    {
        $this->utilisateurs = new ArrayCollection();
        $this->produitEntrepots = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUtilisateurs(): Collection
    {
        return $this->utilisateurs;
    }

    public function addUtilisateur(User $user): static
    {
        if (!$this->utilisateurs->contains($user)) {
            $this->utilisateurs->add($user);
            $user->addEntrepot($this);
        }
        return $this;
    }

    public function removeUtilisateur(User $user): static
    {
        if ($this->utilisateurs->removeElement($user)) {
            $user->removeEntrepot($this);
        }
        return $this;
    }

    /**
     * @return Collection<int, ProduitEntrepot>
     */
    public function getProduitEntrepots(): Collection
    {
        return $this->produitEntrepots;
    }

    public function addProduitEntrepot(ProduitEntrepot $produitEntrepot): static
    {
        if (!$this->produitEntrepots->contains($produitEntrepot)) {
            $this->produitEntrepots->add($produitEntrepot);
            $produitEntrepot->setEntrepot($this);
        }
        return $this;
    }

    public function removeProduitEntrepot(ProduitEntrepot $produitEntrepot): static
    {
        if ($this->produitEntrepots->removeElement($produitEntrepot)) {
            if ($produitEntrepot->getEntrepot() === $this) {
                $produitEntrepot->setEntrepot(null);
            }
        }
        return $this;
    }
}
