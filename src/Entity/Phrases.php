<?php

namespace App\Entity;

use App\Repository\PhrasesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PhrasesRepository::class)]
#[ORM\Table(name: 'phrases', schema: 'simpsons')]
class Phrases
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name:"phrase", type: "text")]
    private ?string $phrase = null;

    #[ORM\ManyToOne(inversedBy: 'phrases')]
    #[ORM\JoinColumn(name: 'character_id', referencedColumnName: 'id', nullable: false)]
    private ?Character $character = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhrase(): ?string
    {
        return $this->phrase;
    }

    public function setPhrase(string $phrase): static
    {
        $this->phrase = $phrase;
        return $this;
    }

    public function getCharacter(): ?Character
    {
        return $this->character;
    }

    public function setCharacter(?Character $character): static
    {
        $this->character = $character;
        return $this;
    }
}
