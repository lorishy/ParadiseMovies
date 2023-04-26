<?php

namespace App\Entity;

use App\Repository\SalleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\Timestampable;


#[ORM\Entity(repositoryClass: SalleRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Salle
{

    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $nombre_place = null;

    #[ORM\Column]
    private ?int $numero_salle = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombrePlace(): ?int
    {
        return $this->nombre_place;
    }

    public function setNombrePlace(int $nombre_place): self
    {
        $this->nombre_place = $nombre_place;

        return $this;
    }

    public function getNumeroSalle(): ?int
    {
        return $this->numero_salle;
    }

    public function setNumeroSalle(int $numero_salle): self
    {
        $this->numero_salle = $numero_salle;

        return $this;
    }
}
