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

namespace Forci\Bundle\MenuBuilder\Repository;

use Doctrine\ORM\EntityRepository;
use Forci\Bundle\MenuBuilder\Entity\Menu;
use Forci\Bundle\MenuBuilder\Entity\Route;
use Forci\Bundle\MenuBuilder\Filter\Menu\MenuFilter;
use Wucdbm\Bundle\QuickUIBundle\Repository\QuickUIRepositoryTrait;

class MenuRepository extends EntityRepository {

    use QuickUIRepositoryTrait;

    public function remove(Menu $menu) {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $conn->beginTransaction();
        try {
            foreach ($menu->getItems() as $item) {
                foreach ($item->getParameters() as $parameter) {
                    $em->remove($parameter);
                }
                $em->remove($item);
            }

            $em->remove($menu);
            $em->flush();

            $conn->commit();
        } catch (\Throwable $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    public function filter(MenuFilter $filter) {
        $builder = $this->getQueryBuilder();

        if ($filter->getName()) {
            $builder->andWhere('m.name = :name')
                ->setParameter('name', '%'.$filter->getName().'%');
        }

        $route = $filter->getRoute();
        if ($route instanceof Route) {
            $builder->andWhere('itemRoute.id = :routeId')
                ->setParameter('routeId', $route->getId());
        }

        return $this->returnFilteredEntities($builder, $filter, 'm.id');
    }

    public function findOneById(int $id): ?Menu {
        $builder = $this->getQueryBuilder()
            ->andWhere('m.id = :id')
            ->setParameter('id', $id);
        $query = $builder->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * @return Menu[]
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findAll() {
        $builder = $this->getQueryBuilder();

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function getQueryBuilder() {
        return $this->createQueryBuilder('m')
            ->addSelect('i, itemParameters, itemChildren, itemParent, itemMenu, itemRoute')
            ->leftJoin('m.items', 'i', null, null, 'i.id')
            ->leftJoin('i.parameters', 'itemParameters')
            ->leftJoin('i.children', 'itemChildren')
            ->leftJoin('i.parent', 'itemParent')
            ->leftJoin('i.menu', 'itemMenu')
            ->leftJoin('i.route', 'itemRoute')
            ->addOrderBy('m.id', 'ASC')
            ->addOrderBy('i.ord', 'ASC')
            ->addOrderBy('itemChildren.ord', 'ASC');
    }

    public function save(Menu $menu) {
        $em = $this->getEntityManager();
        $em->persist($menu);
        foreach ($menu->getItems() as $item) {
            $em->persist($item);
        }
        $em->flush();
    }
}
