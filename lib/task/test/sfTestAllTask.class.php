<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Launches all tests.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
class sfTestAllTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->aliases = array('test-all');
    $this->namespace = 'test';
    $this->name = 'all';
    $this->briefDescription = 'Launches all tests';

    $this->detailedDescription = <<<EOF
The [test:all|INFO] task launches all unit and functional tests:

  [./symfony test:all|INFO]

The task launches all tests found in [test/|COMMENT].

If one or more test fail, you can try to fix the problem by launching
them by hand or with the [test:unit|COMMENT] and [test:functional|COMMENT] task.
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    require_once(sfConfig::get('sf_symfony_lib_dir').'/vendor/lime/lime.php');

    $h = new lime_harness(new lime_output_color());
    $h->base_dir = sfConfig::get('sf_test_dir');

    // register all tests
    $finder = sfFinder::type('file')->ignore_version_control()->follow_link()->name('*Test.php');
    $h->register($finder->in($h->base_dir));

    $h->run();
  }
}
