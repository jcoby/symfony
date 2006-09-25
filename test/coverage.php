<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../lib/vendor/lime/lime.php');

$h = new lime_harness(new lime_output_color());
$h->base_dir = dirname(__FILE__);

// unit tests
$h->register_glob($h->base_dir.'/unit/*/*Test.php');

// functionnal tests
$h->register_glob($h->base_dir.'/functionnal/*Test.php');

$c = new lime_coverage($h);
$c->extension = '.class.php';
$c->verbose = false;
$c->base_dir = realpath(dirname(__FILE__).'/../lib');
$c->register_glob($c->base_dir.'/*/*.php');
$c->run();