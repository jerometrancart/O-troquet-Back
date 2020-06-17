<?php

namespace App\Entity;

use App\Repository\AchievementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=AchievementRepository::class)
 */
class Achievement
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"api_v1_users_read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     * *@Groups({"api_v1_users_read"})
     */
    private $phrase;

    /**
     * @ORM\Column(type="string", length=64)
     * @Groups({"api_v1_users_read"})
     */
    private $icon;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="achievements")
     */
    private $hadUsers;

    /**
     * @ORM\ManyToMany(targetEntity=Game::class, mappedBy="game_achievement")
     */
    private $games;

    public function __construct()
    {
        $this->hadUsers = new ArrayCollection();
        $this->games = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhrase(): ?string
    {
        return $this->phrase;
    }

    public function setPhrase(string $phrase): self
    {
        $this->phrase = $phrase;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getHadUsers(): Collection
    {
        return $this->hadUsers;
    }

    public function addHadUser(User $hadUser): self
    {
        if (!$this->hadUsers->contains($hadUser)) {
            $this->hadUsers[] = $hadUser;
        }

        return $this;
    }

    public function removeHadUser(User $hadUser): self
    {
        if ($this->hadUsers->contains($hadUser)) {
            $this->hadUsers->removeElement($hadUser);
        }

        return $this;
    }

    /**
     * @return Collection|Game[]
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->addGameAchievement($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->contains($game)) {
            $this->games->removeElement($game);
            $game->removeGameAchievement($this);
        }

        return $this;
    }


}
