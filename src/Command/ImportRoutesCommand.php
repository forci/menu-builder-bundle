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

namespace Forci\Bundle\MenuBuilder\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Router;

class ImportRoutesCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('wucdbm_menu_builder:import_routes')
            ->setDescription('Import routes from your application');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $container = $this->getContainer();

        $output->write('<info>WucdbmMenuBuilder: Importing routes...</info>');

        /** @var $router Router */
        $router = $container->get('router');

        $manager = $container->get('forci_menu_builder.manager.routes');

        $manager->importRouter($router);

        $output->writeln('<info> Done.</info>');
    }
}
