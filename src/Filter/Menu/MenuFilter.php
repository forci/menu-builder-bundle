<?php

/*
 * This file is part of the ForciMenuBuilderBundle package.
 *
 * (c) Martin Kirilov <wucdbm@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Forci\Bundle\MenuBuilderBundle\Filter\Menu;

use Forci\Bundle\MenuBuilderBundle\Entity\Route;
use Wucdbm\Bundle\QuickUIBundle\Filter\AbstractFilter;

class MenuFilter extends AbstractFilter {

    /** @var string */
    protected $name;

    /** @var Route */
    protected $route;

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return Route
     */
    public function getRoute() {
        return $this->route;
    }

    /**
     * @param Route $route
     */
    public function setRoute($route) {
        $this->route = $route;
    }
}
