<?php

namespace App\Entity;

use App\Repository\RankingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RankingRepository::class)]
#[ORM\Table(name: 'ranking', schema: 'simpsons')]
class Ranking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id', nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'id_category', referencedColumnName: 'id', nullable: false)]
    private ?Category $category = null;

    #[ORM\OneToMany(
        mappedBy: 'ranking',
        targetEntity: RankingCharacter::class,
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private Collection $rankingCharacters;

    public function __construct()
    {
        $this->rankingCharacters = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getRankingCharacters(): Collection
    {
        return $this->rankingCharacters;
    }

    public function addRankingCharacter(RankingCharacter $rc): self
    {
        if (!$this->rankingCharacters->contains($rc)) {
            $this->rankingCharacters->add($rc);
            $rc->setRanking($this);
        }

        return $this;
    }
}
