<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Clears log files.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
class sfLogClearTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->aliases = array('log-purge');
    $this->namespace = 'log';
    $this->name = 'clear';
    $this->briefDescription = 'Clears log files';

    $this->detailedDescription = <<<EOF
The [log:clear|INFO] task clears all symfony log files:

  [./symfony log:clear|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $logs = sfFinder::type('file')->ignore_version_control()->in(sfConfig::get('sf_log_dir'));
    $this->getFilesystem()->remove($logs);
  }
}
