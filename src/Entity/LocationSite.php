<?php

namespace App\Entity;

use App\Repository\LocationSiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocationSiteRepository::class)]
class LocationSite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'locationSiteEvent', targetEntity: Event::class)]
    private Collection $locationSite;

    #[ORM\OneToMany(mappedBy: 'locationSiteUser', targetEntity: User::class)]
    private Collection $users;

    public function __construct()
    {
        $this->locationSite = new ArrayCollection();
        $this->users = new ArrayCollection();
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

    /**
     * @return Collection<int, Event>
     */
    public function getLocationSite(): Collection
    {
        return $this->locationSite;
    }

    public function addLocationSite(Event $locationSite): static
    {
        if (!$this->locationSite->contains($locationSite)) {
            $this->locationSite->add($locationSite);
            $locationSite->setLocationSite($this);
        }

        return $this;
    }

    public function removeLocationSite(Event $locationSite): static
    {
        if ($this->locationSite->removeElement($locationSite)) {
            // set the owning side to null (unless already changed)
            if ($locationSite->getLocationSite() === $this) {
                $locationSite->setLocationSite(null);
            }
        }

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
            $user->setLocationSite($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getLocationSite() === $this) {
                $user->setLocationSite(null);
            }
        }

        return $this;
    }
}
