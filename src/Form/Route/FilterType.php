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

namespace Forci\Bundle\MenuBuilder\Form\Route;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Forci\Bundle\MenuBuilder\Filter\Route\RouteFilter;
use Wucdbm\Bundle\QuickUIBundle\Form\Filter\BaseFilterType;
use Wucdbm\Bundle\QuickUIBundle\Form\Filter\ChoiceFilterType;
use Wucdbm\Bundle\QuickUIBundle\Form\Filter\TextFilterType;

class FilterType extends BaseFilterType {

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('isNamed', ChoiceFilterType::class, [
                'placeholder' => 'Is Named Filter',
                'choices' => [
                    'Only NOT named' => RouteFilter::IS_NAMED_FALSE,
                    'Only named' => RouteFilter::IS_NAMED_TRUE
                ]
            ])
            ->add('isSystem', ChoiceFilterType::class, [
                'placeholder' => 'Type Filter',
                'choices' => [
                    'Public Routes' => RouteFilter::IS_SYSTEM_FALSE,
                    'System Routes' => RouteFilter::IS_SYSTEM_TRUE
                ]
            ])
            ->add('route', TextFilterType::class, [
                'placeholder' => 'Route'
            ])
            ->add('parameter', TextFilterType::class, [
                'placeholder' => 'Parameter'
            ])
            ->add('name', TextFilterType::class, [
                'placeholder' => 'Route Name'
            ])
            ->add('parameterName', TextFilterType::class, [
                'placeholder' => 'Parameter Name'
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => RouteFilter::class
        ]);
    }
}
