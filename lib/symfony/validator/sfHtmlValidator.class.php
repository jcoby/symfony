<?php

/*
 * This file is part of the symfony package.
 * (c) 2004, 2005 Fabien Potencier <fabien.potencier@gmail.com>
 * (c) 2004, 2005 Sean Kerr.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@gmail.com>
 * @author     Sean Kerr <skerr@mojavi.org>
 * @version    SVN: $Id: sfHtmlValidator.class.php 432 2005-09-07 12:30:24Z fabien $
 */
class sfHtmlValidator extends sfValidator
{
    public function execute (&$value, &$error)
    {
      if (trim(strip_tags($value)) == '')
      {
        // If page contains an object or an image, it's ok
        if (preg_match('/<img/i', $value) || preg_match('/<object/i', $value))
          return true;
        else
        {
          $error = $this->getParameterHolder()->get('html_error');
          return false;
        }
      }

      return true;
    }

    public function initialize ($context, $parameters = null)
    {
      // initialize parent
      parent::initialize($context);

      // set defaults
      $this->getParameterHolder()->set('html_error', 'Invalid input');

      $this->getParameterHolder()->add($parameters);

      return true;
    }
}

?>