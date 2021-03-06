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
 * @ORM\Entity(repositoryClass="Forci\Bundle\MenuBuilder\Repository\MenuRepository")
 * @ORM\Table(name="_forci__menu_builder_menus", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
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
     * @ORM\Column(name="is_api_visible", type="boolean", nullable=false)
     */
    protected $isApiVisible = false;

    /**
     * @ORM\Column(name="date_modified", type="datetime", nullable=false)
     */
    protected $dateModified;

    /**
     * @ORM\OneToMany(targetEntity="Forci\Bundle\MenuBuilder\Entity\MenuItem", mappedBy="menu")
     * @ORM\OrderBy({"ord" = "ASC"})
     */
    protected $items;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->items = new ArrayCollection();
        $this->dateModified = new \DateTime();
    }

    /**
     * @return \DateTime
     */
    public function getDateModified() {
        return $this->dateModified;
    }

    /**
     * @param \DateTime $dateModified
     */
    public function setDateModified(\DateTime $dateModified) {
        $this->dateModified = $dateModified;
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

    /**
     * @return mixed
     */
    public function getIsApiVisible() {
        return $this->isApiVisible;
    }

    /**
     * @param mixed $isApiVisible
     */
    public function setIsApiVisible($isApiVisible) {
        $this->isApiVisible = $isApiVisible;
    }
}
