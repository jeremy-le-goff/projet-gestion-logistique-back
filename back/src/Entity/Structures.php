<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\StructuresRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=StructuresRepository::class)
 */
class Structures
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"structures", "identifiedUser","structuresByOrganization"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotNull
     * @Groups({"structures", "identifiedUser","structuresByOrganization"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=14, nullable=true)
     * @Groups({"structures","structuresByOrganization"})
     */
    private $siret;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull
     * @Groups({"structures","structuresByOrganization"})
     */
    private $status;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"structures"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Groups({"structures","structuresByOrganization"})
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Organizations::class, inversedBy="structures", cascade={"persist"})
     * @Groups({"structures"})
     */
    private $organizations;


    /**
     * @ORM\OneToMany(targetEntity=Products::class, mappedBy="structures")
     * @Groups({"structures","products"})
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="structures")
     * @Groups({"structuresUser","structures"})
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->products = new ArrayCollection();

        // Init createdAt
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): self
    {
        $this->siret = $siret;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @Groups({"structures"})
     */
    public function getOrganizations(): ?Organizations
    {
        return $this->organizations;
    }

    /**
     * @Groups({"structures"})
     */
    public function setOrganizations(?Organizations $organizations): self
    {
        $this->organizations = $organizations;

        return $this;
    }

    /**
     * @return Collection<int, Products>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Products $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setStructures($this);
        }

        return $this;
    }

    public function removeProduct(Products $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getStructures() === $this) {
                $product->setStructures(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setStructures($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getStructures() === $this) {
                $user->setStructures(null);
            }
        }

        return $this;
    }
}
