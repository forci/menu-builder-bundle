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

use Forci\Bundle\MenuBuilder\Manager\RouteManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Router;

class ImportRoutesCommand extends Command {

    /** @var Router */
    private $router;

    /** @var RouteManager */
    private $manager;

    public function __construct(
        Router $router, RouteManager $manager
    ) {
        parent::__construct('forci_menu_builder:import_routes');
        $this->setDescription('Import routes from your application');
        $this->router = $router;
        $this->manager = $manager;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->write('<info>ForciMenuBuilder: Importing routes...</info>');

        $this->manager->importRouter($this->router);

        $output->writeln('<info> Done.</info>');
    }
}
