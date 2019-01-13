<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mgate\TresoBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * NoteDeFraisRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NoteDeFraisRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function findAllByMandat()
    {
        $entities = $this->findBy([], ['mandat' => 'desc', 'date' => 'asc']);
        $nfsParMandat = [];
        foreach ($entities as $nf) {
            $mandat = $nf->getMandat();
            if (array_key_exists($mandat, $nfsParMandat)) {
                $nfsParMandat[$mandat][] = $nf;
            } else {
                $nfsParMandat[$mandat] = [$nf];
            }
        }

        return $nfsParMandat;
    }

    /**
     * Renvoie les NDF pour un mois donné
     * YEAR MONTH DAY sont défini dans DashBoardBundle/DQL (qui doit devenir FrontEndBundle).
     *
     * @return array
     */
    public function findAllByMonth($month, $year, $trimestriel = false)
    {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->select('f')
                     ->from('MgateTresoBundle:NoteDeFrais', 'f');
        if ($trimestriel) {
            $query->where("MONTH(f.date) >= $month")
                  ->andWhere("MONTH(f.date) < ($month + 2)");
        } else {
            $query->where("MONTH(f.date) = $month");
        }

        $query->andWhere("YEAR(f.date) = $year")->orderBy('f.date');

        return $query->getQuery()->getResult();
    }
}