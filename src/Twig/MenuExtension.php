<?php

/*
 * This file is part of the ForciMenuBuilderBundle package.
 *
 * (c) Martin Kirilov <wucdbm@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Forci\Bundle\MenuBuilderBundle\Twig;

use Forci\Bundle\MenuBuilderBundle\Entity\Menu;
use Forci\Bundle\MenuBuilderBundle\Entity\MenuItem;
use Forci\Bundle\MenuBuilderBundle\Manager\MenuManager;

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

    public function getAlias() {
        return 'forci_menu_builder_menu';
    }

    public function getName() {
        return 'forci_menu_builder_menu';
    }
}
