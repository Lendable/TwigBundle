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
     * @var string[]|null
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

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string[]|null $services
     */
    public function setServices(?array $services): self
    {
        $this->services = $services;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getServices(): ?array
    {
        return $this->services;
    }

    public function setLastModified(\DateTimeInterface $lastModified): self
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    public function setLastModifiedToCurrentMoment(): void
    {
        $this->lastModified = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->getName() ?: '';
    }
}
