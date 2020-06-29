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
     * @MaxDepth(4)
     */
    private $friend;
   
    /**
     * @ORM\Column(type="boolean")
     * @Groups({"api_v1_users","friends"})
     */
    private $isAccepted;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"api_v1_users","friends"})
     */
    private $isAnswered;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsAccepted(): ?bool
    {
        return $this->isAccepted;
    }

    public function setIsAccepted(bool $isAccepted): self
    {
        $this->isAccepted = $isAccepted;

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

    

    /**
     * @Groups({"api_v1_users","friends"})
     */
    public function getFriendDetails()
    {
        return [
            'id' => $this->friend->getId(),
            'username' => $this->friend->getUsername(),
        ];
    }

    public function getIsAnswered(): ?bool
    {
        return $this->isAnswered;
    }

    public function setIsAnswered(bool $isAnswered): self
    {
        $this->isAnswered = $isAnswered;

        return $this;
    }
}
