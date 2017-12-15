<?php

/*
 * This file is part of the ForciMenuBuilderBundle package.
 *
 * (c) Martin Kirilov <wucdbm@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Forci\Bundle\MenuBuilderBundle\Form\Menu;

use Forci\Bundle\MenuBuilderBundle\Entity\Menu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'attr' => [
                    'placeholder' => 'Name'
                ]
            ])
            ->add('systemName', TextType::class, [
                'label' => 'System name (serves mostly as a note for developers)',
                'attr' => [
                    'placeholder' => 'System name (serves mostly as a note for developers)'
                ],
                'required' => false
            ])
            ->add('isSystem', CheckboxType::class, [
                'label' => 'This is a system Menu. System menus can not be deleted.',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Menu::class
        ]);
    }
}
