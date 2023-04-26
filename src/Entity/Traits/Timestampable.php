<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait Timestampable
{
  #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
  private ?\DateTimeImmutable $createdAt = null;

  #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
  private ?\DateTimeImmutable $updatedAt = null;

  public function getCreatedAt(): ?\DateTimeInterface
  {
      return $this->createdAt;
  }

  public function setCreatedAt(\DateTimeInterface $createdAt): self
  {
      $this->createdAt = $createdAt;

      return $this;
  }

  public function getUpdatedAt(): ?\DateTimeInterface
  {
      return $this->updatedAt;
  }

  public function setUpdatedAt(\DateTimeInterface $updatedAt): self
  {
      $this->updatedAt = $updatedAt;

      return $this;
  }


  #[ORM\PrePersist]
  #[ORM\PreUpdate]
  public function updateTimestamp()
  {
      if ($this->getCreatedAt()=== null) {
          $this->setCreatedAt(new \DateTimeImmutable());
      }

      $this->setUpdatedAt(new \DateTimeImmutable());
  }
}