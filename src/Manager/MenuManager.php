<?php

/*
 * This file is part of the ForciMenuBuilderBundle package.
 *
 * (c) Martin Kirilov <wucdbm@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Forci\Bundle\MenuBuilderBundle\Manager;

use Forci\Bundle\MenuBuilderBundle\Entity\Menu;
use Forci\Bundle\MenuBuilderBundle\Repository\MenuRepository;

class MenuManager {

    /**
     * @var MenuRepository
     */
    protected $menuRepository;

    /**
     * MenuManager constructor.
     *
     * @param MenuRepository $menuRepository
     */
    public function __construct(MenuRepository $menuRepository) {
        $this->menuRepository = $menuRepository;
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
}
