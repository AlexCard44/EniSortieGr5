<?php

namespace App\Entity;

use App\Repository\SortiesFiltreRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\FormTypeInterface;


class SortiesFiltre
{

    public ?int $id = null;


    public ?bool $sortiesOrganisees = null;


    public ?bool $sortiesInscrit = null;


    public ?bool $sortiesNonInscrit = null;


    public ?bool $sortiesPassees = null;

    public ?string $name=null;

    public ?\DateTimeInterface $dateTime= null;

    /**
     * @return \DateTimeInterface|null
     */
    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->dateTime;
    }

    /**
     * @param \DateTimeInterface|null $dateTime
     */
    public function setDateTime(?\DateTimeInterface $dateTime): void
    {
        $this->dateTime = $dateTime;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isSortiesOrganisees(): ?bool
    {
        return $this->sortiesOrganisees;
    }

    public function setSortiesOrganisees(?bool $sortiesOrganisees): static
    {
        $this->sortiesOrganisees = $sortiesOrganisees;

        return $this;
    }

    public function isSortiesInscrit(): ?bool
    {
        return $this->sortiesInscrit;
    }

    public function setSortiesInscrit(?bool $sortiesInscrit): static
    {
        $this->sortiesInscrit = $sortiesInscrit;

        return $this;
    }

    public function isSortiesNonInscrit(): ?bool
    {
        return $this->sortiesNonInscrit;
    }

    public function setSortiesNonInscrit(?bool $sortiesNonInscrit): static
    {
        $this->sortiesNonInscrit = $sortiesNonInscrit;

        return $this;
    }

    public function isSortiesPassees(): ?bool
    {
        return $this->sortiesPassees;
    }

    public function setSortiesPassees(?bool $sortiesPassees): static
    {
        $this->sortiesPassees = $sortiesPassees;

        return $this;
    }
}
