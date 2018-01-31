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

namespace Forci\Bundle\MenuBuilder\Twig;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Forci\Bundle\MenuBuilder\Entity\MenuItem;
use Forci\Bundle\MenuBuilder\Entity\MenuItemParameter;

class MenuItemExtension extends \Twig_Extension {

    /**
     * @var Router
     */
    protected $router;

    public function __construct(Router $router) {
        $this->router = $router;
    }

    public function getFilters() {
        return [
            new \Twig_SimpleFilter('menuItemUrl', [$this, 'menuItemUrl']),
            new \Twig_SimpleFilter('menuItemPath', [$this, 'menuItemPath'])
        ];
    }

    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('menuItemUrl', [$this, 'menuItemUrl']),
            new \Twig_SimpleFunction('menuItemPath', [$this, 'menuItemPath'])
        ];
    }

    public function menuItemUrl(MenuItem $item, $type = UrlGeneratorInterface::ABSOLUTE_URL) {
        return $this->url($item, $type);
    }

    public function menuItemPath(MenuItem $item) {
        return $this->url($item, UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    protected function url(MenuItem $item, $type) {
        $route = $item->getRoute()->getRoute();
        $parameters = [];
        /** @var MenuItemParameter $parameter */
        foreach ($item->getParameters() as $parameter) {
            $key = $parameter->getParameter()->getParameter();
            $parameters[$key] = $this->getValue($parameter);
        }

        return $this->router->generate($route, $parameters, $type);
    }

    protected function getValue(MenuItemParameter $parameter) {
        if ($parameter->getUseValueFromContext()) {
            $routeParameter = $parameter->getParameter();

            // If the current context has this parameter, use it
            if ($this->router->getContext()->hasParameter($routeParameter->getParameter())) {
                return $this->router->getContext()->getParameter($routeParameter->getParameter());
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
