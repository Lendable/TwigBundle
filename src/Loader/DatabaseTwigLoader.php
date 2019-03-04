<?php

declare(strict_types=1);

namespace Alpha\TwigBundle\Loader;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;

class DatabaseTwigLoader implements \Twig_LoaderInterface
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
        try {
            $template = $this->findTemplate($templateName);

            return $template instanceof $this->entity;
        } catch (NoResultException $e) {
            return false;
        }
    }

    /**
     * @return object
     */
    private function findTemplate(string $templateName)
    {
        return $this->entityManager
            ->getRepository($this->entity)
            ->createQueryBuilder('t')
            ->select('t')
            ->where('t.name = :name')
            ->setMaxResults(1)
            ->getQuery()
            ->setParameter('name', $templateName)
            ->getSingleResult();
    }

    public function getSourceContext($templateName): \Twig_Source
    {
        $templateSource = $this->getValue('source', $templateName);
        if (!is_string($templateSource) || mb_strlen($templateSource) < 1) {
            throw new \Twig_Error_Loader(sprintf('Template "%s" does not exist.', $templateName));
        }

        return new \Twig_Source($templateSource, $templateName);
    }

    public function isFresh($templateName, $time): bool
    {
        try {
            $lastModified = $this->getValue('lastModified', $templateName);
            if (null === $lastModified) {
                return false;
            }

            return strtotime($lastModified) <= $time;
        } catch (NoResultException $e) {
            return false;
        }
    }

    /**
     * @return mixed
     */
    private function getValue(string $column, string $templateName)
    {
        return $this->entityManager
            ->getRepository($this->entity)
            ->createQueryBuilder('t')
            ->select(sprintf('t.%s', $column))
            ->where('t.name = :name')
            ->setMaxResults(1)
            ->getQuery()
            ->setParameter('name', $templateName)
            ->getSingleScalarResult();
    }
}
