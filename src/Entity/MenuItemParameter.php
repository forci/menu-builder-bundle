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

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Forci\Bundle\MenuBuilderBundle\Repository\MenuItemParameterRepository")
 * @ORM\Table(name="_forci__menu_builder_menus_items_parameters",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="item_parameter", columns={"item_id", "parameter_id"})
 *      }
 * )
 */
class MenuItemParameter {

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="value", type="string", nullable=true)
     */
    protected $value;

    /**
     * @ORM\Column(name="use_value_from_context", type="boolean", nullable=false)
     */
    protected $useValueFromContext = false;

    /**
     * @ORM\ManyToOne(targetEntity="Forci\Bundle\MenuBuilderBundle\Entity\MenuItem", inversedBy="parameters")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    protected $item;

    /**
     * @ORM\ManyToOne(targetEntity="Forci\Bundle\MenuBuilderBundle\Entity\RouteParameter", inversedBy="values")
     * @ORM\JoinColumn(name="parameter_id", referencedColumnName="id", nullable=false)
     */
    protected $parameter;

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value) {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getUseValueFromContext() {
        return $this->useValueFromContext;
    }

    /**
     * @param mixed $useValueFromContext
     */
    public function setUseValueFromContext($useValueFromContext) {
        $this->useValueFromContext = $useValueFromContext;
    }

    /**
     * @param \Forci\Bundle\MenuBuilderBundle\Entity\MenuItem $item
     *
     * @return $this
     */
    public function setItem(\Forci\Bundle\MenuBuilderBundle\Entity\MenuItem $item) {
        $this->item = $item;

        return $this;
    }

    /**
     * @return \Forci\Bundle\MenuBuilderBundle\Entity\MenuItem
     */
    public function getItem() {
        return $this->item;
    }

    /**
     * @param \Forci\Bundle\MenuBuilderBundle\Entity\RouteParameter $parameter
     *
     * @return $this
     */
    public function setParameter(\Forci\Bundle\MenuBuilderBundle\Entity\RouteParameter $parameter = null) {
        $this->parameter = $parameter;

        return $this;
    }

    /**
     * @return \Forci\Bundle\MenuBuilderBundle\Entity\RouteParameter
     */
    public function getParameter() {
        return $this->parameter;
    }
}
