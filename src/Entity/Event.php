<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Type;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startDateTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $limitDateInscription = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $eventInfo = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $duration = null;

    #[ORM\ManyToOne(inversedBy: 'locationSite')]
    #[ORM\JoinColumn(nullable: false)]
    private ?LocationSite $locationSiteEvent = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'registred')]
    private Collection $users;

    #[ORM\ManyToOne(inversedBy: 'organizer')]
    private ?User $user = null;

    #[ORM\Column(type: "string", enumType: State::class)]
    private State $state;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->state = State::Created;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStartDateTime(): ?\DateTimeInterface
    {
        return $this->startDateTime;
    }

    public function setStartDateTime(\DateTimeInterface $startDateTime): static
    {
        $this->startDateTime = $startDateTime;

        return $this;
    }

    public function getLimitDateInscription(): ?\DateTimeInterface
    {
        return $this->limitDateInscription;
    }

    public function setLimitDateInscription(\DateTimeInterface $limitDateInscription): static
    {
        $this->limitDateInscription = $limitDateInscription;

        return $this;
    }

    public function getEventInfo(): ?string
    {
        return $this->eventInfo;
    }

    public function setEventInfo(?string $eventInfo): static
    {
        $this->eventInfo = $eventInfo;

        return $this;
    }

    public function getDuration(): ?\DateTimeInterface
    {
        return $this->duration;
    }

    public function setDuration(\DateTimeInterface $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getLocationSiteEvent(): ?LocationSite
    {
        return $this->locationSiteEvent;
    }

    public function setLocationSiteEvent(?LocationSite $locationSiteEvent): static
    {
        $this->locationSiteEvent = $locationSiteEvent;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addRegistred($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeRegistred($this);
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return State
     */
    public function getState(): State
    {
        return $this->state;
    }

    public function getStateDescription(): string
    {
        return match ($this->state->value) {
            "OPEN" => "Ouvert",
            "CREATED" => "En création",
            "CLOSED" => "Fermé",
            "IN_PROGRESS" => "En cours",
            "PASSED" => "Passé",
            "CANCELLED" => "Annulé",
            default => "type non reconnu",
        };
    }

    public function getIsTooLateToSubscribe(): bool
    {
        return $this->limitDateInscription > new \DateTime('now');
    }

    public function getIsInscrit(User $currentUser): bool
    {
        return $this->users->exists(function($key, $user) use($currentUser){
           return ($user->getId() == $currentUser->getId());
        });
    }

    public function getNbInscrit(): int{
        return strlen($this->users);
    }

    /**
     * @param State $state
     * @return Event
     */
    public function setState(State $state): self
    {
        $this->state = $state;
        return $this;
    }
}
