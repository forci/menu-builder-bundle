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

namespace Forci\Bundle\MenuBuilderBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Wucdbm\Bundle\QuickUIBundle\Repository\QuickUIRepositoryTrait;

class MenuItemParameterRepository extends EntityRepository {

    use QuickUIRepositoryTrait;
}
