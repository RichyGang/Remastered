<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="children")
     * @ORM\JoinColumn(nullable=true)
     */
    private $mother;

    /**
     * @ORM\OneToMany(targetEntity=Category::class, mappedBy="mother")
     */
    private $children;

    /**
     * @ORM\ManyToMany(targetEntity=CategoryAttribute::class, mappedBy="category")
     */
    private $categoryAttributes;

    /**
     * @ORM\OneToMany(targetEntity=Ressource::class, mappedBy="category")
     */
    private $ressources;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->categoryAttributes = new ArrayCollection();
        $this->ressources = new ArrayCollection();
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

    public function getMother(): ?self
    {
        return $this->mother;
    }

    public function setMother(?self $mother): self
    {
        $this->mother = $mother;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setMother($this);
        }

        return $this;
    }

    public function removeChild(self $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getMother() === $this) {
                $child->setMother(null);
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
            $categoryAttribute->addCategory($this);
        }

        return $this;
    }

    public function removeCategoryAttribute(CategoryAttribute $categoryAttribute): self
    {
        if ($this->categoryAttributes->removeElement($categoryAttribute)) {
            $categoryAttribute->removeCategory($this);
        }

        return $this;
    }

    /**
     * @return Collection|Ressource[]
     */
    public function getRessources(): Collection
    {
        return $this->ressources;
    }

    public function addRessource(Ressource $ressource): self
    {
        if (!$this->ressources->contains($ressource)) {
            $this->ressources[] = $ressource;
            $ressource->setCategory($this);
        }

        return $this;
    }

    public function removeRessource(Ressource $ressource): self
    {
        if ($this->ressources->removeElement($ressource)) {
            // set the owning side to null (unless already changed)
            if ($ressource->getCategory() === $this) {
                $ressource->setCategory(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

}


