<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE_YAML
 * file that was distributed with this source code.
 */

namespace deploy\config\Exception;

use deploy\config\Exception\RuntimeException;

/**
 * Exception class thrown when an error occurs during dumping.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @api
 */
class DumpException extends RuntimeException
{
}
