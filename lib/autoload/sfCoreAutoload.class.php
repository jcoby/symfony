<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * The current symfony version.
 */
define('SYMFONY_VERSION', '1.1.0-DEV');

/**
 * sfCoreAutoload class.
 *
 * This class is a singleton as PHP seems to be unable to register 2 autoloaders that are instances
 * of the same class (why?).
 *
 * @package    symfony
 * @subpackage util
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
class sfCoreAutoload
{
  static protected
    $instance = null;

  protected function __construct()
  {
  }

  /**
   * Retrieves the singleton instance of this class.
   *
   * @return sfCoreAutoload A sfCoreAutoload implementation instance.
   */
  static public function getInstance()
  {
    if (!isset(self::$instance))
    {
      self::$instance = new sfCoreAutoload();
    }

    return self::$instance;
  }

  /**
   * Register sfCoreAutoload in spl autoloader.
   *
   * @return void
   */
  static public function register()
  {
    ini_set('unserialize_callback_func', 'spl_autoload_call');
    if (!spl_autoload_register(array(self::getInstance(), 'autoload')))
    {
      throw new sfException(sprintf('Unable to register %s::autoload as an autoloading method.', get_class(self::getInstance())));
    }
  }

  /**
   * Unregister sfCoreAutoload from spl autoloader.
   *
   * @return void
   */
  static public function unregister()
  {
    spl_autoload_unregister(array(self::getInstance(), 'autoload'));
  }

  /**
   * Handles autoloading of classes.
   *
   * @param  string  A class name.
   *
   * @return boolean Returns true if the class has been loaded
   */
  public function autoload($class)
  {
    if (!isset($this->classes[$class]))
    {
      return false;
    }

    require dirname(__FILE__).'/../'.$this->classes[$class].'/'.$class.'.class.php';

    return true;
  }

  /**
   * Rebuilds the association array between class names and paths.
   *
   * This method overrides this file (__FILE__)
   */
  static public function make()
  {
    $libDir = realpath(dirname(__FILE__).'/..');
    require_once $libDir.'/util/sfFinder.class.php';

    $files = sfFinder::type('file')
      ->prune('plugins')
      ->prune('vendor')
      ->prune('skeleton')
      ->prune('default')
      ->name('*\.class\.php')
      ->in($libDir)
    ;

    $classes = array();
    foreach ($files as $file)
    {
      $classes[basename($file, '.class.php')] = str_replace($libDir.'/', '', dirname($file));
    }

    $content = preg_replace('/protected \$classes = array *\(.*?\)/s', 'protected $classes = '.var_export($classes, true), file_get_contents(__FILE__));

    file_put_contents(__FILE__, $content);
  }

  // Don't edit this property by hand.
  // To update it, use sfCoreAutoload::make()
  protected $classes = array (
  'sfAction' => 'action',
  'sfActions' => 'action',
  'sfActionStack' => 'action',
  'sfActionStackEntry' => 'action',
  'sfComponent' => 'action',
  'sfComponents' => 'action',
  'sfData' => 'addon',
  'sfPager' => 'addon',
  'sfAutoload' => 'autoload',
  'sfCoreAutoload' => 'autoload',
  'sfSimpleAutoload' => 'autoload',
  'sfAPCCache' => 'cache',
  'sfCache' => 'cache',
  'sfEAcceleratorCache' => 'cache',
  'sfFileCache' => 'cache',
  'sfFunctionCache' => 'cache',
  'sfMemcacheCache' => 'cache',
  'sfNoCache' => 'cache',
  'sfSQLiteCache' => 'cache',
  'sfXCacheCache' => 'cache',
  'sfAnsiColorFormatter' => 'command',
  'sfCommandApplication' => 'command',
  'sfCommandArgument' => 'command',
  'sfCommandArgumentSet' => 'command',
  'sfCommandArgumentsException' => 'command',
  'sfCommandException' => 'command',
  'sfCommandLogger' => 'command',
  'sfCommandManager' => 'command',
  'sfCommandOption' => 'command',
  'sfCommandOptionSet' => 'command',
  'sfFormatter' => 'command',
  'sfSymfonyCommandApplication' => 'command',
  'sfApplicationConfiguration' => 'config',
  'sfAutoloadConfigHandler' => 'config',
  'sfCacheConfigHandler' => 'config',
  'sfCompileConfigHandler' => 'config',
  'sfConfig' => 'config',
  'sfConfigCache' => 'config',
  'sfConfigHandler' => 'config',
  'sfDatabaseConfigHandler' => 'config',
  'sfDefineEnvironmentConfigHandler' => 'config',
  'sfFactoryConfigHandler' => 'config',
  'sfFilterConfigHandler' => 'config',
  'sfGeneratorConfigHandler' => 'config',
  'sfLoader' => 'config',
  'sfProjectConfiguration' => 'config',
  'sfRootConfigHandler' => 'config',
  'sfRoutingConfigHandler' => 'config',
  'sfSecurityConfigHandler' => 'config',
  'sfSimpleYamlConfigHandler' => 'config',
  'sfViewConfigHandler' => 'config',
  'sfYamlConfigHandler' => 'config',
  'sfConsoleController' => 'controller',
  'sfController' => 'controller',
  'sfFrontWebController' => 'controller',
  'sfWebController' => 'controller',
  'sfDatabase' => 'database',
  'sfDatabaseManager' => 'database',
  'sfMySQLDatabase' => 'database',
  'sfPDODatabase' => 'database',
  'sfPostgreSQLDatabase' => 'database',
  'sfDebug' => 'debug',
  'sfTimer' => 'debug',
  'sfTimerManager' => 'debug',
  'sfWebDebug' => 'debug',
  'sfEvent' => 'event',
  'sfEventDispatcher' => 'event',
  'sfCacheException' => 'exception',
  'sfConfigurationException' => 'exception',
  'sfControllerException' => 'exception',
  'sfDatabaseException' => 'exception',
  'sfError404Exception' => 'exception',
  'sfException' => 'exception',
  'sfFactoryException' => 'exception',
  'sfFileException' => 'exception',
  'sfFilterException' => 'exception',
  'sfForwardException' => 'exception',
  'sfInitializationException' => 'exception',
  'sfParseException' => 'exception',
  'sfRenderException' => 'exception',
  'sfSecurityException' => 'exception',
  'sfStopException' => 'exception',
  'sfStorageException' => 'exception',
  'sfViewException' => 'exception',
  'sfBasicSecurityFilter' => 'filter',
  'sfCacheFilter' => 'filter',
  'sfCommonFilter' => 'filter',
  'sfExecutionFilter' => 'filter',
  'sfFilter' => 'filter',
  'sfFilterChain' => 'filter',
  'sfRenderingFilter' => 'filter',
  'sfForm' => 'form',
  'sfFormField' => 'form',
  'sfFormFieldSchema' => 'form',
  'sfAdminGenerator' => 'generator',
  'sfCrudGenerator' => 'generator',
  'sfGenerator' => 'generator',
  'sfGeneratorManager' => 'generator',
  'sfRichTextEditor' => 'helper',
  'sfRichTextEditorFCK' => 'helper',
  'sfRichTextEditorTinyMCE' => 'helper',
  'sfI18nApplicationExtract' => 'i18n/extract',
  'sfI18nExtract' => 'i18n/extract',
  'sfI18nExtractorInterface' => 'i18n/extract',
  'sfI18nModuleExtract' => 'i18n/extract',
  'sfI18nPhpExtractor' => 'i18n/extract',
  'sfI18nYamlExtractor' => 'i18n/extract',
  'sfI18nYamlGeneratorExtractor' => 'i18n/extract',
  'sfI18nYamlValidateExtractor' => 'i18n/extract',
  'TGettext' => 'i18n/Gettext',
  'sfChoiceFormat' => 'i18n',
  'sfCultureInfo' => 'i18n',
  'sfDateFormat' => 'i18n',
  'sfDateTimeFormatInfo' => 'i18n',
  'sfI18N' => 'i18n',
  'sfIMessageSource' => 'i18n',
  'sfMessageFormat' => 'i18n',
  'sfMessageSource' => 'i18n',
  'sfMessageSource_Aggregate' => 'i18n',
  'sfMessageSource_Database' => 'i18n',
  'sfMessageSource_File' => 'i18n',
  'sfMessageSource_gettext' => 'i18n',
  'sfMessageSource_MySQL' => 'i18n',
  'sfMessageSource_SQLite' => 'i18n',
  'sfMessageSource_XLIFF' => 'i18n',
  'sfNumberFormat' => 'i18n',
  'sfNumberFormatInfo' => 'i18n',
  'sfAggregateLogger' => 'log',
  'sfConsoleLogger' => 'log',
  'sfFileLogger' => 'log',
  'sfLogger' => 'log',
  'sfLoggerInterface' => 'log',
  'sfLoggerWrapper' => 'log',
  'sfNoLogger' => 'log',
  'sfStreamLogger' => 'log',
  'sfWebDebugLogger' => 'log',
  'sfPearDownloader' => 'plugin',
  'sfPearEnvironment' => 'plugin',
  'sfPearFrontendPlugin' => 'plugin',
  'sfPearRest' => 'plugin',
  'sfPearRest10' => 'plugin',
  'sfPearRest11' => 'plugin',
  'sfPearRestPlugin' => 'plugin',
  'sfPluginDependencyException' => 'plugin',
  'sfPluginException' => 'plugin',
  'sfPluginManager' => 'plugin',
  'sfPluginRecursiveDependencyException' => 'plugin',
  'sfPluginRestException' => 'plugin',
  'sfSymfonyPluginManager' => 'plugin',
  'sfConsoleRequest' => 'request',
  'sfRequest' => 'request',
  'sfWebRequest' => 'request',
  'sfConsoleResponse' => 'response',
  'sfResponse' => 'response',
  'sfWebResponse' => 'response',
  'sfNoRouting' => 'routing',
  'sfPathInfoRouting' => 'routing',
  'sfPatternRouting' => 'routing',
  'sfRouting' => 'routing',
  'sfDatabaseSessionStorage' => 'storage',
  'sfMySQLSessionStorage' => 'storage',
  'sfNoStorage' => 'storage',
  'sfPDOSessionStorage' => 'storage',
  'sfPostgreSQLSessionStorage' => 'storage',
  'sfSessionStorage' => 'storage',
  'sfSessionTestStorage' => 'storage',
  'sfStorage' => 'storage',
  'sfCacheClearTask' => 'task/cache',
  'sfConfigureAuthorTask' => 'task/configure',
  'sfConfigureDatabaseTask' => 'task/configure',
  'sfGenerateAppTask' => 'task/generator',
  'sfGenerateModuleTask' => 'task/generator',
  'sfGenerateProjectTask' => 'task/generator',
  'sfGeneratorBaseTask' => 'task/generator',
  'sfHelpTask' => 'task/help',
  'sfListTask' => 'task/help',
  'sfI18nExtractTask' => 'task/i18n',
  'sfI18nFindTask' => 'task/i18n',
  'sfLogClearTask' => 'task/log',
  'sfLogRotateTask' => 'task/log',
  'sfPluginAddChannelTask' => 'task/plugin',
  'sfPluginBaseTask' => 'task/plugin',
  'sfPluginInstallTask' => 'task/plugin',
  'sfPluginListTask' => 'task/plugin',
  'sfPluginUninstallTask' => 'task/plugin',
  'sfPluginUpgradeTask' => 'task/plugin',
  'sfProjectClearControllersTask' => 'task/project',
  'sfProjectDeployTask' => 'task/project',
  'sfProjectDisableTask' => 'task/project',
  'sfProjectEnableTask' => 'task/project',
  'sfProjectFreezeTask' => 'task/project',
  'sfProjectPermissionsTask' => 'task/project',
  'sfProjectUnfreezeTask' => 'task/project',
  'sfUpgradeTo11Task' => 'task/project',
  'sfComponentUpgrade' => 'task/project/upgrade1.1',
  'sfConfigFileUpgrade' => 'task/project/upgrade1.1',
  'sfConfigUpgrade' => 'task/project/upgrade1.1',
  'sfEnvironmentUpgrade' => 'task/project/upgrade1.1',
  'sfFactoriesUpgrade' => 'task/project/upgrade1.1',
  'sfFlashUpgrade' => 'task/project/upgrade1.1',
  'sfLoggerUpgrade' => 'task/project/upgrade1.1',
  'sfPropelUpgrade' => 'task/project/upgrade1.1',
  'sfSingletonUpgrade' => 'task/project/upgrade1.1',
  'sfTestUpgrade' => 'task/project/upgrade1.1',
  'sfUpgrade' => 'task/project/upgrade1.1',
  'sfViewCacheManagerUpgrade' => 'task/project/upgrade1.1',
  'sfWebDebugUpgrade' => 'task/project/upgrade1.1',
  'sfBaseTask' => 'task',
  'sfCommandApplicationTask' => 'task',
  'sfFilesystem' => 'task',
  'sfTask' => 'task',
  'sfTestAllTask' => 'task/test',
  'sfTestFunctionalTask' => 'task/test',
  'sfTestUnitTask' => 'task/test',
  'sfTestBrowser' => 'test',
  'sfBasicSecurityUser' => 'user',
  'sfSecurityUser' => 'user',
  'sfUser' => 'user',
  'sfBrowser' => 'util',
  'sfCallable' => 'util',
  'sfContext' => 'util',
  'sfDomCssSelector' => 'util',
  'sfFinder' => 'util',
  'sfInflector' => 'util',
  'sfNamespacedParameterHolder' => 'util',
  'sfParameterHolder' => 'util',
  'sfToolkit' => 'util',
  'sfValidatorI18nChoiceCountry' => 'validator/i18n',
  'sfValidatorI18nChoiceLanguage' => 'validator/i18n',
  'sfValidatorAnd' => 'validator',
  'sfValidatorBase' => 'validator',
  'sfValidatorBoolean' => 'validator',
  'sfValidatorCallback' => 'validator',
  'sfValidatorChoice' => 'validator',
  'sfValidatorChoiceMany' => 'validator',
  'sfValidatorCSRFToken' => 'validator',
  'sfValidatorDate' => 'validator',
  'sfValidatorDateTime' => 'validator',
  'sfValidatorDecorator' => 'validator',
  'sfValidatorEmail' => 'validator',
  'sfValidatorError' => 'validator',
  'sfValidatorErrorSchema' => 'validator',
  'sfValidatorFile' => 'validator',
  'sfValidatorFromDescription' => 'validator',
  'sfValidatorInteger' => 'validator',
  'sfValidatorNumber' => 'validator',
  'sfValidatorOr' => 'validator',
  'sfValidatorPass' => 'validator',
  'sfValidatorRegex' => 'validator',
  'sfValidatorSchema' => 'validator',
  'sfValidatorSchemaCompare' => 'validator',
  'sfValidatorSchemaFilter' => 'validator',
  'sfValidatorSchemaForEach' => 'validator',
  'sfValidatorString' => 'validator',
  'sfValidatorUrl' => 'validator',
  'sfOutputEscaper' => 'view/escaper',
  'sfOutputEscaperArrayDecorator' => 'view/escaper',
  'sfOutputEscaperGetterDecorator' => 'view/escaper',
  'sfOutputEscaperIteratorDecorator' => 'view/escaper',
  'sfOutputEscaperObjectDecorator' => 'view/escaper',
  'sfOutputEscaperSafe' => 'view/escaper',
  'sfEscapedViewParameterHolder' => 'view',
  'sfPartialView' => 'view',
  'sfPHPView' => 'view',
  'sfView' => 'view',
  'sfViewCacheManager' => 'view',
  'sfViewParameterHolder' => 'view',
  'sfWidgetFormI18nDate' => 'widget/i18n',
  'sfWidgetFormI18nDateTime' => 'widget/i18n',
  'sfWidgetFormI18nSelectCountry' => 'widget/i18n',
  'sfWidgetFormI18nSelectLanguage' => 'widget/i18n',
  'sfWidgetFormI18nTime' => 'widget/i18n',
  'sfWidget' => 'widget',
  'sfWidgetForm' => 'widget',
  'sfWidgetFormDate' => 'widget',
  'sfWidgetFormDateTime' => 'widget',
  'sfWidgetFormIdentity' => 'widget',
  'sfWidgetFormInput' => 'widget',
  'sfWidgetFormInputCheckbox' => 'widget',
  'sfWidgetFormInputFile' => 'widget',
  'sfWidgetFormInputHidden' => 'widget',
  'sfWidgetFormInputPassword' => 'widget',
  'sfWidgetFormSchema' => 'widget',
  'sfWidgetFormSchemaDecorator' => 'widget',
  'sfWidgetFormSchemaForEach' => 'widget',
  'sfWidgetFormSchemaFormatter' => 'widget',
  'sfWidgetFormSchemaFormatterList' => 'widget',
  'sfWidgetFormSchemaFormatterTable' => 'widget',
  'sfWidgetFormSelect' => 'widget',
  'sfWidgetFormSelectMany' => 'widget',
  'sfWidgetFormSelectRadio' => 'widget',
  'sfWidgetFormTextarea' => 'widget',
  'sfWidgetFormTime' => 'widget',
  'sfYaml' => 'yaml',
  'sfYamlDumper' => 'yaml',
  'sfYamlInline' => 'yaml',
  'sfYamlParser' => 'yaml',
);
}
