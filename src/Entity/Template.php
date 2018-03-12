<?php

namespace Alpha\TwigBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * Template
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
class Template
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="text")
     */
    protected $source;

    /**
     * @var array
     *
     * @ORM\Column(name="services", type="array", nullable=true)
     */
    protected $services;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastModified", type="datetime")
     */
    protected $lastModified;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param  string   $name
     * @return Template
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set source
     *
     * @param  string   $source
     * @return Template
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set services
     *
     * @param  array    $services
     * @return Template
     */
    public function setServices($services)
    {
        $this->services = $services;

        return $this;
    }

    /**
     * Get services
     *
     * @return array
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * Set lastModified
     *
     * @param  \DateTime $lastModified
     * @return Template
     */
    public function setLastModified(DateTime $lastModified)
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    /**
     * Get lastModified
     *
     * @return \DateTime
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setLastModifiedDate()
    {
        $this->lastModified = new DateTime();
    }

    public function __toString()
    {
        return $this->getName() ?: '';
    }
}
