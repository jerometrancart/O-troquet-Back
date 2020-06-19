<?php

namespace App\Entity;

use App\Repository\UserFriendsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

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
    
    //*@MaxDepth(2)
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="friends")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="friendsWithMe")
     *
     *
     */
    private $friend;

   
    /**
     * @ORM\Column(type="boolean")
     */
    private $is_accepted;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_requested;


    public function getId(): ?int
    {
        return $this->id;
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

    public function getIsRequested(): ?bool
    {
        return $this->is_requested;
    }

    public function setIsRequested(bool $is_requested): self
    {
        $this->is_requested = $is_requested;

        return $this;
    }
}
