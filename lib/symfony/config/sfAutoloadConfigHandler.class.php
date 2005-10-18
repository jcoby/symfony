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
 * @subpackage config
 * @author     Fabien Potencier <fabien.potencier@gmail.com>
 * @author     Sean Kerr <skerr@mojavi.org>
 * @version    SVN: $Id$
 */
class sfAutoloadConfigHandler extends sfYamlConfigHandler
{
  /**
   * Execute this configuration handler.
   *
   * @param  string An absolute filesystem path to a configuration file.
   *
   * @return string Data to be written to a cache file.
   *
   * @throws sfConfigurationException If a requested configuration file does not exist or is not readable.
   * @throws sfParseException If a requested configuration file is improperly formatted.
   */
  public function & execute($configFile, $param = array())
  {
    // set our required categories list and initialize our handler
    $categories = array('required_categories' => array('autoload'));

    $this->initialize($categories);

    // parse the ini
    $config = $this->parseYaml($configFile);

    // init our data array
    $data = array();

    // let's do our fancy work
    foreach ($config['autoload'] as $entry)
    {
      if (isset($entry['name']))
      {
        $tmp = "\n// %s\n";
        $data[] = sprintf($tmp, $entry['name']);
      }

      // file mapping or directory mapping?
      if (!isset($entry['ext']))
      {
        // file mapping
        foreach ($entry['files'] as $class => $path)
        {
          $path = $this->replaceConstants($path);
          $path = $this->replacePath($path);

          $tmp = "\$classes['%s'] = '%s';";
          $data[] = sprintf($tmp, $class, $path);
        }
      }
      else
      {
        // directory mapping
        $ext = $entry['ext'];
        $path = $entry['path'];

        $path = $this->replaceConstants($path);
        $path = $this->replacePath($path);

        if (!is_dir($path))
        {
          continue;
        }

        // we automatically add our php classes
        require_once 'pake/pakeFinder.class.php';
        $finder = pakeFinder::type('file')->name('*'.$ext);

        // recursive mapping?
        $recursive = ((isset($entry['recursive'])) ? $entry['recursive'] : false);
        if (!$recursive)
        {
          $finder->maxdepth(1);
        }

        // exclude files or directories?
        $exclude = array('.svn', 'CVS');
        if (isset($entry['exclude']) && is_array($entry['exclude']))
        {
          $exclude = array_merge($exclude, $entry['exclude']);
        }
        $finder->prune($exclude)->discard($exclude);

        $files = $finder->in($path);
        foreach ($files as $file)
        {
          $tmp = "\$classes['%s'] = '%s';";
          $data[] = sprintf($tmp, basename($file, $ext), $file);
        }
      }
    }

    // compile data
    $retval = "<?php\n".
              "// auth-generated by sfMyAutoloadConfigHandler\n".
              "// date: %s\n%s\n?>";
    $retval = sprintf($retval, date('m/d/Y H:i:s'), implode("\n", $data));

    return $retval;
  }
}

?>
