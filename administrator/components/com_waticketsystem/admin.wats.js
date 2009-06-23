/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL
 * @package wats
 */

/**
 * Updates all form elements names in array from control
 */
function updateControls( array, control )
{
	// loop through array and change values to control
	for (key in array)
			getElement( array[ key ] ).value = control.value;
}

/**
 * Gets element by id
 */
function getElement( id )
{
   if( document.all )
   {
      return document.all[ id ];
   }
   else
   {
      return document.getElementById( id );
   }
} 