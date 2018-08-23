<?php

declare(strict_types=1);

namespace Alpha\TwigBundle\Loader;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface;
use Twig\Source;

class DatabaseTwigLoader implements LoaderInterface
{
    protected $entityManager;
    protected $entity;

    public function __construct(EntityManagerInterface $entityManager, string $entity)
    {
        $this->entityManager = $entityManager;
        $this->entity = $entity;
    }

    public function getCacheKey($name): string
    {
        return $name;
    }

    public function exists($templateName): bool
    {
        $template = $this->findTemplate($templateName);

        return $template instanceof $this->entity;
    }

    /**
     * @return null|object
     */
    private function findTemplate(string $templateName)
    {
        try {
            return $this->entityManager
                ->getRepository($this->entity)
                ->createQueryBuilder('t')
                ->select('t')
                ->where('t.name = :name')
                ->setMaxResults(1)
                ->getQuery()
                ->setParameter('name', $templateName)
                ->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function getSourceContext($templateName): Source
    {
        $templateSource = $this->getValue('source', $templateName);
        if (!is_string($templateSource) || mb_strlen($templateSource) < 1) {
            throw new LoaderError(sprintf('Template "%s" does not exist.', $templateName));
        }

        return new Source($templateSource, $templateName);
    }

    public function isFresh($templateName, $time): bool
    {
        $lastModified = $this->getValue('lastModified', $templateName);
        if (null === $lastModified) {
            return false;
        }

        return strtotime($lastModified) <= $time;
    }

    /**
     * @return null|mixed
     */
    private function getValue(string $column, string $templateName)
    {
        try {
            return $this->entityManager
                ->getRepository($this->entity)
                ->createQueryBuilder('t')
                ->select(sprintf('t.%s', $column))
                ->where('t.name = :name')
                ->setMaxResults(1)
                ->getQuery()
                ->setParameter('name', $templateName)
                ->getSingleScalarResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}
