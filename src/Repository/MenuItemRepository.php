<?php

/*
 * This file is part of the ForciMenuBuilderBundle package.
 *
 * Copyright (c) Forci Web Consulting Ltd.
 *
 * Author Martin Kirilov <martin@forci.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Forci\Bundle\MenuBuilderBundle\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Forci\Bundle\MenuBuilderBundle\Entity\MenuItem;
use Wucdbm\Bundle\QuickUIBundle\Repository\QuickUIRepositoryTrait;

class MenuItemRepository extends EntityRepository {

    use QuickUIRepositoryTrait;

    /**
     * @param $id
     *
     * @return MenuItem
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneById($id) {
        $builder = $this->createQueryBuilder('i')
            ->andWhere('i.id = :id')
            ->setParameter('id', $id);
        $query = $builder->getQuery();

        return $query->getOneOrNullResult();
    }

    public function remove(MenuItem $item, $isFull = true) {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            $this->_remove($em, $item, $isFull);

            $em->flush();

            $conn->commit();
        } catch (\Throwable $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    public function _remove(EntityManager $em, MenuItem $item, $isFull = true) {
        $parent = $item->getParent();
        /** @var MenuItem $child */
        foreach ($item->getChildren() as $child) {
            if ($isFull) {
                $this->_remove($em, $child, $isFull);
            } else {
                $child->setParent($parent);
                $em->persist($child);
            }
        }

        foreach ($item->getParameters() as $parameter) {
            $em->remove($parameter);
        }

        $em->remove($item);
    }

    public function save(MenuItem $item) {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            foreach ($item->getParameters() as $parameter) {
                $em->persist($parameter);
            }

            $em->persist($item);
            $em->flush();

            $conn->commit();
        } catch (\Throwable $e) {
            $conn->rollBack();
            throw $e;
        }
    }
}
