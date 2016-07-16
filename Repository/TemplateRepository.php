<?php

namespace Polonairs\Dialtime\ModelBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Polonairs\Dialtime\ModelBundle\Entity\Template;

class TemplateRepository extends EntityRepository
{
    public function loadOneByName($name)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'SELECT p, pv FROM ModelBundle:Template p JOIN ModelBundle:TemplateVersion pv WITH p.actual = pv.id WHERE pv.name = :name');
        $query->setParameter('name', $name);
        $phones = $query->getResult();
        foreach($phones as $phone) if ($phone instanceof Template) return $phone;
        return null;
    }
    public function loadOneById($id)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'SELECT p, pv FROM ModelBundle:Template p JOIN ModelBundle:TemplateVersion pv WITH p.actual = pv.id WHERE p.id = :id');
        $query->setParameter('id', $id);
        $phones = $query->getResult();
        foreach($phones as $phone) if ($phone instanceof Template) return $phone;
        return null;
    }
    public function loadAllTemplates()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT template, templateVersion 
            FROM ModelBundle:Template template 
            JOIN ModelBundle:TemplateVersion templateVersion WITH template.actual = templateVersion.id 
            ORDER BY templateVersion.name ASC');
        $entries = $query->getResult();
        $result = [];
        foreach($entries as $e)
        {
            if ($e instanceof Template) 
            {
                /*$result['by/id'][$e->getId()] = $e;
                $result['by/name'][$e->getName()] = $e;/**/
                $result[] = $e;
            }
        }
        return $result;
    }
    public function loadAllTemplatesIndexedByName()
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('
            SELECT template, templateVersion 
            FROM ModelBundle:Template template 
            JOIN ModelBundle:TemplateVersion templateVersion WITH template.actual = templateVersion.id 
            ORDER BY templateVersion.name ASC');
        $entries = $query->getResult();
        $result = [];
        foreach($entries as $e)
        {
            if ($e instanceof Template) 
            {
                /*$result['by/id'][$e->getId()] = $e;
                $result['by/name'][$e->getName()] = $e;/**/
                $result[$e->getName()] = $e;
            }
        }
        return $result;
    }
}