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

use Forci\Bundle\MenuBuilderBundle\Entity\Menu;
use Forci\Bundle\MenuBuilderBundle\Entity\MenuItem;
use Forci\Bundle\MenuBuilderBundle\Entity\MenuItemParameter;
use Forci\Bundle\MenuBuilderBundle\Repository\MenuRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router;

class MenuManager {

    /**
     * @var MenuRepository
     */
    protected $menuRepository;

    /**
     * @var Router
     */
    protected $router;

    /**
     * MenuManager constructor.
     *
     * @param MenuRepository $menuRepository
     */
    public function __construct(MenuRepository $menuRepository, Router $router) {
        $this->menuRepository = $menuRepository;
        $this->router = $router;
    }

    public function create(): Menu {
        return new Menu();
    }

    public function createItem(): MenuItem {
        return new MenuItem();
    }

    public function save(Menu $menu) {
        $this->menuRepository->save($menu);
    }

    public function findOneById(int $id): ?Menu {
        return $this->menuRepository->findOneById($id);
    }

    /**
     * @return Menu[]
     */
    public function findAll() {
        return $this->menuRepository->findAll();
    }

    public function generateMenuItemUrl(MenuItem $item, $type = UrlGeneratorInterface::ABSOLUTE_URL) {
        if ($item->getUrl()) {
            return $item->getUrl();
        }
        $route = $item->getRoute()->getRoute();
        $parameters = [];
        /** @var MenuItemParameter $parameter */
        foreach ($item->getParameters() as $parameter) {
            $key = $parameter->getParameter()->getParameter();
            $parameters[$key] = $this->getValue($parameter, $this->router->getContext());
        }

        return $this->router->generate($route, $parameters, $type);
    }

    protected function getValue(MenuItemParameter $parameter, RequestContext $context) {
        if ($parameter->getUseValueFromContext()) {
            $routeParameter = $parameter->getParameter();
            // If the current context has this parameter, use it
            if ($context->hasParameter($routeParameter->getParameter())) {
                return $context->getParameter($routeParameter->getParameter());
            }
            // Otherwise, use the default value for this route
            // Note: This might change, and upon importing routes anew
            // The URLs generated will now use the new default value
            $default = $routeParameter->getDefaultValue();
            if ($default) {
                return $default;
            }
        }
        // If no value was found in the context or the default route parameter value
        // return the last copy of its default
        return $parameter->getValue();
    }
}
