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

namespace Forci\Bundle\MenuBuilder\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Forci\Bundle\MenuBuilder\Repository\RouteRepository")
 * @ORM\Table(name="_forci__menu_builder_routes", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"},
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="route", columns={"route"})
 *      }
 * )
 */
class Route {

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="route", type="string", nullable=false)
     */
    protected $route;

    /**
     * @ORM\Column(name="path", type="string", nullable=true)
     */
    protected $path;

    /**
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(name="is_system", type="boolean", nullable=false)
     */
    protected $isSystem = false;

    /**
     * @ORM\OneToMany(targetEntity="Forci\Bundle\MenuBuilder\Entity\MenuItem", mappedBy="route")
     */
    protected $items;

    /**
     * @ORM\OneToMany(targetEntity="Forci\Bundle\MenuBuilder\Entity\RouteParameter", mappedBy="route")
     */
    protected $parameters;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->items = new ArrayCollection();
        $this->parameters = new ArrayCollection();
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
    public function getRoute() {
        return $this->route;
    }

    /**
     * @param mixed $route
     */
    public function setRoute($route) {
        $this->route = $route;
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
     * @return mixed
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path) {
        $this->path = $path;
    }

    /**
     * Add booking.
     *
     * @param \Forci\Bundle\MenuBuilder\Entity\MenuItem $item
     *
     * @return $this
     */
    public function addItem(\Forci\Bundle\MenuBuilder\Entity\MenuItem $item) {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Remove booking.
     *
     * @param \Forci\Bundle\MenuBuilder\Entity\MenuItem $item
     */
    public function removeItem(\Forci\Bundle\MenuBuilder\Entity\MenuItem $item) {
        $this->items->removeElement($item);
    }

    /**
     * Get bookings.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItems() {
        return $this->items;
    }

    /**
     * Add booking.
     *
     * @param \Forci\Bundle\MenuBuilder\Entity\RouteParameter $parameter
     *
     * @return $this
     */
    public function addParameter(\Forci\Bundle\MenuBuilder\Entity\RouteParameter $parameter) {
        $this->parameters[] = $parameter;

        return $this;
    }

    /**
     * Remove booking.
     *
     * @param \Forci\Bundle\MenuBuilder\Entity\RouteParameter $parameter
     */
    public function removeParameter(\Forci\Bundle\MenuBuilder\Entity\RouteParameter $parameter) {
        $this->parameters->removeElement($parameter);
    }

    /**
     * Get bookings.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParameters() {
        return $this->parameters;
    }

    /**
     * @return bool
     */
    public function getIsSystem() {
        return $this->isSystem;
    }

    /**
     * @param bool $isSystem
     */
    public function setIsSystem($isSystem) {
        $this->isSystem = $isSystem;
    }
}
