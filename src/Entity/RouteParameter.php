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
 * @ORM\Entity(repositoryClass="Forci\Bundle\MenuBuilder\Repository\RouteParameterRepository")
 * @ORM\Table(name="_forci__menu_builder_routes_parameters", options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"},
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="route_parameter", columns={"route_id", "parameter"})
 *      }
 * )
 */
class RouteParameter {

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="parameter", type="string", nullable=false)
     */
    protected $parameter;

    /**
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(name="requirement", type="string", nullable=true)
     */
    protected $requirement;

    /**
     * @ORM\Column(name="default_value", type="string", nullable=true)
     */
    protected $defaultValue;

    /**
     * @ORM\ManyToOne(targetEntity="Forci\Bundle\MenuBuilder\Entity\Route", inversedBy="parameters")
     * @ORM\JoinColumn(name="route_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    protected $route;

    /**
     * @ORM\ManyToOne(targetEntity="Forci\Bundle\MenuBuilder\Entity\RouteParameterType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id", nullable=false)
     */
    protected $type;

    /**
     * @ORM\OneToMany(targetEntity="Forci\Bundle\MenuBuilder\Entity\MenuItemParameter", mappedBy="parameter")
     */
    protected $values;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->values = new ArrayCollection();
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
    public function getParameter() {
        return $this->parameter;
    }

    /**
     * @param mixed $parameter
     */
    public function setParameter($parameter) {
        $this->parameter = $parameter;
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
    public function getRequirement() {
        return $this->requirement;
    }

    /**
     * @param mixed $requirement
     */
    public function setRequirement($requirement) {
        $this->requirement = $requirement;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue() {
        return $this->defaultValue;
    }

    /**
     * @param mixed $defaultValue
     */
    public function setDefaultValue($defaultValue) {
        $this->defaultValue = $defaultValue;
    }

    /**
     * @param \Forci\Bundle\MenuBuilder\Entity\Route $route
     *
     * @return $this
     */
    public function setRoute(\Forci\Bundle\MenuBuilder\Entity\Route $route) {
        $this->route = $route;

        return $this;
    }

    /**
     * @return \Forci\Bundle\MenuBuilder\Entity\Route
     */
    public function getRoute() {
        return $this->route;
    }

    /**
     * @param \Forci\Bundle\MenuBuilder\Entity\RouteParameterType $type
     *
     * @return $this
     */
    public function setType(\Forci\Bundle\MenuBuilder\Entity\RouteParameterType $type) {
        $this->type = $type;

        return $this;
    }

    /**
     * @return \Forci\Bundle\MenuBuilder\Entity\RouteParameterType
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Add booking.
     *
     * @param \Forci\Bundle\MenuBuilder\Entity\MenuItemParameter $value
     *
     * @return $this
     */
    public function addValue(\Forci\Bundle\MenuBuilder\Entity\MenuItemParameter $value) {
        $this->values[] = $value;

        return $this;
    }

    /**
     * Remove booking.
     *
     * @param \Forci\Bundle\MenuBuilder\Entity\MenuItemParameter $value
     */
    public function removeValue(\Forci\Bundle\MenuBuilder\Entity\MenuItemParameter $value) {
        $this->values->removeElement($value);
    }

    /**
     * Get bookings.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getValues() {
        return $this->values;
    }
}
