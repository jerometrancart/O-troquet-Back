<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     * @Groups({"api_v1_users"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=64)
     * @Groups({"api_v1_users"})
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=64)
     * @Groups({"api_v1_users"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Groups({"api_v1_users"})
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=64)
     * @Groups({"api_v1_users"})
     */
    private $role;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"api_v1_users"})
     */
    private $is_active;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"api_v1_users"})
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * The people who I think are my friends.
     *
     * @ORM\OneToMany(targetEntity="UserFriends", mappedBy="user")
     */
    private $friends;

    /**
     * The people who think that I’m their friend.
     *
     * @ORM\OneToMany(targetEntity="UserFriends", mappedBy="friend")
     * 
     */
    private $friendsWithMe;

    /**
     * @ORM\ManyToMany(targetEntity=Achievement::class, mappedBy="hadUsers")
     */
    private $achievements;


    /**
     * @ORM\OneToMany(targetEntity=Play::class, mappedBy="user", orphanRemoval=true)
     */
    private $plays;

    public function __construct()
    {
        $this->friends = new ArrayCollection();
        $this->friendsWithMe = new ArrayCollection();
        $this->achievements = new ArrayCollection();
        $this->plays = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): self
    {
        $this->is_active = $is_active;

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
     * @return Collection|UserFriends[]
     */
    public function getFriends(): Collection
    {
        return $this->friends;
    }

    public function addFriend(UserFriends $friend): self
    {
        if (!$this->friends->contains($friend)) {
            $this->friends[] = $friend;
            $friend->setUser($this);
        }

        return $this;
    }

    public function removeFriend(UserFriends $friend): self
    {
        if ($this->friends->contains($friend)) {
            $this->friends->removeElement($friend);
            // set the owning side to null (unless already changed)
            if ($friend->getUser() === $this) {
                $friend->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserFriends[]
     */
    public function getFriendsWithMe(): Collection
    {
        return $this->friendsWithMe;
    }

    public function addFriendsWithMe(UserFriends $friendsWithMe): self
    {
        if (!$this->friendsWithMe->contains($friendsWithMe)) {
            $this->friendsWithMe[] = $friendsWithMe;
            $friendsWithMe->setFriend($this);
        }

        return $this;
    }

    public function removeFriendsWithMe(UserFriends $friendsWithMe): self
    {
        if ($this->friendsWithMe->contains($friendsWithMe)) {
            $this->friendsWithMe->removeElement($friendsWithMe);
            // set the owning side to null (unless already changed)
            if ($friendsWithMe->getFriend() === $this) {
                $friendsWithMe->setFriend(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Achievement[]
     */
    public function getAchievements(): Collection
    {
        return $this->achievements;
    }

    public function addAchievement(Achievement $achievement): self
    {
        if (!$this->achievements->contains($achievement)) {
            $this->achievements[] = $achievement;
            $achievement->addHadUser($this);
        }

        return $this;
    }

    public function removeAchievement(Achievement $achievement): self
    {
        if ($this->achievements->contains($achievement)) {
            $this->achievements->removeElement($achievement);
            $achievement->removeHadUser($this);
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
            $play->setUser($this);
        }

        return $this;
    }

    public function removePlay(Play $play): self
    {
        if ($this->plays->contains($play)) {
            $this->plays->removeElement($play);
            // set the owning side to null (unless already changed)
            if ($play->getUser() === $this) {
                $play->setUser(null);
            }
        }

        return $this;
    }

}
