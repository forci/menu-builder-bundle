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

namespace Forci\Bundle\MenuBuilderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Forci\Bundle\MenuBuilderBundle\Repository\MenuItemRepository")
 * @ORM\Table(name="_forci__menu_builder_menus_items")
 */
class MenuItem {

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    protected $name;

    /**
     * @ORM\Column(name="ord", type="smallint", options={"unsigned"=true}, nullable=false)
     */
    protected $ord = 0;

    /**
     * @ORM\Column(name="use_current_locale", type="boolean", nullable=false)
     */
    protected $useCurrentLocale = false;

    /**
     * @ORM\ManyToOne(targetEntity="Forci\Bundle\MenuBuilderBundle\Entity\Menu", inversedBy="items")
     * @ORM\JoinColumn(name="menu_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    protected $menu;

    /**
     * @ORM\ManyToOne(targetEntity="Forci\Bundle\MenuBuilderBundle\Entity\Route", inversedBy="items")
     * @ORM\JoinColumn(name="route_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    protected $route;

    /**
     * @ORM\OneToMany(targetEntity="Forci\Bundle\MenuBuilderBundle\Entity\MenuItemParameter", mappedBy="item")
     */
    protected $parameters;

    /**
     * @ORM\ManyToOne(targetEntity="Forci\Bundle\MenuBuilderBundle\Entity\MenuItem", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Forci\Bundle\MenuBuilderBundle\Entity\MenuItem", mappedBy="parent")
     * @ORM\OrderBy({"ord" = "ASC"})
     */
    protected $children;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->parameters = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @param \Forci\Bundle\MenuBuilderBundle\Entity\Menu $menu
     *
     * @return $this
     */
    public function setMenu(\Forci\Bundle\MenuBuilderBundle\Entity\Menu $menu) {
        $this->menu = $menu;

        return $this;
    }

    /**
     * @return \Forci\Bundle\MenuBuilderBundle\Entity\Menu
     */
    public function getMenu() {
        return $this->menu;
    }

    /**
     * @param \Forci\Bundle\MenuBuilderBundle\Entity\Route $route
     *
     * @return $this
     */
    public function setRoute(\Forci\Bundle\MenuBuilderBundle\Entity\Route $route) {
        $this->route = $route;

        return $this;
    }

    /**
     * @return \Forci\Bundle\MenuBuilderBundle\Entity\Route
     */
    public function getRoute() {
        return $this->route;
    }

    /**
     * @param \Forci\Bundle\MenuBuilderBundle\Entity\MenuItemParameter $parameter
     *
     * @return $this
     */
    public function addParameter(\Forci\Bundle\MenuBuilderBundle\Entity\MenuItemParameter $parameter) {
        $this->parameters[] = $parameter;

        return $this;
    }

    /**
     * @param \Forci\Bundle\MenuBuilderBundle\Entity\MenuItemParameter $parameter
     */
    public function removeParameter(\Forci\Bundle\MenuBuilderBundle\Entity\MenuItemParameter $parameter) {
        $this->parameters->removeElement($parameter);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParameters() {
        return $this->parameters;
    }

    /**
     * @param \Forci\Bundle\MenuBuilderBundle\Entity\MenuItem $parent
     *
     * @return $this
     */
    public function setParent(\Forci\Bundle\MenuBuilderBundle\Entity\MenuItem $parent = null) {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return \Forci\Bundle\MenuBuilderBundle\Entity\Route
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * @param \Forci\Bundle\MenuBuilderBundle\Entity\MenuItem $child
     *
     * @return $this
     */
    public function addChild(\Forci\Bundle\MenuBuilderBundle\Entity\MenuItem $child) {
        $this->children[] = $child;

        return $this;
    }

    /**
     * @param \Forci\Bundle\MenuBuilderBundle\Entity\MenuItem $chid
     */
    public function removeChild(\Forci\Bundle\MenuBuilderBundle\Entity\MenuItem $chid) {
        $this->children->removeElement($chid);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren() {
        return $this->children;
    }

    /**
     * @return int
     */
    public function getOrd() {
        return $this->ord;
    }

    /**
     * @param int $ord
     */
    public function setOrd($ord) {
        $this->ord = $ord;
    }

    /**
     * @return mixed
     */
    public function getUseCurrentLocale() {
        return $this->useCurrentLocale;
    }

    /**
     * @param mixed $useCurrentLocale
     */
    public function setUseCurrentLocale($useCurrentLocale) {
        $this->useCurrentLocale = $useCurrentLocale;
    }
}
