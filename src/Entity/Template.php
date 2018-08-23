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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setLastModified(\DateTimeImmutable $lastModified): self
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    public function getLastModified(): ?\DateTimeImmutable
    {
        return $this->lastModified instanceof \DateTime
            ? \DateTimeImmutable::createFromMutable($this->lastModified)
            : $this->lastModified;
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
