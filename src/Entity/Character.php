<?php

namespace App\Entity;

use App\Repository\CharacterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CharacterRepository::class)]
#[ORM\Table(name: 'got_character', schema: 'simpsons')]
class Character
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name:"age")]
    private ?int $age = null;

    #[ORM\Column(name:"birthdate", type: Types::DATE_MUTABLE)]
    private ?\DateTime $birthdate = null;

    #[ORM\Column(name:"gender", length: 255)]
    private ?string $gender = null;

    #[ORM\Column(name:"name", length: 255)]
    private ?string $name = null;

    #[ORM\Column(name:"occupation", length: 255)]
    private ?string $occupation = null;

    #[ORM\Column(name:"portrait_path", length: 800)]
    private ?string $portrait_path = null;

    #[ORM\Column(name: "status", length: 255)]
    private ?string $status = null;

    #[ORM\Column(name:"code")]
    private ?int $code = null;

    /**
     * @var Collection<int, Phrases>
     */
    #[ORM\OneToMany(
        mappedBy: 'character',
        targetEntity: Phrases::class,
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private Collection $phrases;

    #[ORM\OneToMany(mappedBy: 'character', targetEntity: Review::class, orphanRemoval: true)]
    private Collection $reviews;

    public function __construct()
    {
        $this->phrases = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getBirthdate(): ?\DateTime
    {
        return $this->birthdate;
    }

    public function setBirthdate(?\DateTime $birthdate): static
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): static
    {
        $this->gender = $gender;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getOccupation(): ?string
    {
        return $this->occupation;
    }

    public function setOccupation(string $occupation): static
    {
        $this->occupation = $occupation;

        return $this;
    }

    public function getPortraitPath(): ?string
    {
        return $this->portrait_path;
    }

    public function setPortraitPath(string $portrait_path): static
    {
        $this->portrait_path = $portrait_path;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(int $code): static
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection<int, Phrases>
     */
    public function getPhrases(): Collection
    {
        return $this->phrases;
    }

    public function addPhrase(Phrases $phrase): static
    {
        if (!$this->phrases->contains($phrase)) {
            $this->phrases->add($phrase);
            $phrase->setCharacter($this);
        }

        return $this;
    }

    public function removePhrase(Phrases $phrase): static
    {
        if ($this->phrases->removeElement($phrase)) {
            if ($phrase->getCharacter() === $this) {
                $phrase->setCharacter(null);
            }
        }

        return $this;
    }

    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setCharacter($this);
        }
        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            if ($review->getCharacter() === $this) {
                $review->setCharacter(null);
            }
        }
        return $this;
    }
}
