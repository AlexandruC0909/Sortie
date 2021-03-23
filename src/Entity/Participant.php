<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParticipantRepository", repositoryClass=ParticipantRepository::class)
 * @UniqueEntity(fields={"email"}, message="Adresse email déjà utilisée")
 * @UniqueEntity(fields={"pseudo"}, message="Pseudo déjà utilisé")
 * @Vich\Uploadable
 */
class Participant implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Vich\UploadableField(mapping="user_image", fileNameProperty="filename")
     * @var File|null
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string|null
     */
    private $filename;


    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = ['ROLE_USER'];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $telephone;

    /**
     * @ORM\Column(type="boolean")
     */
    private $administrator = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif = 1;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $pseudo;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="organizateur", orphanRemoval=true)
     */
    private $organisateurSorties;

    /**
     * @ORM\ManyToMany(targetEntity=Sortie::class, mappedBy="listeParticipants")
     */
    private $inscritSorties;

    /**
     * @ORM\ManyToOne(targetEntity=Site::class, inversedBy="participant")
     * @ORM\JoinColumn(nullable=false)
     */
    private $site;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTimeInterface|null
     */
     private $updated_at;

    public function __construct()
    {
        $this->organisateurSorties = new ArrayCollection();
        $this->inscritSorties = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
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


        return ($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdministrator(): ?bool
    {
        return $this->administrator;
    }

    public function setAdministrator(bool $administrator): self
    {
        $this->administrator = $administrator;

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getOrganisateurSorties(): Collection
    {
        return $this->organisateurSorties;
    }

    public function addOrganisateurSortie(Sortie $organisateurSortie): self
    {
        if (!$this->organisateurSorties->contains($organisateurSortie)) {
            $this->organisateurSorties[] = $organisateurSortie;
            $organisateurSortie->setOrganizateur($this);
        }

        return $this;
    }

    public function removeOrganizateurSorty(Sortie $organizateurSorty): self
    {
        if ($this->organizateurSorties->removeElement($organizateurSorty)) {
            // set the owning side to null (unless already changed)
            if ($organizateurSorty->getOrganizateur() === $this) {
                $organizateurSorty->setOrganizateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getInscritSorties(): Collection
    {
        return $this->inscritSorties;
    }

    public function addInscritSortie(Sortie $inscritSortie): self
    {
        if (!$this->inscritSorties->contains($inscritSortie)) {
            $this->inscritSorties[] = $inscritSortie;
            $inscritSortie->addListeParticipant($this);
        }

        return $this;
    }

    public function removeInscritSortie(Sortie $inscritSortie): self
    {
        if ($this->inscritSorties->removeElement($inscritSortie)) {
            $inscritSortie->removeListeParticipant($this);
        }

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): self
    {
        $this->site = $site;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @param string|null $filename
     * @return void
     */
    public function setFilename(?string $filename): void
    {
        $this->filename = $filename;
    }

    /**
     * @return File|null
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @param File|UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;
        if (null !== $imageFile){
            $this->updated_at = new \DateTimeImmutable();
        }
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param mixed $updated_at
     * @return Participant
     */
    public function setUpdatedAt($updated_at): Participant
    {
        $this->updated_at = $updated_at;
        return $this;
    }

}
