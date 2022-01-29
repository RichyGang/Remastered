<?php

namespace App\Entity;

use App\Repository\ProposalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProposalRepository::class)
 */
class Proposal
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $location;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $start;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deadline;

    /**
     * @ORM\Column(type="boolean")
     */
    private $offerorneed;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $create_at;


    /**
     * @ORM\Column(type="boolean")
     */
    private $done;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="proposals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity=Ressource::class, inversedBy="proposals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ressource;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="proposals_asked")
     */
    private $askers;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $proposal_picture;

    public function __construct()
    {
        $this->askers = new ArrayCollection();

        $this->create_at = new \DateTimeImmutable();
        $this->start=new \DateTime();
        $this->deadline = new \DateTime();
        $this->proposals_linked = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(?\DateTimeInterface $deadline): self
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function getOfferorneed(): ?bool
    {
        return $this->offerorneed;
    }

    public function setOfferorneed(bool $offerorneed): self
    {
        $this->offerorneed = $offerorneed;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->create_at;
    }

    public function setCreateAt(\DateTimeImmutable $create_at): self
    {
        $this->create_at = $create_at;

        return $this;
    }

    public function getDone(): ?bool
    {
        return $this->done;
    }

    public function setDone(bool $done): self
    {
        $this->done = $done;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getRessource(): ?Ressource
    {
        return $this->ressource;
    }

    public function setRessource(?Ressource $ressource): self
    {
        $this->ressource = $ressource;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getAskers(): Collection
    {
        return $this->askers;
    }

    public function addAsker(User $asker): self
    {
        if (!$this->askers->contains($asker)) {
            $this->askers[] = $asker;
        }

        return $this;
    }

    public function removeAsker(User $asker): self
    {
        $this->askers->removeElement($asker);

        return $this;
    }

    public function getProposalPicture(): ?string
    {
        return $this->proposal_picture;
    }

    public function setProposalPicture(?string $proposal_picture): self
    {
        $this->proposal_picture = $proposal_picture;

        return $this;
    }
}
