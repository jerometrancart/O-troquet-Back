<?php

namespace App\Entity;

use App\Repository\UserFriendsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserFriendsRepository::class)
 */
class UserFriends
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

     /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="friends")
     * 
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="friendsWithMe")
     * 
     */
    private $friend; 

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_requested;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_accepted;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsRequested(): ?bool
    {
        return $this->is_requested;
    }

    public function setIsRequested(bool $is_requested): self
    {
        $this->is_requested = $is_requested;

        return $this;
    }

    public function getIsAccepted(): ?bool
    {
        return $this->is_accepted;
    }

    public function setIsAccepted(bool $is_accepted): self
    {
        $this->is_accepted = $is_accepted;

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

    public function getFriend(): ?User
    {
        return $this->friend;
    }

    public function setFriend(?User $friend): self
    {
        $this->friend = $friend;

        return $this;
    }
    
}
