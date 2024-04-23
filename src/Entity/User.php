<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $pwd = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isactive = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pwdresethash = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $uptime = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $userroles = null;

    #[ORM\OneToMany(targetEntity: Topic::class, mappedBy: 'user')]
    private $topics;

    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'user')]
    private $posts;

    #[ORM\OneToMany(targetEntity: Reply::class, mappedBy: 'topic')]
    private $replies;

    private ?array $roles = [];

    public const ROLE_ADMIN = "ROLE_ADMIN";
    public const ROLE_USER = "ROLE_USER";

    public function __construct()
    {
        $this->topics = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->replies = new ArrayCollection();
    }

    public function getTopics(): Collection
    {
        return $this->topics;
    }

    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function getReplies(): Collection
    {
        return $this->replies;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPwd(): ?string
    {
        return $this->pwd;
    }

    public function setPwd(string $pwd): static
    {
        $this->pwd = $pwd;

        return $this;
    }

    public function isIsactive(): ?bool
    {
        return $this->isactive;
    }

    public function setIsactive(bool $isactive): static
    {
        $this->isactive = $isactive;

        return $this;
    }

    public function getPwdresethash(): ?string
    {
        return $this->pwdresethash;
    }

    public function setPwdresethash(?string $pwdresethash): static
    {
        $this->pwdresethash = $pwdresethash;

        return $this;
    }

    public function getUptime(): ?\DateTimeInterface
    {
        return $this->uptime;
    }

    public function setUptime(\DateTimeInterface $uptime): static
    {
        $this->uptime = $uptime;

        return $this;
    }
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $dec = json_decode($this->userroles);
        $roles = is_array($dec) ? $dec : [];
        // guarantee every user at least has ROLE_USER
        $roles[] = self::ROLE_USER;

        return array_unique($roles);
    }
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
    public function setRoles(array $roles): self
    {
        $this->userroles = json_encode($roles);
        return $this;
    }
    public function getPassword(): string
    {
        return $this->pwd;
    }
    public function getHasRole($rolename)
    {
        return in_array($rolename, $this->userroles);
    }
    public function __toString()
    {
        return $this->username ? $this->username . ' <' . $this->getEmail() . '>' : $this->getEmail();
    }
}
