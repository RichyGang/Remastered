<?php

namespace App\Entity;

use App\Repository\UnityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UnityRepository::class)
 */
class Unity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $degree;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $symbol;

    /**
     * @ORM\ManyToOne(targetEntity=Unity::class, inversedBy="unities")
     */
    private $grandeur;

    /**
     * @ORM\OneToMany(targetEntity=Unity::class, mappedBy="grandeur")
     */
    private $unities;

    /**
     * @ORM\OneToMany(targetEntity=RessourceAttribute::class, mappedBy="unity")
     */
    private $ressourceAttributes;

    /**
     * @ORM\OneToMany(targetEntity=CategoryAttribute::class, mappedBy="unity")
     */
    private $categoryAttributes;

    public function __construct()
    {
        $this->unities = new ArrayCollection();
        $this->ressourceAttributes = new ArrayCollection();
        $this->categoryAttributes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDegree(): ?bool
    {
        return $this->degree;
    }

    public function setDegree(bool $degree): self
    {
        $this->degree = $degree;

        return $this;
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

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): self
    {
        $this->symbol = $symbol;

        return $this;
    }

    public function getGrandeur(): ?self
    {
        return $this->grandeur;
    }

    public function setGrandeur(?self $grandeur): self
    {
        $this->grandeur = $grandeur;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getUnities(): Collection
    {
        return $this->unities;
    }

    public function addUnity(self $unity): self
    {
        if (!$this->unities->contains($unity)) {
            $this->unities[] = $unity;
            $unity->setGrandeur($this);
        }

        return $this;
    }

    public function removeUnity(self $unity): self
    {
        if ($this->unities->removeElement($unity)) {
            // set the owning side to null (unless already changed)
            if ($unity->getGrandeur() === $this) {
                $unity->setGrandeur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|RessourceAttribute[]
     */
    public function getRessourceAttributes(): Collection
    {
        return $this->ressourceAttributes;
    }

    public function addRessourceAttribute(RessourceAttribute $ressourceAttribute): self
    {
        if (!$this->ressourceAttributes->contains($ressourceAttribute)) {
            $this->ressourceAttributes[] = $ressourceAttribute;
            $ressourceAttribute->setUnity($this);
        }

        return $this;
    }

    public function removeRessourceAttribute(RessourceAttribute $ressourceAttribute): self
    {
        if ($this->ressourceAttributes->removeElement($ressourceAttribute)) {
            // set the owning side to null (unless already changed)
            if ($ressourceAttribute->getUnity() === $this) {
                $ressourceAttribute->setUnity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CategoryAttribute[]
     */
    public function getCategoryAttributes(): Collection
    {
        return $this->categoryAttributes;
    }

    public function addCategoryAttribute(CategoryAttribute $categoryAttribute): self
    {
        if (!$this->categoryAttributes->contains($categoryAttribute)) {
            $this->categoryAttributes[] = $categoryAttribute;
            $categoryAttribute->setUnity($this);
        }

        return $this;
    }

    public function removeCategoryAttribute(CategoryAttribute $categoryAttribute): self
    {
        if ($this->categoryAttributes->removeElement($categoryAttribute)) {
            // set the owning side to null (unless already changed)
            if ($categoryAttribute->getUnity() === $this) {
                $categoryAttribute->setUnity(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
