<?php
namespace Dosi\TheseFondationBundle\Entity;

use Doctrine\ORM\EntityRepository;


class CandidatRepository extends EntityRepository
{
    public function getCandidatures($date)
    {

        $qb = $this->createQueryBuilder('p');
        $qb->select('count(p.id)');

        $end = new \DateTime();
        $end->setTimestamp($date->getTimeStamp());
        $end->setTime(23,59,59);
        $qb->where("p.dateCandidature<=:date");
        $qb->setParameter('date',$end, \Doctrine\DBAL\Types\Type::DATETIME);

        return $qb->getQuery()->getSingleScalarResult();
    }
}