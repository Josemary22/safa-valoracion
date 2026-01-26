<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'category', schema: 'simpsons')]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name:"name", length: 255)]
    private ?string $name = null;

    #[ORM\Column(name:"image", type: Types::TEXT)]
    private ?string $image = null;

    /**
     * @var Collection<int, Character>
     */
    #[JoinTable(name: 'category_character')]
    #[JoinColumn(name: 'id_category', referencedColumnName: 'id')]
    #[InverseJoinColumn(name: 'id_character', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: Character::class)]
    private Collection $characters;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function addCharacter($character) {
        if (!$this->characters->contains($character)) {
            $this->characters->add($character);
        }

        return $this;
    }
}
