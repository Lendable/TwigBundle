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

    public function getSourceContext($templateName): \Twig_Source
    {
        $templateSource = $this->getSource($templateName);

        return new \Twig_Source($templateSource, $templateName);
    }

    public function getSource($templateName): string
    {
        $source = $this->getValue('source', $templateName);
        if (!is_string($source) || mb_strlen($source) < 1) {
            throw new \Twig_Error_Loader(sprintf('Template "%s" does not exist.', $templateName));
        }

        return $source;
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
    private function getValue(string $coloumn, string $templateName)
    {
        try {
            return $this->entityManager
                ->getRepository($this->entity)
                ->createQueryBuilder('t')
                ->select(sprintf('t.%s', $coloumn))
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
