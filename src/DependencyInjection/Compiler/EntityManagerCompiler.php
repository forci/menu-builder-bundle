<?php

/*
 * This file is part of the ForciMenuBuilderBundle package.
 *
 * (c) Martin Kirilov <wucdbm@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Forci\Bundle\MenuBuilderBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EntityManagerCompiler implements CompilerPassInterface {

    public function process(ContainerBuilder $container) {
        $managerName = $container->getParameter('forci_menu_builder.config.entity_manager_name');
        $factoryId = sprintf('doctrine.orm.%s_entity_manager', $managerName);

        if (!$container->has($factoryId)) {
            // TODO Figure out a better Exception to throw?
            throw new \Exception(sprintf('Entity Manager "%s" does not exist', $managerName));
        }

        $container->setAlias('forci_menu_builder.entity_manager', $factoryId);
    }
}
