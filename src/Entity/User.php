<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

	#[ORM\Column(length: 180, unique: true)]
                                                                                    	private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?LocationSite $locationSiteUser = null;

    #[ORM\ManyToMany(targetEntity: Event::class, inversedBy: 'users')]
    private Collection $registred;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Event::class)]
    private Collection $organizer;

    public function __construct()
    {
        $this->registred = new ArrayCollection();
        $this->organizer = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

	public function getEUsername(): ?string
                                                                                    	{
                                                                                    		return $this->username;
                                                                                    	}

	public function setUsername(string $username): static
                                                                                    	{
                                                                                    		$this->username = $username;
                                                                                    
                                                                                    		return $this;
                                                                                    	}

	/**
	 * A visual identifier that represents this user.
	 *
	 * @see UserInterface
	 */
	public function getUserIdentifier(): string
                                                                                    	{
                                                                                    		return (string) $this->email;
                                                                                    	}

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getLocationSite(): ?LocationSite
    {
        return $this->locationSite;
    }

    public function setLocationSite(?LocationSite $locationSite): static
    {
        $this->locationSite = $locationSite;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getRegistred(): Collection
    {
        return $this->registred;
    }

    public function addRegistred(Event $registred): static
    {
        if (!$this->registred->contains($registred)) {
            $this->registred->add($registred);
        }

        return $this;
    }

    public function removeRegistred(Event $registred): static
    {
        $this->registred->removeElement($registred);

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getOrganizer(): Collection
    {
        return $this->organizer;
    }

    public function addOrganizer(Event $organizer): static
    {
        if (!$this->organizer->contains($organizer)) {
            $this->organizer->add($organizer);
            $organizer->setUser($this);
        }

        return $this;
    }

    public function removeOrganizer(Event $organizer): static
    {
        if ($this->organizer->removeElement($organizer)) {
            // set the owning side to null (unless already changed)
            if ($organizer->getUser() === $this) {
                $organizer->setUser(null);
            }
        }

        return $this;
    }
}
