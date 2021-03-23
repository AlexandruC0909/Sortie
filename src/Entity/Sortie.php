<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use App\Form\RaisonAnnulationType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SortieRepository::class)
 */
class Sortie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateHeureDebut;

    /**
     * @ORM\Column(type="integer")
     */
    private $duree;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateLimiteInscription;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbInscriptionsMax;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $infoSortie;

    /**
     * @ORM\ManyToOne(targetEntity=Participant::class, inversedBy="organisateurSorties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organizateur;

    /**
     * @ORM\ManyToMany(targetEntity=Participant::class, inversedBy="inscritSorties")
     */
    private $listeParticipants;

    /**
     * @ORM\ManyToOne(targetEntity=Lieu::class, inversedBy="sortie",cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $lieu;

    /**
     * @ORM\ManyToOne(targetEntity=Etat::class, inversedBy="sortie",cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $etat;

    /**
     * @ORM\Column(type="string", length=2000, nullable=true)
     */
    private $motif;

    public function __construct()
    {
        $this->listeParticipants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateHeureDebut(): ?\DateTimeInterface
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(\DateTimeInterface $dateHeureDebut): self
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateLimiteInscription(): ?\DateTimeInterface
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(\DateTimeInterface $dateLimiteInscription): self
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    public function getNbInscriptionsMax(): ?int
    {
        return $this->nbInscriptionsMax;
    }

    public function setNbInscriptionsMax(int $nbInscriptionsMax): self
    {
        $this->nbInscriptionsMax = $nbInscriptionsMax;

        return $this;
    }

    public function getInfoSortie(): ?string
    {
        return $this->infoSortie;
    }

    public function setInfoSortie(string $infoSortie): self
    {
        $this->infoSortie = $infoSortie;

        return $this;
    }

    public function getOrganizateur(): ?Participant
    {
        return $this->organizateur;
    }

    public function setOrganizateur(?Participant $organizateur): self
    {
        $this->organizateur = $organizateur;

        return $this;
    }

    /**
     * @return Collection|Participant[]
     */
    public function getListeParticipants(): Collection
    {
        return $this->listeParticipants;
    }

    public function addListeParticipant(Participant $listeParticipant): self
    {
        if (!$this->listeParticipants->contains($listeParticipant)) {
            $this->listeParticipants[] = $listeParticipant;
        }

        return $this;
    }

    public function removeListeParticipant(Participant $listeParticipant): self
    {
        $this->listeParticipants->removeElement($listeParticipant);

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(?string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }
}
