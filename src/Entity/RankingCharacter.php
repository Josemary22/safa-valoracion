<?php

namespace App\Entity;

use App\Repository\RankingCharacterRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RankingCharacterRepository::class)]
#[ORM\Table(name: 'ranking_character', schema: 'simpsons')]
class RankingCharacter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'id_ranking', referencedColumnName: 'id', nullable: false)]
    private ?Ranking $ranking = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'id_character', referencedColumnName: 'id', nullable: false)]
    private ?Character $character = null;

    #[ORM\Column(name: 'position')]
    private int $position;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRanking(): Ranking
    {
        return $this->ranking;
    }

    public function setRanking(Ranking $ranking): self
    {
        $this->ranking = $ranking;
        return $this;
    }

    public function getCharacter(): Character
    {
        return $this->character;
    }

    public function setCharacter(Character $character): self
    {
        $this->character = $character;
        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;
        return $this;
    }
}
