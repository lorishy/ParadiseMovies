<?php

namespace App\Entity;

use App\Repository\FilmRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\Timestampable;

#[ORM\Entity(repositoryClass: FilmRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Film
{

    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 2500)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $duree = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $video = null;

    #[ORM\ManyToMany(targetEntity: Acteur::class, inversedBy: 'films')]
    private Collection $casting;

    #[ORM\ManyToMany(targetEntity: Categorie::class, inversedBy: 'films')]
    private Collection $categorie;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $sortie = null;

    #[ORM\OneToMany(mappedBy: 'film', targetEntity: Avis::class)]
    private Collection $avis;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'favsFilms')]
    private Collection $usersFavs;

    #[ORM\Column(nullable: true)]
    private ?float $note = null;

    public function __construct()
    {
        $this->casting = new ArrayCollection();
        $this->categorie = new ArrayCollection();
        $this->avis = new ArrayCollection();
        $this->usersFavs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDuree(): ?\DateTimeInterface
    {
        return $this->duree;
    }

    public function setDuree(\DateTimeInterface $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(?string $video): self
    {
        $this->video = $video;

        return $this;
    }

    /**
     * @return Collection<int, Acteur>
     */
    public function getCasting(): Collection
    {
        return $this->casting;
    }

    public function addCasting(Acteur $casting): self
    {
        if (!$this->casting->contains($casting)) {
            $this->casting->add($casting);
        }

        return $this;
    }

    public function removeCasting(Acteur $casting): self
    {
        $this->casting->removeElement($casting);

        return $this;
    }

    /**
     * @return Collection<int, Categorie>
     */
    public function getCategorie(): Collection
    {
        return $this->categorie;
    }

    public function addCategorie(Categorie $categorie): self
    {
        if (!$this->categorie->contains($categorie)) {
            $this->categorie->add($categorie);
        }

        return $this;
    }

    public function removeCategorie(Categorie $categorie): self
    {
        $this->categorie->removeElement($categorie);

        return $this;
    }

    public function getSortie(): ?\DateTimeInterface
    {
        return $this->sortie;
    }

    public function setSortie(\DateTimeInterface $sortie): self
    {
        $this->sortie = $sortie;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getTitre() ?? '';
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvis(): Collection
    {
        return $this->avis;
    }

    /**
     * Ajouter un avis associé au film.
     *
     * @param Avis $avi
     * @return self
     */
    public function addAvi(Avis $avi): self
    {
        if (!$this->avis->contains($avi)) {
            $this->avis[] = $avi;
            $avi->setFilm($this);
            $this->updateNote(); // Mettre à jour la note du film
        }

        return $this;
    }

    /**
     * Supprimer un avis associé au film.
     *
     * @param Avis $avi
     * @return self
     */
    public function removeAvi(Avis $avi): self
    {
        if ($this->avis->removeElement($avi)) {
            $avi->setFilm(null);
            $this->updateNote(); // Mettre à jour la note du film
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsersFavs(): Collection
    {
        return $this->usersFavs;
    }

    public function addUsersFav(User $usersFav): self
    {
        if (!$this->usersFavs->contains($usersFav)) {
            $this->usersFavs->add($usersFav);
            $usersFav->addFavsFilm($this);
        }

        return $this;
    }

    public function removeUsersFav(User $usersFav): self
    {
        if ($this->usersFavs->removeElement($usersFav)) {
            $usersFav->removeFavsFilm($this);
        }

        return $this;
    }

    public function getNote(): ?float
    {
        return $this->note;
    }

    public function setNote(?float $note): self
    {
        $this->note = $note;

        return $this;
    }
    
    /**
     * Recalculer la note du film en fonction des avis associés.
     */
    public function updateNote(): void
    {
        $avis = $this->getAvis();
        $totalNotes = 0;
        $count = count($avis);

        if ($count > 0) {
            foreach ($avis as $avi) {
                $totalNotes += $avi->getNote();
            }

            $this->setNote($totalNotes / $count);
        } else {
            $this->setNote(0);
        }
    }
}
