<?php

namespace App\Entity;

use App\Repository\RessourceRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RessourceRepository::class)
 */
class Ressource
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity=RessourceAttribute::class, mappedBy="ressource")
     */
    private $ressourceAttributes;

    /**
     * @ORM\OneToMany(targetEntity=Proposal::class, mappedBy="ressource")
     */
    private $proposals;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="ressources")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\Column(type="string")
     */
    private $ressource_picture;

    public function __construct()
    {
        $this->ressourceAttributes = new ArrayCollection();
        $this->proposals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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
            $ressourceAttribute->addRessource($this);
        }

        return $this;
    }

    public function removeRessourceAttribute(RessourceAttribute $ressourceAttribute): self
    {
        if ($this->ressourceAttributes->removeElement($ressourceAttribute)) {
            $ressourceAttribute->removeRessource($this);
        }

        return $this;
    }

    public function getRessourcePicture()
    {
        return $this->ressource_picture;
    }

    public function setRessourcePicture($ressource_picture)
    {
        $this->ressource_picture = $ressource_picture;

        return $this;
    }

    /**
     * @return Collection|Proposal[]
     */
    public function getProposals(): Collection
    {
        return $this->proposals;
    }

    public function addProposal(Proposal $proposal): self
    {
        if (!$this->proposals->contains($proposal)) {
            $this->proposals[] = $proposal;
            $proposal->setRessource($this);
        }

        return $this;
    }

    public function removeProposal(Proposal $proposal): self
    {
        if ($this->proposals->removeElement($proposal)) {
            // set the owning side to null (unless already changed)
            if ($proposal->getRessource() === $this) {
                $proposal->setRessource(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getSlug():string
    {
        return $slugify = (new Slugify())->slugify($this->id);
    }

    public function __toString()
    {
        return $this->description;
    }

}
