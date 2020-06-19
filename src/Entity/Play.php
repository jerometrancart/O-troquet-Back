<?php
//TODO:faire isGranted sur toutes les routes
namespace App\Entity;

use App\Repository\PlayRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PlayRepository::class)
 */
class Play
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"api_v1_users_stat"})
     */
    private $id;

    
    /**
     * @ORM\Column(type="boolean")
     * @Groups({"api_v1_users_stat"})
     */
    private $win;

    /**
     * @ORM\Column(type="datetime")
     * 
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=Game::class, inversedBy="plays")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"api_v1_users_stat"})
     */
    private $game;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="plays")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWin(): ?bool
    {
        return $this->win;
    }

    public function setWin(bool $win): self
    {
        $this->win = $win;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

   
}
