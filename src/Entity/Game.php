<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 */
class Game
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $icon;

    /**
     * @ORM\ManyToMany(targetEntity=Achievement::class, inversedBy="games")
     */
    private $game_achievement;

    /**
     * @ORM\OneToMany(targetEntity=Play::class, mappedBy="game", orphanRemoval=true, cascade={"persist"})
     */
    private $plays;

    public function __construct()
    {
        $this->game_achievement = new ArrayCollection();
        $this->plays = new ArrayCollection();
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

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return Collection|Achievement[]
     */
    public function getGameAchievement(): Collection
    {
        return $this->game_achievement;
    }

    public function addGameAchievement(Achievement $gameAchievement): self
    {
        if (!$this->game_achievement->contains($gameAchievement)) {
            $this->game_achievement[] = $gameAchievement;
        }

        return $this;
    }

    public function removeGameAchievement(Achievement $gameAchievement): self
    {
        if ($this->game_achievement->contains($gameAchievement)) {
            $this->game_achievement->removeElement($gameAchievement);
        }

        return $this;
    }

    /**
     * @return Collection|Play[]
     */
    public function getPlays(): Collection
    {
        return $this->plays;
    }

    public function addPlay(Play $play): self
    {
        if (!$this->plays->contains($play)) {
            $this->plays[] = $play;
            $play->setGame($this);
        }

        return $this;
    }

    public function removePlay(Play $play): self
    {
        if ($this->plays->contains($play)) {
            $this->plays->removeElement($play);
            // set the owning side to null (unless already changed)
            if ($play->getGame() === $this) {
                $play->setGame(null);
            }
        }
        
        return $this;
    }

}
