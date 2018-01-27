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

namespace Forci\Bundle\MenuBuilderBundle\Manager;

use Symfony\Component\Routing\Route as SymfonyRoute;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Router;
use Forci\Bundle\MenuBuilderBundle\Entity\MenuItem;
use Forci\Bundle\MenuBuilderBundle\Entity\Route;
use Forci\Bundle\MenuBuilderBundle\Entity\RouteParameter;
use Forci\Bundle\MenuBuilderBundle\Repository\MenuItemRepository;
use Forci\Bundle\MenuBuilderBundle\Repository\RouteParameterRepository;
use Forci\Bundle\MenuBuilderBundle\Repository\RouteParameterTypeRepository;
use Forci\Bundle\MenuBuilderBundle\Repository\RouteRepository;

class RouteManager {

    /**
     * @var RouteRepository
     */
    protected $routeRepo;

    /**
     * @var RouteParameterRepository
     */
    protected $routeParameterRepo;

    /**
     * @var RouteParameterTypeRepository
     */
    protected $routeParameterTypeRepo;

    /**
     * @var MenuItemRepository
     */
    protected $menuItemRepo;

    /**
     * RouteManager constructor.
     *
     * @param RouteRepository              $routeRepo
     * @param RouteParameterRepository     $routeParameterRepo
     * @param RouteParameterTypeRepository $routeParameterTypeRepo
     * @param MenuItemRepository           $menuItemRepository
     */
    public function __construct(RouteRepository $routeRepo, RouteParameterRepository $routeParameterRepo,
                                RouteParameterTypeRepository $routeParameterTypeRepo, MenuItemRepository $menuItemRepository) {
        $this->routeRepo = $routeRepo;
        $this->routeParameterRepo = $routeParameterRepo;
        $this->routeParameterTypeRepo = $routeParameterTypeRepo;
        $this->menuItemRepo = $menuItemRepository;
    }

    /**
     * @return Route[]
     */
    public function findAll() {
        return $this->routeRepo->findAll();
    }

    /**
     * @param Router $router
     */
    public function importRouter(Router $router) {
        $routes = $this->findAll();

        $toRemove = [];
        /** @var Route $route */
        foreach ($routes as $route) {
            $toRemove[$route->getRoute()] = $route;
        }

        /** @var $collection RouteCollection */
        $collection = $router->getRouteCollection();

        $allRoutes = $collection->all();

        /**
         * @var string
         * @var SymfonyRoute $route
         */
        foreach ($allRoutes as $routeName => $route) {
            $this->importRoute($routeName, $route);
            unset($toRemove[$routeName]);
        }

        /**
         * @var string
         * @var Route  $route
         */
        foreach ($toRemove as $routeName => $route) {
            /** @var MenuItem $item */
            foreach ($route->getItems() as $item) {
                $parent = $item->getParent();
                /** @var MenuItem $child */
                foreach ($item->getChildren() as $child) {
                    $child->setParent($parent);
                    $this->menuItemRepo->save($child);
                }
            }
            $this->removeRoute($route);
        }
    }

    /**
     * @param $routeName
     * @param SymfonyRoute $route
     */
    public function importRoute($routeName, SymfonyRoute $route) {
        $routeEntity = $this->routeRepo->saveIfNotExists($routeName);
        $routeEntity->setPath($route->getPath());
        $this->routeRepo->save($routeEntity);
        $compiledRoute = $route->compile();
        $requiredType = $this->routeParameterTypeRepo->findRequiredType();

        $parametersToRemove = [];
        /** @var RouteParameter $parameterEntity */
        foreach ($routeEntity->getParameters() as $parameterEntity) {
            $parametersToRemove[$parameterEntity->getId()] = $parameterEntity;
        }

        foreach ($compiledRoute->getVariables() as $parameter) {
            $parameterEntity = $this->routeParameterRepo->saveIfNotExists($routeEntity, $parameter, $requiredType);
            $parameterEntity->setRequirement($route->getRequirement($parameter));
            $parameterEntity->setDefaultValue($route->getDefault($parameter));
            $this->routeParameterRepo->save($parameterEntity);
            unset($parametersToRemove[$parameterEntity->getId()]);
        }

//        if ($route->getSchemes()) {
//            $schemeParameterEntity = $this->routeParameterRepo->saveIfNotExists($routeEntity, '_scheme', $requiredType);
//            $schemeParameterEntity->setRequirement(implode('|', $route->getSchemes()));
//            $schemeParameterEntity->setDefaultValue($route->getDefault('_scheme'));
//            $this->routeParameterRepo->save($schemeParameterEntity);
//            unset($parametersToRemove[$schemeParameterEntity->getId()]);
//        } else {
//            $schemeParameterEntity = $this->routeParameterRepo->findOneByRouteAndParameter($routeEntity, '_scheme');
//            if ($schemeParameterEntity) {
//                $this->routeParameterRepo->remove($schemeParameterEntity);
//            }
//        }

        /** @var RouteParameter $parameterEntity */
        foreach ($parametersToRemove as $parameterEntity) {
            $this->routeParameterRepo->remove($parameterEntity);
        }
    }

    public function removeRoute(Route $route) {
        $this->routeRepo->remove($route);
    }
}
