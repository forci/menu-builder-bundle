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

use Forci\Bundle\MenuBuilder\Entity\Menu;
use Forci\Bundle\MenuBuilder\Entity\MenuItem;
use Forci\Bundle\MenuBuilder\Manager\MenuManager;

class MenuExtension extends \Twig_Extension {

    /**
     * @var MenuManager
     */
    protected $manager;

    public function __construct(MenuManager $manager) {
        $this->manager = $manager;
    }

    public function getFilters() {
        return [
            new \Twig_SimpleFilter('getMenu', [$this, 'getMenu']),
            new \Twig_SimpleFilter('menuTopLevelItems', [$this, 'menuTopLevelItems'])
        ];
    }

    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('getMenu', [$this, 'getMenu']),
            new \Twig_SimpleFunction('getMenus', [$this, 'getMenus'])
        ];
    }

    /**
     * @param Menu $menu
     *
     * @return array
     */
    public function menuTopLevelItems(Menu $menu) {
        $items = [];
        /** @var MenuItem $item */
        foreach ($menu->getItems() as $item) {
            if (!$item->getParent()) {
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * @param $id
     *
     * @return Menu|null
     */
    public function getMenu($id) {
        return $this->manager->findOneById($id);
    }

    /**
     * @return Menu[]
     */
    public function getMenus() {
        return $this->manager->findAll();
    }

}
