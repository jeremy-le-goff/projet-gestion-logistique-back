<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProductsRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=ProductsRepository::class)
 */
class Products
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"products"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotNull
     * @Groups({"products"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=300, nullable=true)
     * @Groups({"products"})
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"products"})
     */
    private $picture;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\NotNull
     * @Groups({"products"})
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotNull
     * @Groups({"products"})
     */
    private $conservationType;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotNull
     * @Groups({"products"})
     */
    private $weight;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotNull
     * @Groups({"products"})
     */
    private $conditioning;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"products", "update_quantity"})
     */
    private $quantity;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"products"})
     */
    private $expirationDate;

    /**
     * @ORM\Column(type="string", length=13, nullable=true)
     * @Groups({"products"})
     */
    private $ean13;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Assert\NotNull
     * @Groups({"products"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Groups({"products"})
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Structures::class, inversedBy="products")
     * 
     */
    private $structures;

    /**
     * @ORM\ManyToOne(targetEntity=Categories::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"products"})
     */
    private $categories;

    /**
     * @ORM\ManyToOne(targetEntity=Brands::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"products"})
     */
    private $brands;

    public function __construct()
    {
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getConservationType(): ?string
    {
        return $this->conservationType;
    }

    public function setConservationType(string $conservationType): self
    {
        $this->conservationType = $conservationType;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getConditioning(): ?string
    {
        return $this->conditioning;
    }

    public function setConditioning(string $conditioning): self
    {
        $this->conditioning = $conditioning;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getStructures(): ?Structures
    {
        return $this->structures;
    }

    public function setStructures(?Structures $structures): self
    {
        $this->structures = $structures;

        return $this;
    }

    public function getCategories(): ?Categories
    {
        return $this->categories;
    }

    public function setCategories(?Categories $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    public function getBrands(): ?Brands
    {
        return $this->brands;
    }

    public function setBrands(?Brands $brands): self
    {
        $this->brands = $brands;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(?\DateTimeInterface $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function getean13(): ?string
    {
        return $this->ean13;
    }

    public function setean13(?string $ean13): self
    {
        $this->ean13 = $ean13;

        return $this;
    }
}
