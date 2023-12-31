<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'Il existe déjà un compte avec cette adresse email !')]
#[UniqueEntity(fields: ['username'], message: 'Il existe déjà un compte avec ce nom d\'utilisateur !')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
	#[Assert\NotNull(message: 'Veuillez saisir une adresse email !')]
	#[Assert\NotBlank(message: 'Veuillez saisir une adresse email !')]
	#[Assert\Email(message: 'L\'adresse email suivante {{ value }} n\'est pas valide !')]
    private ?string $email = null;

	#[ORM\Column(length: 180, unique: true)]
         	private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string|null The hashed password
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

    #[ORM\ManyToMany(targetEntity: Event::class, inversedBy: 'users')]
    private Collection $registred;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Event::class)]
    private Collection $organizer;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?LocationSite $sites_no_site = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picture = null;

    #[ORM\Column]
    private ?int $force_change = null;

    //Cette propriété ne sert qu'à recevoir l'objet créé par Symfony lors de l'upload du fichier
    //Et à faire la validation
#[Assert\Image(
    maxSize: "20M",
    maxWidth: 2000,
    maxHeight: 2000,
    minHeight: 200,
      )]
    private ?UploadedFile $pictureUpload;

#[ORM\Column]
private ?int $isPublic = null;

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

	public function getUsername(): ?string
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

    public function getFullName(): string
    {
        return $this->getFirstName()." ".substr($this->getName(), 0, 1).".";
    }

    public function getSitesNoSite(): ?locationSite
    {
        return $this->sites_no_site;
    }

    public function setSitesNoSite(?locationSite $sites_no_site): static
    {
        $this->sites_no_site = $sites_no_site;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getForceChange(): ?int
    {
        return $this->force_change;
    }

    public function setForceChange(int $force_change): static
    {
        $this->force_change = $force_change;

        return $this;
    }

    public function getPictureUpload(): ?UploadedFile
    {
        return $this->pictureUpload;
    }

    public function setPictureUpload(?UploadedFile $pictureUpload): void
    {
        $this->pictureUpload = $pictureUpload;
    }

    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'username' => $this->username,
            'roles' => $this->roles,
            'password' => $this->password,
            'name' => $this->name,
            'firstName' => $this->firstName,
            'phone' => $this->phone,
            'isActive' => $this->isActive,
            'registred' => $this->registred,
            'organizer' => $this->organizer,
            'sites_no_site' => $this->sites_no_site,
            'picture' => $this->picture,
            'force_change' => $this->force_change,
        ];
    }

    public function __unserialize(array $serialized): void
    {
        $this->id = $serialized['id'];
        $this->email = $serialized['email'];
        $this->username = $serialized['username'];
        $this->roles = $serialized['roles'];
        $this->password = $serialized['password'];
        $this->name = $serialized['name'];
        $this->firstName = $serialized['firstName'];
        $this->phone = $serialized['phone'];
        $this->isActive = $serialized['isActive'];
        $this->registred = $serialized['registred'];
        $this->organizer = $serialized['organizer'];
        $this->sites_no_site = $serialized['sites_no_site'];
        $this->picture = $serialized['picture'];
        $this->force_change = $serialized['force_change'];

    }

    public function getIsPublic(): ?int
    {
        return $this->isPublic;
    }

    public function setIsPublic(int $isPublic): static
    {
        $this->isPublic = $isPublic;

        return $this;
    }

}
