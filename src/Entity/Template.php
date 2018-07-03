<?php

declare(strict_types=1);

namespace Alpha\TwigBundle\Entity;

class Template
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $source;

    /**
     * @var array
     */
    protected $services;

    /**
     * @var \DateTimeInterface
     */
    protected $lastModified;

    public function getId(): int
    {
        return $this->id;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @param array|null $services
     */
    public function setServices($services): self
    {
        $this->services = $services;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getServices()
    {
        return $this->services;
    }

    public function setLastModified(\DateTimeInterface $lastModified): self
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    public function getLastModified(): \DateTimeInterface
    {
        return $this->lastModified;
    }

    public function setLastModifiedToCurrentMoment()
    {
        $this->lastModified = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->getName() ?: '';
    }
}
