<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Polonairs\Dialtime\ModelBundle\Entity\Partner;
use Polonairs\Dialtime\ModelBundle\Entity\Category;
use Polonairs\Dialtime\ModelBundle\Entity\CategoryVersion;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class CategoryRepository extends EntityRepository
{
    public function loadOne($id)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT category, categoryVersion 
            FROM ModelBundle:Category category 
            JOIN ModelBundle:CategoryVersion categoryVersion WITH category.actual = categoryVersion.id
            WHERE category.id = :id');
        $query->setParameter('id', $id);
        $data = $query->getResult();
        foreach($data as $d) if ($d instanceof Category) return $d;
        return null;
    }
    public function loadIndexed()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT category, categoryVersion 
            FROM ModelBundle:Category category 
            JOIN ModelBundle:CategoryVersion categoryVersion WITH category.actual = categoryVersion.id');
        $data = $query->getResult();
        $result = [];
        foreach($data as $d)
        {
            if ($d instanceof Category) $result[$d->getId()] = $d;
        }
        return $result;
    }
    public function isChildOrSame(Category $parent, Category $child)
    {
        if (($parent->getId() == 3) && ($child->getId() == 6))
        {
            //dump($parent->getId());
            //dump($child->getId());
            if ($parent->getId() == $child->getId()) return true;
            $result = $this->loadIndexed();
            //dump($result);
            if ($result[$child->getId()]->getParent() === null) return false;
            $category = $result[$child->getId()]->getParent()->getId();
            while (true)
            {
                //dump($category);
                if ($category == $parent->getId()) return true;
                if ($result[$category]->getParent() === null) return false;
                $category = $result[$category]->getParent()->getId();
            }
            return false;
        }
        else
        {
            //dump($parent->getId());
            //dump($child->getId());
            if ($parent->getId() === $child->getId()) return true;
            $result = $this->loadIndexed();
            //dump($result);
            if ($result[$child->getId()]->getParent() === null) return false;
            $category = $result[$child->getId()]->getParent()->getId();
            while (true)
            {
                //dump($category);
                if ($category === $parent->getId()) return true;
                if ($result[$category]->getParent() === null) return false;
                $category = $result[$category]->getParent()->getId();
            }
            return false;
        }
    }
}