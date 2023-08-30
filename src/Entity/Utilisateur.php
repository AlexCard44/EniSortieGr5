<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Serializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
#[Vich\Uploadable()]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
//    #[Assert\NotNull()]
//    #[Assert\NotBlank()]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 30)]
    private ?string $nom = null;

    #[ORM\Column(length: 30)]
    private ?string $prenom = null;

    #[ORM\Column(length: 15, nullable: true)]
    #[Assert\Regex("/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/")]
    private ?string $telephone = null;

    #[ORM\Column(length: 30)]
    private ?string $mail = null;

    #[ORM\Column]
    private ?bool $administrateur = null;

    #[ORM\Column]
    private ?bool $actif = null;

    #[ORM\ManyToOne(inversedBy: 'utilisateurs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Site $site = null;

    #[ORM\OneToMany(mappedBy: 'organisateur', targetEntity: Sortie::class, orphanRemoval: true)]
    private Collection $sortiesOrganisees;

    #[ORM\ManyToMany(targetEntity: Sortie::class, mappedBy: 'participants')]
    private Collection $sortiesParticipees;

    #[ORM\Column(length: 2100, nullable: true)]
    private ?String $photo = null;

    #[Vich\UploadableField(mapping: 'products', fileNameProperty: 'photo', size: 'imageSize')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?int $imageSize = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @return File|null
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @param File|null $imageFile
     */
    public function setImageFile(?File $imageFile): void
    {
        $this->imageFile = $imageFile;
        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    /**
     * @return int|null
     */
    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    /**
     * @param int|null $imageSize
     */
    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTimeImmutable|null $updatedAt
     */
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }


    public function __construct()
    {
        $this->administrateur = false;

        $this->sortiesOrganisees = new ArrayCollection();
        $this->sortiesParticipees = new ArrayCollection();
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }

    public function isAdministrateur(): ?bool
    {
        $role = $this->getRoles();

        if($this->administrateur === true) {
            $this->setRoles([]);
        } else {
            $this->setRoles(["ROLE_ADMIN"]);
        }

        return $this->administrateur;
    }

    public function setAdministrateur(bool $administrateur): static
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): static
    {
        $this->site = $site;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortiesOrganisees(): Collection
    {
        return $this->sortiesOrganisees;
    }

    public function addSortiesOrganisee(Sortie $sortiesOrganisee): static
    {
        if (!$this->sortiesOrganisees->contains($sortiesOrganisee)) {
            $this->sortiesOrganisees->add($sortiesOrganisee);
            $sortiesOrganisee->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSortiesOrganisee(Sortie $sortiesOrganisee): static
    {
        if ($this->sortiesOrganisees->removeElement($sortiesOrganisee)) {
            // set the owning side to null (unless already changed)
            if ($sortiesOrganisee->getOrganisateur() === $this) {
                $sortiesOrganisee->setOrganisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortiesParticipees(): Collection
    {
        return $this->sortiesParticipees;
    }

    public function __toString(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function addSortiesParticipee(Sortie $sortiesParticipee): static
    {
        if (!$this->sortiesParticipees->contains($sortiesParticipee)) {
            $this->sortiesParticipees->add($sortiesParticipee);
            $sortiesParticipee->addParticipant($this);
        }

        return $this;
    }

    public function removeSortiesParticipee(Sortie $sortiesParticipee): static
    {
        if ($this->sortiesParticipees->removeElement($sortiesParticipee)) {
            $sortiesParticipee->removeParticipant($this);
        }

        return $this;
    }

    /**
     * @return String|null
     */
    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    /**
     * @param String|null $photo
     */
    public function setPhoto(?string $photo): void
    {
        $this->photo = $photo;
    }


    public function __serialize()
    {
        return [
            'id' => $this->id,
            "photo" => $this->photo,
            'site'=>$this->site,
            'username'=>$this->username,
            'role'=>$this->roles,
            'password'=>$this->password,
            'nom'=>$this->nom,
            'prenom'=>$this->prenom,
            'telephone'=>$this->telephone,
            'mail'=>$this->mail,
            'administrateur'=>$this->administrateur,
            'actif'=>$this->actif,
            'sortiesParticipees'=>$this->sortiesParticipees,
            'sortiesOrganisees'=>$this->sortiesOrganisees,
            'imageSize'=>$this->imageSize,
            'updatedAt'=>$this->updatedAt
        ];
    }

    public function __unserialize(array $data)
    {
        $this->photo = $data['photo'];
        $this->site = $data['site'];
        $this->sortiesOrganisees = $data['sortiesOrganisees'];
        $this->sortiesParticipees = $data['sortiesParticipees'];
        $this->updatedAt = $data['updatedAt'];
        $this->imageSize = $data['imageSize'];
        $this->actif = $data['actif'];
        $this->administrateur = $data['administrateur'];
        $this->mail = $data['mail'];
        $this->nom = $data['nom'];
        $this->prenom = $data['prenom'];
        $this->password = $data['password'];
        $this->telephone = $data['telephone'];
        $this->username = $data['username'];
        $this->id = $data['id'];

    }

}
