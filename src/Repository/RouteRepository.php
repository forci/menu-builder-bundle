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
use Forci\Bundle\MenuBuilder\Entity\Route;
use Forci\Bundle\MenuBuilder\Filter\Route\RouteFilter;
use Wucdbm\Bundle\QuickUIBundle\Repository\QuickUIRepositoryTrait;

class RouteRepository extends EntityRepository {

    use QuickUIRepositoryTrait;

    public function getPublicRoutesQueryBuilder() {
        $builder = $this->getQueryBuilder();

        $builder->andWhere('r.isSystem = :isSystem')
            ->setParameter('isSystem', false);

        return $builder;
    }

    /**
     * @return Route[]
     */
    public function findAll() {
        $builder = $this->getQueryBuilder();

        $query = $builder->getQuery();

        return $query->getResult();
    }

    public function filter(RouteFilter $filter) {
        $builder = $this->getQueryBuilder();

        if ($filter->getName()) {
            $builder->andWhere('r.name LIKE :name')
                ->setParameter('name', '%'.$filter->getName().'%');
        }

        if ($filter->getRoute()) {
            $builder->andWhere('r.route LIKE :route')
                ->setParameter('route', '%'.$filter->getRoute().'%');
        }

        if ($filter->getParameter()) {
            $builder->andWhere('p.parameter LIKE :parameter')
                ->setParameter('parameter', '%'.$filter->getParameter().'%');
        }

        if ($filter->getParameterName()) {
            $builder->andWhere('p.name LIKE :parameterName')
                ->setParameter('parameterName', '%'.$filter->getParameterName().'%');
        }

        if ($filter->getIsNamed()) {
            switch ($filter->getIsNamed()) {
                case RouteFilter::IS_NAMED_TRUE:
                    $builder->andWhere('r.name IS NOT NULL');
                    break;
                case RouteFilter::IS_NAMED_FALSE:
                    $builder->andWhere('r.name IS NULL');
                    break;
            }
        }

        if ($filter->getIsSystem()) {
            switch ($filter->getIsSystem()) {
                case RouteFilter::IS_SYSTEM_TRUE:
                    $builder->andWhere('r.isSystem = :isSystem')
                        ->setParameter('isSystem', true);
                    break;
                case RouteFilter::IS_SYSTEM_FALSE:
                    $builder->andWhere('r.isSystem = :isSystem')
                        ->setParameter('isSystem', false);
                    break;
            }
        }

        return $this->returnFilteredEntities($builder, $filter, 'r.id');
    }

    public function saveIfNotExists($routeName) {
        $route = $this->findOneByRoute($routeName);
        if ($route) {
            return $route;
        }
        $route = new Route();
        $route->setRoute($routeName);
        $this->save($route);

        return $route;
    }

    public function findOneByRoute($routeName) {
        $builder = $this->getQueryBuilder()
            ->andWhere('r.route = :route')
            ->setParameter('route', $routeName);
        $query = $builder->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * @param $id
     *
     * @return Route
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneById($id): ?Route {
        $builder = $this->getQueryBuilder()
            ->andWhere('r.id = :id')
            ->setParameter('id', $id);
        $query = $builder->getQuery();

        return $query->getOneOrNullResult();
    }

    public function getQueryBuilder() {
        return $this->createQueryBuilder('r')
            ->addSelect('i, p')
            ->leftJoin('r.items', 'i')
            ->leftJoin('r.parameters', 'p');
    }

    public function getRoutesWithParametersQueryBuilder() {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.parameters', 'p')
            ->andHaving('COUNT(p.id) > 0')
            ->groupBy('r.id');
    }

    public function save(Route $route) {
        $em = $this->getEntityManager();
        $em->persist($route);
        $em->flush();
    }

    public function remove(Route $route) {
        $em = $this->getEntityManager();
        foreach ($route->getParameters() as $parameter) {
            $em->remove($parameter);
        }
        $em->remove($route);
        $em->flush();
    }

}
