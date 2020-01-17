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

use Forci\Bundle\MenuBuilder\Entity\MenuItem;
use Forci\Bundle\MenuBuilder\Manager\MenuManager;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class MenuItemExtension extends AbstractExtension {

    /** @var MenuManager */
    protected $manager;

    public function __construct(MenuManager $manager) {
        $this->manager = $manager;
    }

    public function getFilters() {
        return [
            new TwigFilter('menuItemUrl', [$this, 'menuItemUrl']),
            new TwigFilter('menuItemPath', [$this, 'menuItemPath'])
        ];
    }

    public function getFunctions() {
        return [
            new TwigFunction('menuItemUrl', [$this, 'menuItemUrl']),
            new TwigFunction('menuItemPath', [$this, 'menuItemPath'])
        ];
    }

    public function menuItemUrl(MenuItem $item, $type = UrlGeneratorInterface::ABSOLUTE_URL) {
        return $this->url($item, $type);
    }

    public function menuItemPath(MenuItem $item) {
        return $this->url($item, UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    protected function url(MenuItem $item, $type) {
        return $this->manager->generateMenuItemUrl($item, $type);
    }
}
