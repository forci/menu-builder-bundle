<?php

/*
 * This file is part of the ForciMenuBuilderBundle package.
 *
 * (c) Martin Kirilov <wucdbm@gmail.com>
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
