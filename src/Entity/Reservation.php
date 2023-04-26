<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\Timestampable;


#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Reservation
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantite_place = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantitePlace(): ?int
    {
        return $this->quantite_place;
    }

    public function setQuantitePlace(int $quantite_place): self
    {
        $this->quantite_place = $quantite_place;

        return $this;
    }
}
