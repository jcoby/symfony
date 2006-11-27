<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app = 'backend';
$fixtures = 'fixtures/fixtures.yml';
$ret = include(dirname(__FILE__).'/../../bootstrap/functional.php');
if (!$ret)
{
  return;
}

include(dirname(__FILE__).'/backendTestBrowser.class.php');

$b = new backendTestBrowser();
$b->initialize();

launch_tests($b);

sfConfig::set('sf_escaping_strategy', 'both');
launch_tests($b);

function launch_tests($b)
{
  // m2m relationships
  $b->
    // admin_double_list
    checkEditCustomization('m2m relationship (admin_double_list)', array('display' => array('title', 'body', 'author_article'), 'fields' => array('author_article' => array('type' => 'admin_double_list', 'params' => array('through_class' => 'AuthorArticle')))))->
    checkResponseElement('div.form-row label', 'Author article:', array('position' => 2))->
    checkResponseElement('div.form-row select[name="unassociated_author_article[]"]', true, array('position' => 2))->
    checkResponseElement('div.form-row select[name="unassociated_author_article[]"] option', 1)->
    checkResponseElement('div.form-row select[name="associated_author_article[]"]', true, array('position' => 2))->
    checkResponseElement('div.form-row select[name="associated_author_article[]"] option', 2)->

    // admin_select_list
    checkEditCustomization('m2m relationship (admin_select_list)', array('display' => array('title', 'body', 'author_article'), 'fields' => array('author_article' => array('type' => 'admin_select_list', 'params' => array('through_class' => 'AuthorArticle')))))->
    checkResponseElement('div.form-row label', 'Author article:', array('position' => 2))->
    checkResponseElement('div.form-row select[name="associated_author_article[]"][multiple="multiple"]', true)->
    checkResponseElement('div.form-row select[name="associated_author_article[]"] option', 3)->
    checkResponseElement('div.form-row select[name="associated_author_article[]"] option[selected="selected"]', 2)->

    // admin_check_list
    checkEditCustomization('m2m relationship (admin_check_list)', array('display' => array('title', 'body', 'author_article'), 'fields' => array('author_article' => array('type' => 'admin_check_list', 'params' => array('through_class' => 'AuthorArticle')))))->
    checkResponseElement('div.form-row label', 'Author article:', array('position' => 2))->
    checkResponseElement('div.form-row input[type="checkbox"][name="associated_author_article[]"][checked="checked"]', 2)->
    checkResponseElement('div.form-row input[type="checkbox"][name="associated_author_article[]"]', 3)->

    // update m2m
    click('save', array('associated_author_article' => array(2, 3)))->
    isStatusCode(302)->
    isRequestParameter('module', 'article')->
    isRequestParameter('action', 'edit')->

    isRedirected()->
    followRedirect()->
    isStatusCode(200)->
    isRequestParameter('module', 'article')->
    isRequestParameter('action', 'edit')->
    isRequestParameter('id', 1)->

    checkResponseElement('div.form-row input[type="checkbox"][id="associated_author_article_1"][checked="checked"]', false)->
    checkResponseElement('div.form-row input[type="checkbox"][id="associated_author_article_2"][checked="checked"]')->
    checkResponseElement('div.form-row input[type="checkbox"][id="associated_author_article_3"][checked="checked"]')
  ;
}