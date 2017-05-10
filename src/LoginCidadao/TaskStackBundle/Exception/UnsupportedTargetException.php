<?php
/**
 * This file is part of the login-cidadao project or it's bundles.
 *
 * (c) Guilherme Donato <guilhermednt on github>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace LoginCidadao\TaskStackBundle\Exception;

use LoginCidadao\TaskStackBundle\Model\TaskTargetInterface;

class UnsupportedTargetException extends \RuntimeException
{

    /**
     * UnsupportedTargetException constructor.
     *
     * @param TaskTargetInterface $target
     */
    public function __construct(TaskTargetInterface $target)
    {
        $message = sprintf("Task Target of class %s is not supported.", get_class($target));
        parent::__construct($message);
    }
}