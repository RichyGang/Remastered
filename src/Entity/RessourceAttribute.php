<?php

namespace App\Entity;

use App\Repository\RessourceAttributeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RessourceAttributeRepository::class)
 */
class RessourceAttribute
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=CategoryAttribute::class, inversedBy="ressourceAttributes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $categoryAttribute;

    /**
     * @ORM\ManyToMany(targetEntity=Ressource::class, inversedBy="ressourceAttributes")
     */
    private $ressource;

    /**
     * @ORM\ManyToOne(targetEntity=Unity::class, inversedBy="ressourceAttributes")
     */
    private $unity;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $value;

    public function __construct()
    {
        $this->ressourceAttribute = new ArrayCollection();
        $this->ressource = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoryAttribute(): ?CategoryAttribute
    {
        return $this->categoryAttribute;
    }

    public function setCategoryAttribute(?CategoryAttribute $categoryAttribute): self
    {
        $this->categoryAttribute = $categoryAttribute;

        return $this;
    }

    /**
     * @return Collection|Ressource[]
     */
    public function getRessourceAttribute(): Collection
    {
        return $this->ressourceAttribute;
    }

    public function addRessourceAttribute(Ressource $ressourceAttribute): self
    {
        if (!$this->ressourceAttribute->contains($ressourceAttribute)) {
            $this->ressourceAttribute[] = $ressourceAttribute;
        }

        return $this;
    }

    public function removeRessourceAttribute(Ressource $ressourceAttribute): self
    {
        $this->ressourceAttribute->removeElement($ressourceAttribute);

        return $this;
    }

    /**
     * @return Collection|Ressource[]
     */
    public function getRessource(): Collection
    {
        return $this->ressource;
    }

    public function addRessource(Ressource $ressource): self
    {
        if (!$this->ressource->contains($ressource)) {
            $this->ressource[] = $ressource;
        }

        return $this;
    }

    public function removeRessource(Ressource $ressource): self
    {
        $this->ressource->removeElement($ressource);

        return $this;
    }

    public function getUnity(): ?Unity
    {
        return $this->unity;
    }

    public function setUnity(?Unity $unity): self
    {
        $this->unity = $unity;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function __toString()
    {
        return $this->value;
    }
}
