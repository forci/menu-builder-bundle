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

namespace Forci\Bundle\MenuBuilderBundle\Twig;

use Forci\Bundle\MenuBuilderBundle\Entity\Route;
use Forci\Bundle\MenuBuilderBundle\Manager\RouteManager;

class RouteExtension extends \Twig_Extension {

    /**
     * @var RouteManager
     */
    protected $manager;

    public function __construct(RouteManager $manager) {
        $this->manager = $manager;
    }

    public function getFilters() {
        return [
            new \Twig_SimpleFilter('routeName', [$this, 'routeName']),
        ];
    }

    public function routeName(Route $route) {
        if ($route->getName()) {
            return sprintf('%s (%s)', $route->getName(), $route->getRoute());
        }

        $routeName = str_replace(['_', '.'], ' ', $route->getRoute());
        $words = explode(' ', $routeName);
        foreach ($words as $key => $word) {
            $words[$key] = ucfirst($word);
        }

        return sprintf('%s (%s)', implode(' ', $words), $route->getRoute());
    }

}
