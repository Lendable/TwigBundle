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

    public function getSource($name): string
    {
        $source = $this->getValue('source', $name);
        if (!is_string($source) || mb_strlen($source) < 1) {
            throw new \Twig_Error_Loader(sprintf('Template "%s" does not exist.', $name));
        }

        return $source;
    }

    public function getCacheKey($name): string
    {
        return $name;
    }

    public function isFresh($name, $time): bool
    {
        if (false === $lastModified = $this->getValue('lastModified', $name)) {
            return false;
        }

        return strtotime($lastModified) <= $time;
    }

    /**
     * @return string|null
     */
    private function getValue(string $column, string $templateName)
    {
        $value = null;

        try {
            $value = $this->entityManager
                ->getRepository($this->entity)
                ->createQueryBuilder('t')
                ->select('t.'.$column)
                ->where('t.name = :name')
                ->setMaxResults(1)
                ->getQuery()
                ->setParameter('name', $templateName)
                ->getSingleScalarResult();
        } catch (NoResultException $e) {
        }

        return $value;
    }
}
