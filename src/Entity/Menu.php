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
 * @ORM\Entity(repositoryClass="Forci\Bundle\MenuBuilderBundle\Repository\MenuRepository")
 * @ORM\Table(name="_forci__menu_builder_menus")
 */
class Menu {

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
     * @ORM\Column(name="system_name", type="string", nullable=true)
     */
    protected $systemName;

    /**
     * @ORM\Column(name="is_system", type="boolean", nullable=false)
     */
    protected $isSystem = false;

    /**
     * @ORM\OneToMany(targetEntity="Forci\Bundle\MenuBuilderBundle\Entity\MenuItem", mappedBy="menu")
     * @ORM\OrderBy({"ord" = "ASC"})
     */
    protected $items;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->items = new ArrayCollection();
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
     * @return mixed
     */
    public function getSystemName() {
        return $this->systemName;
    }

    /**
     * @param mixed $systemName
     */
    public function setSystemName($systemName) {
        $this->systemName = $systemName;
    }

    /**
     * Add booking.
     *
     * @param \Forci\Bundle\MenuBuilderBundle\Entity\MenuItem $item
     *
     * @return $this
     */
    public function addItem(\Forci\Bundle\MenuBuilderBundle\Entity\MenuItem $item) {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Remove booking.
     *
     * @param \Forci\Bundle\MenuBuilderBundle\Entity\MenuItem $item
     */
    public function removeItem(\Forci\Bundle\MenuBuilderBundle\Entity\MenuItem $item) {
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
