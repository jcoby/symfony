<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * PartialHelper.
 *
 * @package    symfony
 * @subpackage helper
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */

/**
 * Evaluates and echoes a component slot.
 * The component name is deduced from the definition of the view.yml
 * For a variable to be accessible to the component and its partial, 
 * it has to be passed in the second argument.
 *
 * <b>Example:</b>
 * <code>
 *  include_component_slot('sidebar', array('myvar' => 12345));
 * </code>
 *
 * @param  string slot name
 * @param  array variables to be made accessible to the component
 * @return void
 * @see    get_component_slot, include_partial, include_component
 */
function include_component_slot($name, $vars = array())
{
  echo get_component_slot($name, $vars);
}

/**
 * Evaluates and returns a component slot.
 * The syntax is similar to the one of include_component_slot.
 *
 * <b>Example:</b>
 * <code>
 *  echo get_component_slot('sidebar', array('myvar' => 12345));
 * </code>
 *
 * @param  string slot name
 * @param  array variables to be made accessible to the component
 * @return string result of the component execution
 * @see    get_component_slot, include_partial, include_component
 */
function get_component_slot($name, $vars = array())
{
  $context = sfContext::getInstance();

  $actionStackEntry = $context->getController()->getActionStack()->getLastEntry();
  $viewInstance     = $actionStackEntry->getViewInstance();

  if (!$viewInstance->hasComponentSlot($name))
  {
    // cannot find component slot
    $error = 'The component slot "%s" is not set';
    $error = sprintf($error, $name);

    throw new sfConfigurationException($error);
  }

  if ($componentSlot = $viewInstance->getComponentSlot($name))
  {
    return get_component($componentSlot[0], $componentSlot[1], $vars);
  }
}

/**
 * Evaluates and echoes a component.
 * For a variable to be accessible to the component and its partial, 
 * it has to be passed in the third argument.
 *
 * <b>Example:</b>
 * <code>
 *  include_component('mymodule', 'mypartial', array('myvar' => 12345));
 * </code>
 *
 * @param  string module name
 * @param  string component name
 * @param  array variables to be made accessible to the component
 * @return void
 * @see    get_component, include_partial, include_component_slot
 */
function include_component($moduleName, $componentName, $vars = array())
{
  echo get_component($moduleName, $componentName, $vars);
}

/**
 * Evaluates and returns a component.
 * The syntax is similar to the one of include_component.
 *
 * <b>Example:</b>
 * <code>
 *  echo get_component('mymodule', 'mypartial', array('myvar' => 12345));
 * </code>
 *
 * @param  string module name
 * @param  string component name
 * @param  array variables to be made accessible to the component
 * @return string result of the component execution
 * @see    include_component
 */
function get_component($moduleName, $componentName, $vars = array())
{
  $context = sfContext::getInstance();
  $actionName = '_'.$componentName;

  // check cache
  $cacheKey = md5(serialize($vars));
  $uri = $moduleName.'/'.$actionName.'?key='.$cacheKey;
  if (null !== $retval = _get_cache($context, $moduleName, $actionName, $uri, $cacheKey))
  {
    return $retval;
  }

  $controller = $context->getController();

  if (!$controller->componentExists($moduleName, $componentName))
  {
    // cannot find component
    $error = 'The component does not exist: "%s", "%s"';
    $error = sprintf($error, $moduleName, $componentName);

    throw new sfConfigurationException($error);
  }

  // create an instance of the action
  $componentInstance = $controller->getComponent($moduleName, $componentName);

  // initialize the action
  if (!$componentInstance->initialize($context))
  {
    // component failed to initialize
    $error = 'Component initialization failed for module "%s", component "%s"';
    $error = sprintf($error, $moduleName, $componentName);

    throw new sfInitializationException($error);
  }

  // load component's module config file
  require(sfConfigCache::getInstance()->checkConfig(sfConfig::get('sf_app_module_dir_name').'/'.$moduleName.'/'.sfConfig::get('sf_app_module_config_dir_name').'/module.yml'));

  $componentInstance->getVarHolder()->add($vars);

  // dispatch component
  $componentToRun = 'execute'.ucfirst($componentName);
  if (!method_exists($componentInstance, $componentToRun))
  {
    if (!method_exists($componentInstance, 'execute'))
    {
      // component not found
      $error = 'sfComponent initialization failed for module "%s", component "%s"';
      $error = sprintf($error, $moduleName, $componentName);
      throw new sfInitializationException($error);
    }

    $componentToRun = 'execute';
  }

  if (sfConfig::get('sf_logging_active')) $context->getLogger()->info('{PartialHelper} call "'.$moduleName.'->'.$componentToRun.'()'.'"');

  // run component
  $sf_logging_active = sfConfig::get('sf_logging_active');
  if ($sf_logging_active)
  {
    $timer = sfTimerManager::getTimer(sprintf('Component "%s/%s"', $moduleName, $componentName));
  }

  $retval = $componentInstance->$componentToRun();

  if ($sf_logging_active)
  {
    $timer->addTime();
  }

  if ($retval != sfView::NONE)
  {
    // get component vars
    $componentVars = $componentInstance->getVarHolder()->getAll();

    // include partial
    return get_partial($moduleName.'/'.$componentName, $componentVars);
  }
}

/**
 * Evaluates and echoes a partial.
 * The partial name is composed as follows: 'mymodule/mypartial'.
 * The partial file name is _mypartial.php and is looked for in modules/mymodule/templates/.
 * If the partial name doesn't include a module name,
 * then the partial file is searched for in the caller's template/ directory.
 * If the module name is 'global', then the partial file is looked for in myapp/templates/.
 * For a variable to be accessible to the partial, it has to be passed in the second argument.
 *
 * <b>Example:</b>
 * <code>
 *  include_partial('mypartial', array('myvar' => 12345));
 * </code>
 *
 * @param  string partial name
 * @param  array variables to be made accessible to the partial
 * @return void
 * @see    get_partial, include_component
 */
function include_partial($templateName, $vars = array())
{
  echo get_partial($templateName, $vars);
}

/**
 * Evaluates and returns a partial.
 * The syntax is similar to the one of include_partial
 *
 * <b>Example:</b>
 * <code>
 *  echo get_partial('mypartial', array('myvar' => 12345));
 * </code>
 *
 * @param  string partial name
 * @param  array variables to be made accessible to the partial
 * @return string result of the partial execution
 * @see    include_partial
 */
function get_partial($templateName, $vars = array())
{
  $sf_logging_active = sfConfig::get('sf_logging_active');
  if ($sf_logging_active)
  {
    $timer = sfTimerManager::getTimer(sprintf('Partial "%s"', $templateName));
  }

  $context = sfContext::getInstance();

  // partial is in another module?
  $sep = strpos($templateName, '/');
  if ($sep)
  {
    $moduleName   = substr($templateName, 0, $sep);
    $templateName = substr($templateName, $sep + 1);
  }
  else
  {
    $moduleName = $context->getActionStack()->getLastEntry()->getModuleName();
  }
  $actionName = '_'.$templateName;

  $cacheKey = md5(serialize($vars));
  $uri = $moduleName.'/'.$actionName.'?key='.$cacheKey;
  if (null !== $retval = _get_cache($context, $moduleName, $actionName, $uri, $cacheKey))
  {
    if ($sf_logging_active)
    {
      $timer->addTime();
    }

    return $retval;
  }

  $controller = $context->getController();

  // get original render mode
  $renderMode = $controller->getRenderMode();

  // set render mode to var
  $controller->setRenderMode(sfView::RENDER_VAR);

  // get the view instance
  $viewInstance = $controller->getView($moduleName, $actionName, '');

  // not configurable
  $viewInstance->setConfigurable(false);

  // initialize the view
  if (!$viewInstance->initialize($context, $moduleName, $actionName, ''))
  {
    // view failed to initialize
    $error = 'View initialization failed for module "%s"';
    $error = sprintf($error, $moduleName);

    throw new sfInitializationException($error);
  }

  $viewInstance->setTemplate($actionName.$viewInstance->getExtension());
  if ('global' == $moduleName)
  {
    $viewInstance->setDirectory(sfConfig::get('sf_app_template_dir'));
  }

  // no decorator
  $viewInstance->setDecorator(false);

  // view initialization completed successfully
  $viewInstance->execute();

  // render the partial template
  $retval = $viewInstance->render($vars);

  // put render mode back
  $controller->setRenderMode($renderMode);

  if (sfConfig::get('sf_cache'))
  {
    if ($retval !== null)
    {
      $cacheManager = $context->getViewCacheManager();

      $saved = $cacheManager->set($retval, $uri, 'slot');

      if (sfConfig::get('sf_web_debug') && $saved)
      {
        $retval = sfWebDebug::getInstance()->decorateContentWithDebug($uri, 'slot', $retval, true);
      }
    }

    if ($sf_logging_active && $saved)
    {
      $context->getLogger()->info(sprintf('{PartialHelper} save slot "%s - %s" in cache', $uri, $cacheKey));
    }
  }

  if ($sf_logging_active)
  {
    $timer->addTime();
  }

  return $retval;
}

function _get_cache($context, $moduleName, $actionName, $uri, $cacheKey)
{
  if (!sfConfig::get('sf_cache'))
  {
    return null;
  }

  $cacheManager = $context->getViewCacheManager();

  // register our cache configuration
  $cacheConfigFile = $moduleName.'/'.sfConfig::get('sf_app_module_config_dir_name').'/cache.yml';
  if (is_readable(sfConfig::get('sf_app_module_dir').'/'.$cacheConfigFile))
  {
    require(sfConfigCache::getInstance()->checkConfig(sfConfig::get('sf_app_module_dir_name').'/'.$cacheConfigFile));
  }

  $retval = $cacheManager->get($uri, 'slot');

  if (sfConfig::get('sf_logging_active') && $cacheManager->isCacheable($uri, 'slot'))
  {
    $context->getLogger()->info(sprintf('{PartialHelper} cache for "%s" %s', $uri, ($retval !== null ? 'exists' : 'does not exist')));
  }

  if ($retval !== null)
  {
    if (sfConfig::get('sf_web_debug'))
    {
      $retval = sfWebDebug::getInstance()->decorateContentWithDebug($uri, 'slot', $retval, false);
    }

    return $retval;
  }
}

/**
 * Begins the capturing of the slot.
 *
 * @param  string slot name
 * @return void
 * @see    end_slot
 */
function slot($name)
{
  $context = sfContext::getInstance();
  $response = $context->getResponse();

  $slots = $response->getParameter('slots', array(), 'symfony/view/sfView/slot');
  $slot_names = $response->getParameter('slot_names', array(), 'symfony/view/sfView/slot');
  if (in_array($name, $slot_names))
  {
    throw new sfCacheException(sprintf('A slot named "%s" is already started.', $name));
  }

  $slot_names[] = $name;
  $slots[$name] = '';

  $response->setParameter('slots', $slots, 'symfony/view/sfView/slot');
  $response->setParameter('slot_names', $slot_names, 'symfony/view/sfView/slot');

  if (sfConfig::get('sf_logging_active'))
  {
    $context->getLogger()->info(sprintf('{PartialHelper} set slot "%s"', $name));
  }

  ob_start();
  ob_implicit_flush(0);
}

/**
 * Stops the content capture and save the content in the slot.
 *
 * @return void
 * @see    slot
 */
function end_slot()
{
  $content = ob_get_clean();

  $response = sfContext::getInstance()->getResponse();
  $slots = $response->getParameter('slots', array(), 'symfony/view/sfView/slot');
  $slot_names = $response->getParameter('slot_names', array(), 'symfony/view/sfView/slot');
  if (!$slot_names)
  {
    throw new sfCacheException('No slot started.');
  }

  $name = array_pop($slot_names);
  $slots[$name] = $content;

  $response->setParameter('slots', $slots, 'symfony/view/sfView/slot');
  $response->setParameter('slot_names', $slot_names, 'symfony/view/sfView/slot');
}

/**
 * Returns true if the slot exists.
 *
 * @param  string slot name
 * @return boolean true, if the slot exists
 * @see    get_slot, include_slot
 */
function has_slot($name)
{
  $response = sfContext::getInstance()->getResponse();
  $slots = $response->getParameter('slots', array(), 'symfony/view/sfView/slot');

  return array_key_exists($name, $slots);
}

/**
 * Evaluates and echoes a slot.
 *
 * <b>Example:</b>
 * <code>
 *  include_slot('navigation');
 * </code>
 *
 * @param  string slot name
 * @return void
 * @see    has_slot, get_slot
 */
function include_slot($name)
{
  echo get_slot($name);
}

/**
 * Evaluates and returns a slot.
 *
 * <b>Example:</b>
 * <code>
 *  echo get_slot('navigation');
 * </code>
 *
 * @param  string slot name
 * @return string content of the slot
 * @see    has_slot, include_slot
 */
function get_slot($name)
{
  $context = sfContext::getInstance();
  $slots = $context->getResponse()->getParameter('slots', array(), 'symfony/view/sfView/slot');

  if (sfConfig::get('sf_logging_active'))
  {
    $context->getLogger()->info(sprintf('{PartialHelper} get slot "%s"', $name));
  }

  return isset($slots[$name]) ? $slots[$name] : '';
}
