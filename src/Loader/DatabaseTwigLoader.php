<?php

namespace Alpha\TwigBundle\Loader;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Twig_LoaderInterface;
use Twig_Error_Loader;

class DatabaseTwigLoader implements Twig_LoaderInterface
{
    protected $em;
    protected $entity;

    public function __construct(EntityManagerInterface $em, $entity)
    {
        $this->em = $em;
        $this->entity = $entity;
    }

    public function getSource($name)
    {
        if (false === $source = $this->getValue('source', $name)) {
            throw new Twig_Error_Loader(sprintf('Template "%s" does not exist.', $name));
        }

        return $source;
    }

    public function getCacheKey($name)
    {
        return $name;
    }

    public function isFresh($name, $time)
    {
        if (false === $lastModified = $this->getValue('lastModified', $name)) {
            return false;
        }

        return strtotime($lastModified) <= $time;
    }

    protected function getValue($column, $name)
    {
        try {
            $result = $this->em
                ->getRepository($this->entity)
                ->createQueryBuilder('t')
                ->select('t.'.$column)
                ->where('t.name = :name')
                ->setMaxResults(1)
                ->getQuery()
                ->setParameter('name', $name)
                ->getSingleScalarResult();
        } catch (NoResultException $e) {
            $result = false;
        }

        return $result;
    }
}
