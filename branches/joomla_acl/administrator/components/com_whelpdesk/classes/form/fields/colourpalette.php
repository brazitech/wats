<?php
/**
 * @version		$Id$
 * @package     helpdesk
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
wimport('database.query');

JHtml::script('administrator/components/com_whelpdesk/assets/javascript/moorainbow/mooRainbow.js', true, false);
JHtml::stylesheet('administrator/components/com_whelpdesk/assets/javascript/moorainbow/mooRainbow.css', null);

/**
 *
 *
 */
class JFormFieldColourPalette extends JFormField
{
    private static $colorpaletteIncrementor = 1;

	/**
	 * The field type.
	 *
	 * @var		string
	 */
	public $type = 'ColourPalette';

	public function getInput()
	{
        $red   = @(int)hexdec(substr($this->value, 1, 2));
        $green = @(int)hexdec(substr($this->value, 3, 2));
        $blue  = @(int)hexdec(substr($this->value, 5, 2));

        $number = self::$colorpaletteIncrementor++;
        $imagesPath = JURI::root(true).'/administrator/components/com_whelpdesk/assets/javascript/moorainbow/images/';
        $moorainbow = <<<MOORAINBOW
window.addEvent('domready', function() {
    var r = new MooRainbow('moorainbow-icon-number-$number', {
        'startColor': [$red, $green, $blue],
        'onChange': function(color) {
            $('$this->inputName').value = color.hex;
        },
        'imgPath': '$imagesPath'
    });
});
MOORAINBOW;

        $document = &JFactory::getDocument();
        $document->addScriptDeclaration($moorainbow);

        return '<input type="text" name="'.$this->inputName.'" id="'.$this->inputId.'" value="'.
                htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8').'"'.$class.$size.'readonly="readonly" />'
                .JHtml::image('/administrator/components/com_whelpdesk/assets/images/colour-16.png',
                        JText::_('WHD_DATA:SELECT A COLOUR'), array('id' => 'moorainbow-icon-number-'.$number), false);
    }
}