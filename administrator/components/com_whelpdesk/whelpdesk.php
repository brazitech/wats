<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL, see LICENSE.php
 * @package wats
 */

// Don't allow direct linking
defined('_JEXEC') or die('Restricted Access');

if (JRequest::getBool('modal'))
{
    JRequest::setVar('tmpl', 'component');
}

// add CSS
$document = &JFactory::getDocument();
$document->addStyleSheet('components/com_whelpdesk/assets/css/whd.css');


// wrap everything to catch any unexepcted errors
try
{

    // get the loader
    require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'classes' . DS . 'loader.php');

    // import the classes we need
    wimport('factory');
    wimport('dbhelper');
    wimport('helper.toolbar');
    wimport('helper.document');
    wimport('helper.message');
    wimport('database.identifiers');
    wimport('router');
    wimport('exceptions.invalidtoken');

    // import plugins
    JPluginHelper::importPlugin('WHD_Linker');

    // add include paths
    JTable::addIncludePath(JPATH_COMPONENT . DS . 'tables');

    // set the default toolbar title
    WDocumentHelper::title("Webamoeba Help Desk");
    JFactory::getDocument()->addStyleDeclaration(".icon-48-wats { background-image:url(components/com_whelpdesk/assets/icon-48.png );}");
    
    try
    {
        ob_start();
        // execute the request
        WFactory::getCommand()->execute();
        ob_end_flush();
    }
    catch (WException $exc)
    {
        // output the excpetion and clean the output buffer
        ob_end_clean();
        echo '<h1>'.JText::_('WHD_E:AN ERROR OCCURED').'</h1>';
        echo '<p>'.$exc->getMessage().'</p>';

        // @todo should only output this in debug mode
        echo '<h2>'.JText::_('WHD_E:DETAIL').'</h2>';
        echo '<pre>'.$exc->getTraceAsString().'</pre>';
    }



} catch (WInvalidTokenException $e) {
    // deal with applicaion specific exceptions
    JError::raiseError('403', $e->getMessage());
    jexit($e->getMessage());
}  catch (WException $e) {
    // deal with applicaion specific exceptions
    var_dump($e);
    jexit($e);
} catch (Exception $e) {
    // deal with general exceptions
    jexit($e);
}   
