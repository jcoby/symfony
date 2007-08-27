<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(1, new lime_output_color());

// initialize the storage
$storage = sfStorage::newInstance('sfNoStorage');
$storage->initialize();

$t->ok($storage instanceof sfStorage, 'sfNoStorage is an instance of sfStorage');

// shutdown the storage
$storage->shutdown();