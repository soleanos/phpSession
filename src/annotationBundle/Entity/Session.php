<?php

namespace annotationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Session
 *
 * @ORM\Table(name="session")
 * @ORM\Entity(repositoryClass="annotationBundle\Repository\SessionRepository")
 */
class Session
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="intitule", type="string", length=255)
     */
    private $intitule;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDebut", type="date")
     */
    private $dateDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateFin", type="date")
     */
    private $dateFin;


    /**
     *
     *
     * @ORM\ManyToOne(targetEntity="Personne", inversedBy="session")
     * @ORM\JoinColumn(name="enseignant_id", referencedColumnName="id")
     *
     */
    private $enseignant;

    /**
     * @var ArrayCollection etudiants $etudiants
     * 
     * @ORM\ManyToMany(targetEntity="Personne", inversedBy="Session")
     * @ORM\JoinTable(name="Session_Personne")
     */

    private $etudiants;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set intitule
     *
     * @param string $intitule
     * @return Session
     */
    public function setIntitule($intitule)
    {
        $this->intitule = $intitule;

        return $this;
    }

    /**
     * Get intitule
     *
     * @return string 
     */
    public function getIntitule()
    {
        return $this->intitule;
    }

    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     * @return Session
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateDebut
     *
     * @return \DateTime 
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateFin
     *
     * @param \DateTime $dateFin
     * @return Session
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return \DateTime 
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set enseignant
     *
     * @param \annotationBundle\Entity\Personne $enseignant
     * @return Session
     */
    public function setEnseignant(\annotationBundle\Entity\Personne $enseignant = null)
    {
        $this->enseignant = $enseignant;

        return $this;
    }

    /**
     * Get enseignant
     *
     * @return \annotationBundle\Entity\Personne 
     */
    public function getEnseignant()
    {
        return $this->enseignant;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->eleves = new \Doctrine\Common\Collections\ArrayCollection();
    }

    

    /**
     * Add etudiants
     *
     * @param \annotationBundle\Entity\Session $etudiants
     * @return Session
     */
    public function addEtudiant(\annotationBundle\Entity\Personne $etudiants)
    {
        $this->etudiants[] = $etudiants;

        return $this;
    }

    /**
     * Remove etudiants
     *
     * @param \annotationBundle\Entity\Personne $etudiants
     */
    public function removeEtudiant(\annotationBundle\Entity\Personne $etudiants)
    {
        $this->etudiants->removeElement($etudiants);
    }

    /**
     * Get etudiants
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEtudiants()
    {
        return $this->etudiants;
    }
}
