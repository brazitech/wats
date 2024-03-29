<?php
/**
 * @version $Id$
 * @copyright Copyright (C) James Kennard
 * @license GNU/GPL, see LICENSE.php
 * @package wats
 */

// Don't allow direct linking
defined('_JEXEC') or die('Restricted Access');

// include classes
require("components/com_whelpdesk/admin.waticketsystem.class.php");

/**
 * @version 1.0
 * @created 06-Dec-2005 21:42:51
 */
class watsUserHTML extends watsUser
{
	/**
	 *
	 * @param database
	 */
	 function watsUserHTML() {
	 	$this->watsUser();
	 }

	/**
	 *
	 */
	function view()
	{
		echo "  <table class=\"adminform\">
				  <tr>
				  	<th colspan=\"2\">
						User
					</td>
				  </tr>
				  ";
		// add image
		if ( $this->image != null )
		{
			echo "  <tr>
					<td colspan=\"2\">
			        <img border=\"0\" src=\"".$this->image."\" />
					</td></tr>";
		} // end add image
		echo "		<tr><td width=\"100\">Username: </td><td>".$this->username."</td></tr>
					<tr><td width=\"100\">Group: </td><td>".$this->groupName."</td></tr>
					<tr><td width=\"100\">Organisation: </td><td>".$this->organisation."</td></tr>
				</table>";
	}
	
	/**
	 *
	 */
	function viewEdit()
	{
		global $Itemid;
		echo "  <table class=\"adminform\">
					<tr>
						<th colspan=\"2\">
							User Details
						</th>
					</tr>
					<tr>
						<td width=\"100\">
							Username:
						</td>
						<td width=\"85%\">
							".$this->username."
						</td>
					</tr>
					<tr>
						<td width=\"100\">
							Name:
						</td>
						<td width=\"85%\">
							".$this->name."
						</td>
					</tr>
					<tr>
						<td width=\"100\">
							Group:
						</td>
						<td width=\"85%\">
							<select name=\"grpId\">";
		// groups
		$db =& JFactory::getDBO();
		$db->setQuery( "SELECT g.grpid, g.name FROM #__wats_groups AS g ORDER BY g.name" );
		$groups = $db->loadObjectList();
		$noOfGroups = count( $groups );
		$i = 0;
		while ( $i < $noOfGroups )
		{
			if ( $groups[$i]->grpid == $this->group )
			{
				// cuurent group
				 echo "<option value=\"".$groups[$i]->grpid."\" selected=\"selected\">".$groups[$i]->name."</option>";
			}
			else
			{
				// other groups
				echo "<option value=\"".$groups[$i]->grpid."\">".$groups[$i]->name."</option>";
			}
			$i ++;
		}
		echo "    		 	</select>
						</td>
					</tr>
					<tr>
						<td width=\"100\">
							Organisation:
						</td>
						<td width=\"85%\">
							<input name=\"organisation\" type=\"text\" maxlength=\"255\" value=\"".$this->organisation."\" />
						</td>
					</tr>
				</table>
				<input type=\"hidden\" name=\"userid\" value=\"".$this->id."\" />";
	}
	
	/**
	 *
	 */
	function viewDelete()
	{
		global $Itemid;
		echo "<table class=\"adminform\">
					<tr>
						<th>
							Delete User
						</th>
					</tr>
					<tr>
						<td>
							<p>
							  Do not delete<br />
							  <input name=\"remove\" type=\"radio\" value=\"none\" checked=\"checked\" />
							</p>
							<p>
							  Remove users tickets (recommended when deleting)<br />
							  <input name=\"remove\" type=\"radio\" value=\"removetickets\" />
							</p>
							<p>
							  Remove users tickets and replies to other tickets<br />
							  <input name=\"remove\" type=\"radio\" value=\"removeposts\" />
							</p>
							<p>
							  Deleting a user here will remove their ticket system account, not their Joomla/Mambo account. If you have all user access turned on they will still be able to access the ticket system as the default group.
							</p>
						</td>
					</tr>
				</table>";
	}

	/**
	 *
	 * @param watsId
	 */
	function viewSimple()
	{
		echo $this->username."<br />".$this->groupName." - ".$this->organisation;
	}
	
	/**
	 * static
	 */
	 function makeButton() {
	 	global $Itemid;
	 	echo "<form name=\"watsUserMake\" method=\"get\" action=\"index.php\">
				<input name=\"option\" type=\"hidden\" value=\"com_whelpdesk\">
				<input name=\"Itemid\" type=\"hidden\" value=\"".$Itemid."\">
				<input name=\"act\" type=\"hidden\" value=\"user\">
				<input name=\"task\" type=\"hidden\" value=\"make\">
				<input type=\"submit\" name=\"watsAddUser\" value=\"Add User XXX\" class=\"watsFormSubmit\">
			  </form>";
	 }
	 
	/**
	 * static
	 * @param database
	 */
	 function newForm() {
	 	
		$wats =& WFactory::getConfig();
		
	 	echo "<table class=\"adminform\">
					<tr>
						<th colspan=\"2\">
							Add Users
						</th>
					</tr>
					<tr>
						<td width=\"100\">
							Select Users:
						</td>
						<td>
				  <select name=\"user[]\" size=\"10\" multiple=\"multiple\" id=\"user\">";
		// potential users
		$database =& JFactory::getDBO();
		$database->setQuery( "SELECT u.username, u.id, u.name FROM #__users AS u LEFT OUTER JOIN #__wats_users AS wu ON u.id=wu.watsid where wu.watsid is null" );
		$users = $database->loadObjectList();
		$noOfNullUsers = count( $users );
		$i = 0;
		while ( $i < $noOfNullUsers )
		{
			echo "<option value=\"".$users[$i]->id."\">".$users[$i]->username." (".$users[$i]->name.")</option>";
			$i ++;
		}
		echo "    </select>
						</td>
					</tr>
					<tr>
						<td width=\"100\">
							Select Group:
						</td>
						<td>
		          <select name=\"grpId\">";
		// potential groups
		$database->setQuery( "SELECT g.grpid, g.name FROM #__wats_groups AS g ORDER BY g.name" );
		$groups = $database->loadObjectList();
		$noOfGroups = count( $groups );
		$i = 0;
		while ( $i < $noOfGroups )
		{
			echo "<option value=\"".$groups[$i]->grpid."\">".$groups[$i]->name."</option>";
			$i ++;
		}
		echo "    </select>
						</td>
					</tr>
					<tr>
						<td width=\"100\">
							Enter Organisation:
						</td>
						<td>
					<input name=\"organisation\" type=\"text\" maxlength=\"255\" size=\"50\" value=\"".$wats->get( 'dorganisation' )."\" />
										</td>
					</tr>
					</table>";
	 }
}

/**
 * @version 1.0
 * @created 09-Jan-2006 15:54
 */
class watsUserSetHTML extends watsUserSet
{
	
	/**
	 * 
	 * @param finish
	 * @param start
	 */
	function view( $limitstart, $limit )
	{
		global $Itemid;
		
		$wats =& WFactory::getConfig();
		
		echo "<form name=\"adminForm\" method=\"post\" action=\"index.php\">
			  <input type=\"hidden\" value=\"com_whelpdesk\" name=\"option\"/>
			  <input type=\"hidden\" value=\"\" name=\"task\"/>
			  <input type=\"hidden\" value=\"user\" name=\"act\"/>";
		
		echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"adminlist\">
					<thead>
						<tr>
						  <th scope=\"col\" class=\"title\" style=\"width: 16px;\">&nbsp;</th>
						  <th scope=\"col\" class=\"title\">Username (name)</th>
						  <th scope=\"col\" class=\"title\">Organisation</th>
						  <th scope=\"col\" class=\"title\">Group</th>
						  <th scope=\"col\" class=\"title\">Email</th>
						</tr>
					</thead>
					<tbody>";
		// itterate through users
        $limitstartThrow = $limitstart;
        $limitMax = ($limit > 0) ? $limitstart + $limit : $this->noOfUsers;
        while (($limitstartThrow < $this->noOfUsers ) && ($limitstartThrow < $limitMax))
		{
			echo "<tr class=\"row".($limitstartThrow % 2)."\">
					<td>
					    <img src=\"../components/com_whelpdesk/images/".$wats->get('iconset', "PATH")."user1616.gif\" height=\"16\" width=\"16\" border=\"0\">
			        </td>
					<td>
			            <a href=\"index.php?option=com_whelpdesk&Itemid=".$Itemid."&act=user&task=edit&userid=".$this->userSet[$limitstartThrow]->id."\">".$this->userSet[$limitstartThrow]->username."</a> (".$this->userSet[$limitstartThrow]->name.")
			        </td>
					<td>".$this->userSet[$limitstartThrow]->organisation."</td>
					<td>".$this->userSet[$limitstartThrow]->groupName."</td>
					<td>".$this->userSet[$limitstartThrow]->email."</td>
				  </tr>";
			$limitstartThrow ++;
		} // end itterate through users
        
		echo "</tbody><tfoot><tr><td colspan=\"6\">";
        
        jimport("joomla.html.pagination");
        
        $pagination = new JPagination($this->noOfUsers, $limitstart, $limit);
        echo $pagination->getListFooter();
        
        echo "<input type=\"hidden\" name=\"option\" value=\"com_whelpdesk\" />";
		echo "<input type=\"hidden\" name=\"act\" value=\"user\" />";
		echo "<input type=\"hidden\" name=\"boxchecked\" value=\"0\" />";
		echo "<input type=\"hidden\" name=\"hidemainmenu\" value=\"0\" />";
        
        echo "</td></tr></tfoot></table></form>";
	}
	
}
 
 /**
 * @version 1.0
 * @created 06-Dec-2005 21:43:25
 */
class watsTicketHTML extends watsTicket
{

	/**
	 * 
	 */	
	function view( )
	{
		global $Itemid;
		
		$wats =& JFactory::getConfig();
		
		// echo out
		echo "<table class=\"adminform\"><tr><th>".$this->name."<br/>";
		echo "Ticket ID: WATS-".$this->ticketId."</th></tr></table>";
		// itterate through messages
		$i = 0;
		while ( $i < $this->msgNumberOf )
		{
			// create new user
			$msgUser = new watsUserHTML();
			$msgUser->loadWatsUser( $this->_msgList[$i]->watsId  );
			// print message
			echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"adminlist\">
				    <tr class=\"row0\">
					  <td scope=\"col\">";
			$msgUser->viewSimple();
		 	echo "<br /><span class=\"watsDate\">".date( $wats->get( 'date' ), $this->_msgList[$i]->datetime )."</span></td>
				    </tr>
				    <tr class=\"row1\">
					  <td>".$this->_msgList[$i]->msg."</td>
				    </tr>
				  </table>\n";
			// end print message
			$i ++;
		} // end itterate through messages
	}

	/**
	 * 
	 */
	function make( &$categorySet, &$watsUser )
	{
		global $Itemid;
		
		$wats =& JFactory::getConfig();
		
		// header and ticket name
		echo "<span class=\"watsHeading1\">"._WATS_TICKETS_SUBMIT."</span>
			  <div class=\"watsTicketMake\" id=\"watsTicketMake\">
			  <form name=\"submitticket\" method=\"post\" action=\"index.php?option=com_whelpdesk&Itemid=".$Itemid."&act=ticket&task=completeMake\" onsubmit=\"return watsValidateTicketMake( this, 'refill form XXX', '".$wats->get( 'defaultmsg' )."' );\">"
			  ._WATS_TICKETS_NAME.
			  "<input name=\"ticketname\" type=\"text\" id=\"ticketname\" maxlength=\"25\">";
		// itterate through categories
		echo "Category XXX<select name=\"catid\" class=\"watsCategorySetSelect\">";
	    foreach( $categorySet->categorySet as $category )
		{
			if ( $watsUser->checkPermission( $category->catid, "m" ) > 0 )
			{
				// allow user to submit ticket to category ticket
				echo "<option value=\"".$category->catid."\">".$category->name."</option>\n";
			}
		}
		// end itterate through categories
		echo "</select>";
		// message box
		echo _WATS_TICKETS_DESC;
		if ( $wats->get( 'msgbox' ) == "editor" )
		{
			editorArea( "msg", $wats->get( 'defaultmsg' ), "msg", $wats->get( 'msgboxw' )*8.5, $wats->get( 'msgboxh' )*18, 45, 5 );
		}
		else
		{
			echo "<textarea name=\"msg\" cols=\"".$wats->get( 'msgboxw' )."\" rows=\"".$wats->get( 'msgboxh' )."\" id=\"msg\">".$wats->get( 'defaultmsg' )."</textarea>";
		}
		// submit button
		echo "<input name=\"option\" type=\"hidden\" value=\"com_whelpdesk\">
			  <input name=\"Itemid\" type=\"hidden\" value=\"".$Itemid."\">
			  <input name=\"act\" type=\"hidden\" value=\"ticket\">
			  <input name=\"task\" type=\"hidden\" value=\"makeComplete\">
			  <input type=\"submit\" name=\"Submit\" value=\""._WATS_TICKETS_SUBMIT."\" class=\"watsFormSubmit\">
			  </form>
			  </div>";
		echo ( $wats->get( 'msgbox' ) == "bbcode" AND $wats->get( 'msgboxt' ) == "1" ) ? _WATS_BB_HELP : "";
	}

	function reopen()
	{
		global $Itemid;
		
		$wats =& JFactory::getConfig();
		
		echo "<div id=\"watsReply\" class=\"watsReply\">
		      <form name=\"submitmsg\" method=\"post\" action=\"index.php?option=com_whelpdesk&Itemid=".$Itemid."&act=ticket&task=completeReopen&ticketid=".$this->ticketId."\" onsubmit=\"return watsValidateTicketReopen( this, 'refill form XXX', '".$wats->get( 'defaultmsg' )."' );\">
			  Please give a reason why you want to reopen this ticket XXXX";
		// message box
		if ( $wats->get( 'msgbox' ) == "editor" )
		{
			editorArea( "msg", $wats->get( 'defaultmsg' ), "msg", $wats->get( 'msgboxw' )*8.5, $wats->get( 'msgboxh' )*18, 45, 5 );
		}
		else
		{
			echo "<textarea name=\"msg\" cols=\"".$wats->get( 'msgboxw' )."\" rows=\"".$wats->get( 'msgboxh' )."\" id=\"msg\">".$wats->get( 'defaultmsg' )."</textarea>";
		} // end message box
		echo "  <input name=\"option\" type=\"hidden\" value=\"com_whelpdesk\">
			    <input name=\"Itemid\" type=\"hidden\" value=\"".$Itemid."\">
			    <input name=\"act\" type=\"hidden\" value=\"ticket\">
			    <input name=\"task\" type=\"hidden\" value=\"completeReopen\">
			    <input name=\"ticketid\" type=\"hidden\" value=\"".$this->ticketId."\">";
		//echo newInput( $_GET );
		echo "  <input type=\"submit\" name=\"watsTicketReopen\" value=\"Reopen XXX\" class=\"watsFormSubmit\">
			  </form>
		      </div>";
	}
}

/**
 * @version 1.0
 * @created 06-Dec-2005 21:43:53
 */
class watsTicketSetHTML extends watsTicketSet
{
	/**
	 * 
	 * @param database
	 */
	function watsTicketSetHTML()
	{
		$this->watsTicketSet();
	}

	/**
	 * 
	 * @param finish
	 * @param start
	 */
	function view($limit, $limitstart) {
		$wats =& JFactory::getConfig();
		
		echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"adminlist\">
				<thead>
				    <tr>
					  <th scope=\"col\" class=\"title\" >Name</th>
					  <th scope=\"col\" class=\"title\" >User</th>
					  <th scope=\"col\" class=\"title\" >Posts</th>
					  <th scope=\"col\" class=\"title\" >First Post</th>
					  <th scope=\"col\" class=\"title\" >Last Post</th>
					  <th scope=\"col\" class=\"title\" width=\"40\">Status</th>
				    </tr>
				</thead>
				<tbody>";
		// itterate through tickets
        $limitstartThrow = $limitstart;
        $limitMax = ($limit > 0) ? $limitstart + $limit : $this->ticketNumberOf;
		while (($limitstartThrow < $this->ticketNumberOf ) && ($limitstartThrow < $limitMax))
		{
			echo "<tr class=\"row".($limitstartThrow % 2)."\">
					<td>
			            <a href=\"index.php?option=com_whelpdesk&act=ticket&task=view&ticketid=".$this->_ticketList[$limitstartThrow]->ticketId."\">".$this->_ticketList[$limitstartThrow]->name."</a></td>
					<td>".$this->_ticketList[$limitstartThrow]->username."</td>
					<td>".$this->_ticketList[$limitstartThrow]->msgNumberOf."</td>
					<td>".date( $wats->get( 'date' ), $this->_ticketList[$limitstartThrow]->datetime )."</td>
					<td><span class=\"watsDate\">".date( $wats->get( 'date' ), $this->_ticketList[$limitstartThrow]->lastMsg )."</span></td>
					<td>";
			// status
			if ( $this->_ticketList[$limitstartThrow]->lifeCycle == 1 )
			{
				echo "<img src=\"images/tick.png\" width=\"12\" height=\"12\" border=\"0\" alt=\"Open\" />";
			}
			else if ( $this->_ticketList[$limitstartThrow]->lifeCycle == 2 )
			{
				echo "<img src=\"images/publish_x.png\" width=\"12\" height=\"12\" border=\"0\" alt=\"Closed\" />";
			}
			else
			{
				echo "<img src=\"images/checked_out.png\" width=\"12\" height=\"12\" border=\"0\" alt=\"Dead\" />";
			}
			// end status
			echo "</td></tr>";
			$limitstartThrow ++;
		} // end itterate through tickets
		echo "</tbody><tfoot><tr><td colspan=\"6\">";
        
        jimport("joomla.html.pagination");
        
        $pagination = new JPagination($this->ticketNumberOf, $limitstart, $limit);
        echo $pagination->getListFooter();
        
        echo "<input type=\"hidden\" name=\"option\" value=\"com_whelpdesk\" />";
		echo "<input type=\"hidden\" name=\"act\" value=\"ticket\" />";
		echo "<input type=\"hidden\" name=\"boxchecked\" value=\"0\" />";
		echo "<input type=\"hidden\" name=\"hidemainmenu\" value=\"0\" />";
        
        echo "</td></tr></tfoot></table>";
	}
	
}

/**
 * @version 1.0
 * @created 06-Dec-2005 21:44:17
 */
class watsCategoryHTML extends watsCategory
{
	/**
	 * 
	 * @param database
	 */	
	function watsCategoryHTML()
	{
		$this->watsCategory();
	}
	
	/**
	 * static
	 */
	function newForm( )
	{
		echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"adminform\">
			    <tr>
				  <th colspan=\"2\" scope=\"col\">New Category</th>
			    </tr>
			    <tr>
				  <td width=\"100\">
				  	Name
				  </td>
				  <td><input name=\"name\" type=\"text\" maxlength=\"255\" size=\"50\" value=\"\" /></td>
			    </tr>
			    <tr>
				  <td width=\"100\">
				  	Description
				  </td>
				  <td><input name=\"description\" type=\"text\" maxlength=\"255\" size=\"50\" value=\"\" /></td>
			    </tr>
			    <tr>
				  <td width=\"100\">
				  	Image
				  </td>
				  <td><input name=\"image\" type=\"text\" maxlength=\"255\" size=\"50\" value=\"\" /></td>
			    </tr>
			  </table>";
	}

	/**
	 * 
	 */
	function viewEdit( )
	{
		echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"adminform\">
			    <tr>
				  <th colspan=\"2\" scope=\"col\">".$this->name."</th>
			    </tr>
			    <tr>
				  <td width=\"100\">
				  	Name
				  </td>
				  <td><input name=\"name\" type=\"text\" maxlength=\"255\" size=\"50\" value=\"".$this->name."\" /></td>
			    </tr>
			    <tr>
				  <td width=\"100\">
				  	Description
				  </td>
				  <td><input name=\"description\" type=\"text\" maxlength=\"255\" size=\"50\" value=\"".$this->description."\" /></td>
			    </tr>
			    <tr>
				  <td width=\"100\">
				  	Image
				  </td>
				  <td><input name=\"image\" type=\"text\" maxlength=\"255\" size=\"50\" value=\"".$this->image."\" /></td>
			    </tr>
			    <tr>
				  <td width=\"100\">
				  	Preview
				  </td>
				  <td><img src=\"".$this->image."\" /></td>
			    </tr>
			  </table>
			  <input type=\"hidden\" name=\"catid\" value=\"".$this->catid."\" />";
	}

	/**
	 *
	 * @param watsId
	 */
	function viewDelete()
	{
		global $Itemid;
		echo "<table class=\"adminform\">
					<tr>
						<th>
							Delete Category
						</th>
					</tr>
					<tr>
						<td>
							<p>
							  Do not delete<br />
							  <input name=\"remove\" type=\"radio\" value=\"none\" checked=\"checked\" />
							</p>
							<p>
							  Delete All tickets from category<br />
							  <input name=\"remove\" type=\"radio\" value=\"removetickets\" />
							</p>
							<p>
							  Deleting a Category will cause all tickets within the category to be deleted and purged.
							</p>
						</td>
					</tr>
				</table>";
	}

	function viewPurge()
	{
		global $Itemid;
		echo "<p>
				<form name=\"watsTicketMake\" method=\"get\" action=\"index.php\">
				  <input name=\"option\" type=\"hidden\" value=\"com_whelpdesk\">
				  <input name=\"Itemid\" type=\"hidden\" value=\"".$Itemid."\">
				  <input name=\"act\" type=\"hidden\" value=\"category\">
				  <input name=\"task\" type=\"hidden\" value=\"purge\">
				  <input name=\"catid\" type=\"hidden\" value=\"".$this->catid."\">
				  <input name=\"lifecycle\" type=\"hidden\" value=\"a\">
				  <input name=\"page\" type=\"hidden\" value=\"1\">
				  <input type=\"submit\" name=\"watsTicketMake\" value=\"Purge dead tickets in ".$this->name."\" class=\"watsFormSubmit\">
			    </form>
			  </p>";
	}
}

/**
 * @version 1.0
 * @created 12-Dec-2005 13:54:49
 */
class watsAssignHTML extends watsAssign
{
	/**
	 * 
	 */
	function view( )
	{
		global $Itemid;
		
		$wats =& JFactory::getConfig();
		
		echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"watsCategoryView\">
			    <tr>
				  <th colspan=\"2\" scope=\"col\"><a href=\"index.php?option=com_whelpdesk&Itemid=$Itemid&act=assign&task=view&page=1\">".$wats->get( 'assignname' )."</a></th>
			    </tr>
			    <tr>
				  <td>";
		if ( $wats->get( 'assignimage' ) !== null )
		{
			echo "";
		}
		echo "</td>
				  <td>".$wats->get( 'assigndescription' )."</td>
			    </tr>
			  </table>";
	}

	/**
	 * 
	 */
	function viewTicketSet( $finish, $start )
	{
		// display category details
		$this->view();
		// display ticketSet
		if ( isset($this->ticketSet) )
		{
			$this->ticketSet->view( $finish, $start );
		}
		else
		{
			echo "error ticket set not loaded";
		}
	}

	/**
	 * 
	 */	
	function pageNav( $ticketsPerPage, $currentPage = 0, $currentTicketsPerPage = 0 )
	{
		global $Itemid;
		if ( $currentTicketsPerPage == 0 )
		{
			$currentTicketsPerPage = $ticketsPerPage;
		}
		echo "<div class=\"watsPageNav\">";
		// check is valid to show
		if ( $currentTicketsPerPage < $this->ticketSet->ticketNumberOf )
		{
			echo _WATS_TICKETS_PAGES.": ";
			$numberOfPages = 0;
			$numberOfPages = intval( $this->ticketSet->ticketNumberOf / $ticketsPerPage );
			if ( ( $this->ticketSet->ticketNumberOf % $ticketsPerPage ) > 0 )
			{
				$numberOfPages ++;
			}
			// previous
			if ( $currentPage > 1 )
			{
				echo " <a href=\"index.php?option=com_whelpdesk&Itemid=$Itemid&act=assign&task=view&page=".($currentPage - 1)."\">&lt;</a>";
			} // end previous
			// itterate through pages
			$i = 1;
			while ( $i <= $numberOfPages )
			{
				if ( $i != $currentPage)
				{
					echo " <a href=\"index.php?option=com_whelpdesk&Itemid=$Itemid&act=assign&task=view&page=".$i."\">".$i."</a>";
				}
				else
				{
					echo " <span class=\"watsPageNavCurrentPage\">".$i."</span>";
				}
				$i ++;
			} // end itterate through pages
			// next
			if ( $currentPage < $numberOfPages )
			{
				echo " <a href=\"index.php?option=com_whelpdesk&Itemid=$Itemid&act=assign&task=view&page=".($currentPage + 1)."\">&gt;</a>";
			} // end next
		}
		echo " </div>";
	}
}

/**
 * @version 1.0
 * @created 06-Dec-2005 21:43:13
 */
class watsCategorySetHTML extends watsCategorySet
{

	/**
	 * 
	 */	
	function viewWithTicketSet( $finish, $start = 0, &$watsUser )
	{
		$wats =& JFactory::getConfig();
		
		foreach( $this->categorySet as $category )
		{
			echo "<div class=\"watsCategoryViewWithTicketSet\" id=\"watsCategory".$category->catid."\">";
			$category->viewTicketSet( $finish, $start );
			$category->pageNav( $wats->get( 'ticketssub' ), 0, $wats->get( 'ticketsfront' ), $watsUser );
			echo "</div>";
		}
	}


	/**
	 * 
	 */		
	function select()
	{
		global $Itemid;
		
		$wats =& JFactory::getConfig();
		
		echo "<select name=\"option\" id=\"watsCategorySetSelect\" class=\"watsCategorySetSelect\">
				<option onClick=\"watsCategorySetSelect( -1, $Itemid )\">".$wats->get( 'name' )."</option>\n";
	    foreach( $this->categorySet as $category )
		{
		    echo "<option onClick=\"watsCategorySetSelect( $category->catid, $Itemid )\">".$category->name."</option>\n";
		}
		echo "</select>";
	}

	/**
	 * 
	 * @param finish
	 * @param start
	 */
	function view( $limit, $limitstart )
	{
		global $Itemid, $wats;
		echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"adminlist\">
				<thead>
				    <tr>
					  <th scope=\"col\" class=\"title\">Name</th>
					  <th scope=\"col\" class=\"title\">Description</th>
				    </tr>
				</thead>
				<tbody>";
		// itterate through categories
        $limitstartThrow = $limitstart;
        $limitMax = ($limit > 0) ? $limitstart + $limit : count($this->categorySet);
        while (($limitstartThrow < count($this->categorySet) ) && ($limitstartThrow < $limitMax))
		{
            echo "<tr class=\"row".($limitstartThrow % 2)."\">";
			echo "<td><a href=\"index.php?option=com_whelpdesk&act=category&task=view&catid=".$this->categorySet[$limitstartThrow]->catid."\">".$this->categorySet[$limitstartThrow]->name."</a></td>";
			echo "<td>".$this->categorySet[$limitstartThrow]->description."</td>";
			echo "</tr>";
			$limitstartThrow ++;
		} // end itterate through users
		echo "</tbody><tfoot><tr><td colspan=\"6\">";
        
        jimport("joomla.html.pagination");
        
        $pagination = new JPagination(count($this->categorySet), $limitstart, $limit);
        echo $pagination->getListFooter();
        
        echo "<input type=\"hidden\" name=\"option\" value=\"com_whelpdesk\" />";
		echo "<input type=\"hidden\" name=\"act\" value=\"category\" />";
		echo "<input type=\"hidden\" name=\"boxchecked\" value=\"0\" />";
		echo "<input type=\"hidden\" name=\"hidemainmenu\" value=\"0\" />";
        
        echo "</td></tr></tfoot></table>";
	}
	
}

class watsCssHTML extends watsCss
{

	/*
	 * @param selector name
	 * @param textarea name
	 */
	function processSettings()
	{
		$wats =& WFactory::getConfig();
		$status = JRequest::getCmd('watsCSS', null);

		// enable disable css
        // check is of correct type and is different
        if ( ( $status == 'enable' || $status == 'disable' ) && $status != $wats->get('css') )
        {
            $wats->set( 'css', $status );;
            $wats->save();
            $this->css = $status;
        }
		
		$keys = array_keys( $_POST );
		foreach( $keys as $key )
		{
			if ( substr($key, 0, 7 )  == 'watscss' )
			{
				// decode name
				$selector = substr( $key, 7, strlen( $key ) - 7 );
				$selector = str_replace( '_dot_', '.', $selector );
				$selector = str_replace( '_hash_', '#', $selector );
				$selector = str_replace( '_comma_', ',', $selector );
				$selector = str_replace( '_', ' ', $selector );
				// end decode name
				// parse content
				$style = eregi_replace( '}', '', $_POST[$key]);
				$this->setStyle( $selector, $style );
			}
		}
	}

	/*
	 * @param selector name
	 * @param textarea name
	 */
	function edit( $selector, $textarea='' )
	{
		// check textarea name
		if ( $textarea == '' )
		{
			$textarea = 'watscss'.$selector;
		}
		$textarea = str_replace( '#', '_hash_', $textarea );
		$textarea = str_replace( '.', '_dot_', $textarea );
		$textarea = str_replace( ',', '_comma_', $textarea );
		$textarea = str_replace( ' ', '_', $textarea );
		echo "<textarea name=\"".$textarea."\" cols=\"50\" rows=\"4\">";
		echo $this->getStyle( $selector );
		echo "</textarea>";
	}

	/*
	 * General CSS Styles
	 */
	function editSettings()
	{
	    JHTML::_("behavior.tooltip");
	
		echo "  <table border=\"0\"> 
				  <tr> 
					<td colspan=\"2\">To make it easier to customise the visual appearance, you can, if you choose, use this integrated CSS editor. If you do choose to turn this option on, please make make sure that none of the styles defined here are in your template CSS file. </td> 
				  </tr> 
				  <tr> 
					<td colspan=\"2\">&nbsp;</td> 
				  </tr> 
				  <tr> 
					<td width=\"185\">WATS CSS :</td> 
					<td width=\"500\">
					  <input type=\"radio\" name=\"watsCSS\" id=\"watsCSSDisable\" value=\"disable\"";
					   echo ($this->css == "disable") ? " checked=\"checked\"" : " ";
					   echo "class=\"inputbox\" />
					  <label for=\"watsCSSDisable\">Disable</label>
					  <input type=\"radio\" name=\"watsCSS\" id=\"watsCSSEnable\" value=\"enable\"";
					   echo ($this->css == "enable") ? " checked=\"checked\"" : " ";
					   echo "class=\"inputbox\" />
					  <label for=\"watsCSSEnable\">Enable</label> ".JHTML::_("tooltip", 'Ensure no WATS styles are present in the site template before enabling', 'WATS Warning', "warning.png")."
					  </td> 
				  </tr> 
				  <tr> 
					<td colspan=\"2\">&nbsp;</td> 
				  </tr> 
				</table>";
	}
	
	/*
	 * General CSS Styles
	 */
	function editGeneral()
	{
		echo "  <p><strong>Heading 1</strong> (.watsHeading1)<br />
				  ";
				  $this->edit('.watsHeading1');
				  echo "
				  </p>
				<p><strong>Heading 2</strong> (.watsHeading2)<br />
				  ";
				  $this->edit('.watsHeading2');
				  echo "
				  </p>
				<p><strong>Date/Time</strong> (.watsDate)<br />
				  ";
				  $this->edit('.watsDate');
				  echo "
				  </p>
				<p><strong>Submit Button</strong> (.watsFormSubmit)<br />
				  ";
				  $this->edit('.watsFormSubmit');
				  echo "
				  </p>";
	}
	
	/*
	 * Navigation CSS Styles
	 */
	function editNavigation()
	{
		echo "  <p><strong>Navigation Combobox </strong> (#watsCategorySetSelect)<br />
				  ";
				  $this->edit('#watsCategorySetSelect');
				  echo "
				  </p>
				<p><strong>Navigation Division</strong> (#watsNavigation)<br />
				  ";
				  $this->edit('#watsNavigation');
				  echo "
				  </p>
				<p><strong>Navigation Form, Input And Select</strong> (#watsNavigation form, input, select)<br />
				  ";
				  $this->edit('#watsNavigation form, input, select');
				  echo "
				  </p>
				<p><strong>Navigation Form Select</strong> (#watsNavigation select)<br />
				  ";
				  $this->edit('#watsNavigation select');
				  echo "
				  </p>";
	}
	
	/*
	 * Categories CSS Styles
	 */
	function editCategories()
	{
		echo "  <p><strong>Category Division</strong> (.watsCategoryViewWithTicketSet)<br />
				  ";
				  $this->edit('.watsCategoryViewWithTicketSet');
				  echo "
				  </p>
				<p><strong>Category Description Table</strong> (.watsCategoryView)<br />
				  ";
				  $this->edit('.watsCategoryView');
				  echo "
				  </p>
				<p><strong>Links In Category Tables</strong> (.watsCategoryView th a)<br />
				  ";
				  $this->edit('.watsCategoryView th a');
				  echo "
				  </p>
				<p><strong>Selected Page</strong> (.watsSelectedPage)<br />
				  ";
				  $this->edit('.watsSelectedPage');
				  echo "
				  </p>";
	}
	
	/*
	 * Categories CSS Styles
	 */
	function editTickets()
	{
		echo "  <p><strong>Table Of Tickets</strong> (.watsTicketSetView)<br />
				  ";
				  $this->edit('.watsTicketSetView');
				  echo "
				  </p>
				<p><strong>Table Of Tickets Header</strong> (.watsTicketSetView th)<br />
				  ";
				  $this->edit('.watsTicketSetView th');
				  echo "
				  </p>
				<p><strong>Odd Row of Ticket Table</strong> (.watsTicketSetViewRow0)<br />
				  ";
				  $this->edit('.watsTicketSetViewRow0');
				  echo "
				  </p>
				<p><strong>Highlight Identifier i.e. new</strong> (.watsTicketHighlight)<br />
				  ";
				  $this->edit('.watsTicketHighlight');
				  echo "
				  </p>
				<p><strong>Assign Ticket Button</strong> (.watsTicketAssign)<br />
				  ";
				  $this->edit('.watsTicketAssign');
				  echo "
				  </p>
				<p><strong>Assign Ticket Combobox</strong> (.watsViewAssignTo select)<br />
				  ";
				  $this->edit('.watsViewAssignTo select');
				  echo "
				  </p>
				<p><strong>Ticket ID</strong> (.watsTicketId)<br />
				  ";
				  $this->edit('.watsTicketId');
				  echo "
				  </p>
				<p><strong>Reply Division</strong> (#watsReply)<br />
				  ";
				  $this->edit('#watsReply');
				  echo "
				  </p>
				<p><strong>Ticket Division</strong> (#watsTicketView)<br />
				  ";
				  $this->edit('#watsTicketView');
				  echo "
				  </p>
				<p><strong>Message Table</strong> (.watsMsgViewTable)<br />
				  ";
				  $this->edit('.watsMsgViewTable');
				  echo "
				  </p>
				<p><strong>Message Table Headings</strong> (.watsMsgViewTable th)<br />
				  ";
				  $this->edit('.watsMsgViewTable th');
				  echo "
				  </p>
				<p><strong>Message Division</strong> (div .watsMsgView)<br />
				  ";
				  $this->edit('div .watsMsgView');
				  echo "
				  </p>
				<p><strong>Make Ticket Division</strong> (#watsTicketMake)<br />
				  ";
				  $this->edit('#watsTicketMake');
				  echo "
				  </p>";
	}

	/*
	 * Assigned Tickets CSS Styles
	 */
	function editAssignedTickets()
	{
		echo "  <p><strong>Assigned Tickets Description Table</strong> (#watsAssignedTickets .watsCategoryView)<br />
				  ";
				  $this->edit('#watsAssignedTickets .watsCategoryView');
				  echo "
				  </p>
				<p><strong>Table Of Assigned Tickets</strong> (#watsAssignedTickets .watsTicketSetView)<br />
				  ";
				  $this->edit('#watsAssignedTickets .watsTicketSetView');
				  echo "
				  </p>
				<p><strong>Table Of Assigned Tickets Headings</strong> (#watsAssignedTickets .watsTicketSetView th)<br />
				  ";
				  $this->edit('#watsAssignedTickets .watsTicketSetView th');
				  echo "
				  </p>
				<p><strong>Odd Row Of Assigned Tickets Table</strong> (#watsAssignedTickets .watsTicketSetViewRow0)<br />
				  ";
				  $this->edit('#watsAssignedTickets .watsTicketSetViewRow0');
				  echo "
				  </p>";
	}

	/*
	 * Users CSS Styles
	 */
	function editUsers()
	{
		echo "  <p><strong>Table Of Users Header</strong> (.watsUsersView)<br />
				  ";
				  $this->edit('.watsUsersView');
				  echo "
				  </p>
				<p><strong>Links In Table Of Users Header</strong> (.watsUsersView th a)<br />
				  ";
				  $this->edit('.watsUsersView th a');
				  echo "
				  </p>
				<p><strong>Odd Row Of User Table</strong> (.watsUserSetViewRow0)<br />
				  ";
				  $this->edit('.watsUserSetViewRow0');
				  echo "
				  </p>
				<p><strong>Table Of Users</strong> (.watsUserSetView)<br />
				  ";
				  $this->edit('.watsUserSetView');
				  echo "
				  </p>
				<p><strong>Table of Users Headings</strong> (.watsUserSetView th)<br />
				  ";
				  $this->edit('.watsUserSetView th');
				  echo "
				  </p>
				<p><strong>Single User Division</strong> (.watsUserView)<br />
				  ";
				  $this->edit('.watsUserView');
				  echo "
				  </p>
				<p><strong>Delete User Division</strong> (#watsUserDelete)<br />
				  ";
				  $this->edit('#watsUserDelete');
				  echo "
				  </p>";
	}

	/*
	 *
	 */
	function editAll()
	{
		$this->editSettings();
		$this->editGeneral();
		$this->editNavigation();
		$this->editCategories();
		$this->editTickets();
		$this->editAssignedTickets();
		$this->editUsers();
	}
	
	function viewRestore()
	{
		global $Itemid;
		echo "<table class=\"adminform\">
					<tr>
						<th>
							Restore CSS
						</th>
					</tr>
					<tr>
						<td>
							<p>
							  Do not restore<br />
							  <input name=\"restore\" type=\"radio\" value=\"none\" checked=\"checked\" />
							</p>
							<p>
							  Restore CSS to installation default (use this option with caution, any CSS that you have edited will be deleted)<br />
							  <input name=\"restore\" type=\"radio\" value=\"restore\" />
							</p>
						</td>
					</tr>
				</table>";
	}
	
}

/**
 * @version 1.0
 * @created 01-May-2006 16:25:10
 */
class watsUserGroupHTML extends watsUserGroup
{

	/**
	 *
	 * @param watsId
	 */
	function viewEdit()
	{
		global $Itemid;
		
		JHTML::_("behavior.tooltip");
		
		echo "  <table class=\"adminform\">
					<tr>
						<th colspan=\"2\">
							Group Details
						</th>
					</tr>
					<tr>
						<td width=\"100\">
							Name:
						</td>
						<td>
							<input name=\"name\" type=\"text\" maxlength=\"255\" size=\"50\" value=\"".$this->name."\" />
						</td>
					</tr>
					<tr>
						<td width=\"100\">
							Image:
						</td>
						<td>
							<input name=\"image\" type=\"text\" maxlength=\"255\" size=\"50\" value=\"".$this->image."\" />
						</td>
					</tr>
					<tr>
						<td width=\"100\">
							Preview:
						</td>
						<td>
							<img src=\"../".$this->image."\" />
						</td>
					</tr>
					<tr>
					    <td colspan=\"2\">";
		// Tabs
		jimport("joomla.html.pane");
		
		$ritesTabs = JPane::getInstance("tabs");
		echo $ritesTabs->startPane('ritesTabs');
		// fill tabs
		{
			// User Setup Rites
			echo $ritesTabs->startPanel( 'User Setup', 'ritesTabs' );
		echo "  <table class=\"adminform\">
					<tr>
						<td width=\"100\">
							View:
						</td>
						<td>
							<input name=\"userGroupV\" type=\"checkbox\"";
							echo ($this->checkUserPermission('V') == 2) ? " checked=\"checked\"" : "" ;
							echo " /> ";
							echo JHTML::_("tooltip", 'This will allow all users of this group to view ALL other user accounts for this component.', 'WATS Warning', "warning.png");
						echo "</td>
					</tr>
					<tr>
						<td width=\"100\">
							Make:
						</td>
						<td>
							<input name=\"userGroupM\" type=\"checkbox\"";
							echo ($this->checkUserPermission('M') == 2) ? " checked=\"checked\"" : "" ;
							echo " /> ";
							echo JHTML::_("tooltip", 'This will allow all users of this group to ADD user accounts to this component.', 'WATS Warning', "warning.png");
						echo "</td>
					</tr>
					<tr>
						<td width=\"100\">
							Edit:
						</td>
						<td>
							<input name=\"userGroupE\" type=\"checkbox\"";
							echo ($this->checkUserPermission('E') == 2) ? " checked=\"checked\"" : "" ;
							echo " /> ";
							echo JHTML::_("tooltip", 'This will allow all users of this group to EDIT user accounts in this component.', 'WATS Warning', "warning.png");
						echo "</td>
					</tr>
					<tr>
						<td width=\"100\">
							Delete:
						</td>
						<td>
							<input name=\"userGroupD\" type=\"checkbox\"";
							echo ($this->checkUserPermission('D') == 2) ? " checked=\"checked\"" : "" ;
							echo " /> ";
							echo JHTML::_("tooltip", 'This will allow all users of this group to DELETE user accounts from this component.', 'WATS Warning', "warning.png");
						echo "</td>
					</tr>
				</table>";
			echo $ritesTabs->endPanel();
			// Rites Matrix
			echo $ritesTabs->startPanel( 'Rites Matrix', 'ritesTabs' );
			$this->categoryRites->viewMatrix();
			echo $ritesTabs->endPanel();
		}
		// end fill tabs
		echo $ritesTabs->endPane();
		echo "</tr></td></table>";
	}

	/**
	 *
	 */
	function viewDelete()
	{
		global $Itemid;
		echo "<table class=\"adminform\">
					<tr>
						<th>
							Delete Group
						</th>
					</tr>
					<tr>
						<td>
							<p>
							  Do not delete<br />
							  <input name=\"remove\" type=\"radio\" value=\"none\" checked=\"checked\" />
							</p>
							<p>
							  Remove users from group<br />
							  <input name=\"remove\" type=\"radio\" value=\"remove\" />
							</p>
							<p>
							  Remove users from group and delete users tickets (recommended when deleting)<br />
							  <input name=\"remove\" type=\"radio\" value=\"removetickets\" />
							</p>
							<p>
							  Remove users from group and delete users tickets and replies to other tickets<br />
							  <input name=\"remove\" type=\"radio\" value=\"removeposts\" />
							</p>
							<p>
							  Deleting a user here will remove their ticket system account, not their Joomla/Mambo account. If you have all user access turned on they will still be able to access the ticket system as the default group.
							</p>
						</td>
					</tr>
				</table>";
	}
	
	/**
	 * static
	 */
	function newForm( )
	{
		echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"adminform\">
			    <tr>
				  <th colspan=\"2\" scope=\"col\">New Group</th>
			    </tr>
			    <tr>
				  <td width=\"100\">
				  	Name
				  </td>
				  <td><input name=\"name\" type=\"text\" maxlength=\"255\" size=\"50\" value=\"\" /></td>
			    </tr>
			    <tr>
				  <td width=\"100\">
				  	Image
				  </td>
				  <td><input name=\"image\" type=\"text\" maxlength=\"255\" size=\"50\" value=\"\" /></td>
			    </tr>
			  </table>";
	}

	/*
	 *
	 */
	function processForm()
	{
		global $_POST, $wats;
		
		$keys = array_keys( $_POST );
		$riteString = '';
		$lastCatid = null;
		foreach( $keys as $key )
		{
			if ( is_numeric( substr($key, 0, 1 ) ) )
			{
				// get category
				$catid = substr($key, 0, strlen( $key ) - 1 );
				// get rite
				$rite = substr($key, strlen( $key ) - 1, strlen( $key ) );
				// create watsUserGroupCategoryPermissionSet
				$edit = new watsUserGroupCategoryPermissionSet( $_POST['groupid'], $catid );
				$edit->setPermission( $rite, $_POST[$key] );
				$edit->save();
			}
		}
		
		// process basics... yawn
		$this->name = htmlspecialchars( $_POST['name'] );
		$this->image = htmlspecialchars( $_POST['image'] );
		
		// view
		if ( isset( $_POST['userGroupV'] ) )
		{
			$this->setUserPermission( 'V', true );
		}
		else
		{
			$this->setUserPermission( 'V', false );
		}
		
		// make
		if ( isset( $_POST['userGroupM'] ) )
		{
			$this->setUserPermission( 'M', true );
		}
		else
		{
			$this->setUserPermission( 'M', false );
		}
		
		// edit
		if ( isset( $_POST['userGroupE'] ) )
		{
			$this->setUserPermission( 'E', true );
		}
		else
		{
			$this->setUserPermission( 'E', false );
		}
		
		// make
		if ( isset( $_POST['userGroupD'] ) )
		{
			$this->setUserPermission( 'D', true );
		}
		else
		{
			$this->setUserPermission( 'D', false );
		}
		
	}

}

/**
 * @version 1.0
 * @created 01-May-2006 16:25:10
 */
class watsUserGroupSetHTML extends watsUserGroupSet
{

	/**
	 * 
	 * @param finish
	 * @param start
	 */
	function view( $limitstart, $limit )
	{
		global $Itemid, $wats;
		echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"adminlist\">
				    <thead>
						<tr>
						  <th scope=\"col\" class=\"title\">Group</th>
						  <th scope=\"col\" class=\"title\">Image</th>
						  <th scope=\"col\" class=\"title\" width=\"35\">View</th>
						  <th scope=\"col\" class=\"title\" width=\"35\">Make</th>
						  <th scope=\"col\" class=\"title\" width=\"35\">Edit</th>
						  <th scope=\"col\" class=\"title\" width=\"35\">Delete</th>
						</tr>
					</thead>
					<tbody>";
		// itterate through groups
        // itterate through users
        $limitstartThrow = $limitstart;
        $limitMax = ($limit > 0) ? $limitstart + $limit : $this->noOfUsers;
        while (($limitstartThrow < $this->noOfGroups ) && ($limitstartThrow < $limitMax))
		{
			echo "<tr class=\"row".($limitstartThrow % 2)."\">
					<td><a href=\"index.php?option=com_whelpdesk&act=rites&task=view&groupid=".$this->_userGroupList[$limitstartThrow]->grpid."\">".$this->_userGroupList[$limitstartThrow]->name."</a></td>
					<td>".$this->_userGroupList[$limitstartThrow]->image."</td>
					<td><input name=\"userGroupV".$limitstartThrow."\" type=\"checkbox\"";
					echo ($this->_userGroupList[$limitstartThrow]->checkUserPermission('V') == 2) ? " checked=\"checked\"" : "" ;
					echo " DISABLED /></td>
					<td><input name=\"userGroupM".$limitstartThrow."\" type=\"checkbox\"";
					echo ($this->_userGroupList[$limitstartThrow]->checkUserPermission('M') == 2) ? " checked=\"checked\"" : "" ;
					echo " DISABLED /></td>
					<td><input name=\"userGroupE".$limitstartThrow."\" type=\"checkbox\"";
					echo ($this->_userGroupList[$limitstartThrow]->checkUserPermission('E') == 2) ? " checked=\"checked\"" : "" ;
					echo " DISABLED /></td>
					<td><input name=\"userGroupD".$limitstartThrow."\" type=\"checkbox\"";
					echo ($this->_userGroupList[$limitstartThrow]->checkUserPermission('D') == 2) ? " checked=\"checked\"" : "" ;
					echo " DISABLED /></td>
				  </tr>";
			$limitstartThrow ++;
		} // end itterate through groups
        
		echo "</tbody><tfoot><tr><td colspan=\"6\">";
        
        jimport("joomla.html.pagination");
        
        $pagination = new JPagination($this->noOfGroups, $limitstart, $limit);
        echo $pagination->getListFooter();
        
        echo "<input type=\"hidden\" name=\"option\" value=\"com_whelpdesk\" />";
		echo "<input type=\"hidden\" name=\"act\" value=\"rites\" />";
		echo "<input type=\"hidden\" name=\"boxchecked\" value=\"0\" />";
		echo "<input type=\"hidden\" name=\"hidemainmenu\" value=\"0\" />";
        
        echo "</td></tr></tfoot></table>";
	}
}

class watsUserGroupCategoryPermissionSetSetHTML extends watsUserGroupCategoryPermissionSetSet
{

	/**
	 * Converts a PHP array into a JS array string
	 */
	function _arrayToJSArray( $array, $prepend=null, $append=null )
	{
		$jsArray = 'new Array( ';
		foreach ( $this->watsUserGroupCategoryPermissionSet as $set )
		{
			$jsArray .= "'".$prepend.$set->catid.$append."', ";
		}
		$jsArray = substr( $jsArray, 0, strlen( $jsArray ) - 2 );
		$jsArray .= ' )';	
		return $jsArray;
	}

	function viewMatrix()
	{
	// prepare javascript array
	$idArray = array();
  	// iterate through permission sets
	foreach ( $this->watsUserGroupCategoryPermissionSet as $set )
	{
		$idArray[] = $set->catid;
	}
	
	echo "<br/>
	<table class=\"adminform\"> 
	  <tr> 
		<th>Quick Update</th> 
		<th>View</th> 
		<th>Make</th> 
		<th>Reply</th> 
		<th>Close</th> 
		<th>Delete</th> 
		<th>Purge</th> 
		<th>Assign</th> 
		<th>ReOpen</th> 
	  </tr>
	  <tr><td>&nbsp;</td>
			<td><select name=\"v\" onChange=\"updateControls( ".$this->_arrayToJSArray( $idArray, '', 'v' ).", this )\">
			  <option value=\"0\" SELECTED> </option>
			  <option value=\"1\" >Own</option>
			  <option value=\"2\">All</option>
			</select></td>
			<td><select name=\"m\" onChange=\"updateControls( ".$this->_arrayToJSArray( $idArray, '', 'm' ).", this )\">
			  <option value=\"0\" SELECTED> </option>
			  <option value=\"1\">Own</option>
			</select></td>
			<td><select name=\"r\" onChange=\"updateControls( ".$this->_arrayToJSArray( $idArray, '', 'r' ).", this )\">
			  <option value=\"0\" SELECTED> </option>
			  <option value=\"1\" >Own</option>
			  <option value=\"2\">All</option>
			</select></td>
			<td><select name=\"c\" onChange=\"updateControls( ".$this->_arrayToJSArray( $idArray, '', 'c' ).", this )\">
			  <option value=\"0\" SELECTED> </option>
			  <option value=\"1\" >Own</option>
			  <option value=\"2\">All</option>
			</select></td>
			<td><select name=\"d\" onChange=\"updateControls( ".$this->_arrayToJSArray( $idArray, '', 'd' ).", this )\">
			  <option value=\"0\" SELECTED> </option>
			  <option value=\"1\" >Own</option>
			  <option value=\"2\">All</option>
			</select></td>
			<td><select name=\"p\" onChange=\"updateControls( ".$this->_arrayToJSArray( $idArray, '', 'p' ).", this )\">
			  <option value=\"0\" SELECTED> </option>
			  <option value=\"1\" >Own</option>
			  <option value=\"2\">All</option>
			</select></td>
			<td><select name=\"a\" onChange=\"updateControls( ".$this->_arrayToJSArray( $idArray, '', 'a' ).", this )\">
			  <option value=\"0\" SELECTED> </option>
			  <option value=\"1\" >Own</option>
			  <option value=\"2\">All</option>
			</select></td>
			<td><select name=\"o\" onChange=\"updateControls( ".$this->_arrayToJSArray( $idArray, '', 'o' ).", this )\">
			  <option value=\"0\" SELECTED> </option>
			  <option value=\"1\" >Own</option>
			  <option value=\"2\">All</option>
			</select></td>
		</tr>
	  <tr> 
		<th>Category</th> 
		<th>View</th> 
		<th>Make</th> 
		<th>Reply</th> 
		<th>Close</th> 
		<th>Delete</th> 
		<th>Purge</th> 
		<th>Assign</th> 
		<th>ReOpen</th> 
	  </tr>";
  	// iterate through permission sets
	foreach ( $this->watsUserGroupCategoryPermissionSet as $set )
	{
		echo "<tr><td>".$set->categoryname."</td>
			<td><select name=\"".$set->catid."v\" id=\"".$set->catid."v\">
			  <option value=\"0\"> </option>
			  <option value=\"1\" "; 
		echo ( $set->checkPermission( 'V' ) == 1 ) ? "SELECTED" : "" ;
		echo ">Own</option>
			  <option value=\"2\" "; 
		echo ( $set->checkPermission( 'V' ) == 2 ) ? "SELECTED" : "" ;
		echo ">All</option>
			</select></td>
			<td><select name=\"".$set->catid."m\" id=\"".$set->catid."m\">
			  <option value=\"0\"> </option>
			  <option value=\"1\" "; 
		echo ( $set->checkPermission( 'M' ) == 1 ) ? "SELECTED" : "" ;
		echo ">Own</option>
			</select></td>
			<td><select name=\"".$set->catid."r\" id=\"".$set->catid."r\">
			  <option value=\"0\"> </option>
			  <option value=\"1\" "; 
		echo ( $set->checkPermission( 'R' ) == 1 ) ? "SELECTED" : "" ;
		echo ">Own</option>
			  <option value=\"2\" "; 
		echo ( $set->checkPermission( 'R' ) == 2 ) ? "SELECTED" : "" ;
		echo ">All</option>
			</select></td>
			<td><select name=\"".$set->catid."c\" id=\"".$set->catid."c\">
			  <option value=\"0\"> </option>
			  <option value=\"1\" "; 
		echo ( $set->checkPermission( 'C' ) == 1 ) ? "SELECTED" : "" ;
		echo ">Own</option>
			  <option value=\"2\" "; 
		echo ( $set->checkPermission( 'C' ) == 2 ) ? "SELECTED" : "" ;
		echo ">All</option>
			</select></td>
			<td><select name=\"".$set->catid."d\" id=\"".$set->catid."d\">
			  <option value=\"0\"> </option>
			  <option value=\"1\" "; 
		echo ( $set->checkPermission( 'D' ) == 1 ) ? "SELECTED" : "" ;
		echo ">Own</option>
			  <option value=\"2\" "; 
		echo ( $set->checkPermission( 'D' ) == 2 ) ? "SELECTED" : "" ;
		echo ">All</option>
			</select></td>
			<td><select name=\"".$set->catid."p\" id=\"".$set->catid."p\">
			  <option value=\"0\"> </option>
			  <option value=\"1\" "; 
		echo ( $set->checkPermission( 'P' ) == 1 ) ? "SELECTED" : "" ;
		echo ">Own</option>
			  <option value=\"2\" "; 
		echo ( $set->checkPermission( 'P' ) == 2 ) ? "SELECTED" : "" ;
		echo ">All</option>
			</select></td>
			<td><select name=\"".$set->catid."a\" id=\"".$set->catid."a\">
			  <option value=\"0\"> </option>
			  <option value=\"1\" "; 
		echo ( $set->checkPermission( 'A' ) == 1 ) ? "SELECTED" : "" ;
		echo ">Own</option>
			  <option value=\"2\" "; 
		echo ( $set->checkPermission( 'A' ) == 2 ) ? "SELECTED" : "" ;
		echo ">All</option>
			</select></td>
			<td><select name=\"".$set->catid."o\" id=\"".$set->catid."o\">
			  <option value=\"0\"> </option>
			  <option value=\"1\" "; 
		echo ( $set->checkPermission( 'O' ) == 1 ) ? "SELECTED" : "" ;
		echo ">Own</option>
			  <option value=\"2\" "; 
		echo ( $set->checkPermission( 'O' ) == 2 ) ? "SELECTED" : "" ;
		echo ">All</option>
			</select></td>
		</tr>";
	}
	// end iterate through permission sets
	echo "</table>";
	}
	
}

/**
 * @version 1.0
 * @created 07-May-2006 15:49:25
 */
class watsDatabaseMaintenanceHTML extends watsDatabaseMaintenance
{

	/**
	 * 
	 * @param database
	 */
	function performMaintenance()
	{
		$performOrphanUsers = $this->performOrphanUsers();
		$performOrphanPermissionSets = $this->performOrphanPermissionSets();
		$performOrphanTickets = $this->performOrphanTickets();
		$performOrphanMessages = $this->performOrphanMessages();
		$performMissingPermissionSets = $this->performMissingPermissionSets();
		$performUserPermissionsFormat = $this->performUserPermissionsFormat();
		$performPermissionSetsFormat = $this->performPermissionSetsFormat();
		$total = $performOrphanUsers + $performMissingPermissionSets + $performUserPermissionsFormat + $performPermissionSetsFormat + $performOrphanPermissionSets + $performOrphanTickets + $performOrphanMessages;
		echo "<table class=\"adminlist\"> 
              <thead>
                  <tr> 
                    <th class=\"title\"> Action</th> 
                    <th class=\"title\"> # of Inconsistencies</th> 
                    <th class=\"title\"> Action Resolved</th> 
                  </tr> 
              </thead>
			  <tr class=\"row0\"> 
				<td width=\"350\">Orphan users </td> 
				<td width=\"150\"><b>".$performOrphanUsers."</b> Inconsistencies</td> 
				<td width=\"100\">";
		echo ( $performOrphanUsers > 0 ) ? "<img src=\"images/tick.png\" border=\"0\" alt=\"tick\" />" : "&nbsp;" ;
		echo"</td> 
			  </tr>  
			  <tr class=\"row1\"> 
				<td width=\"350\"> Orphan permission sets </td> 
				<td width=\"150\"><b>".$performOrphanPermissionSets."</b> Inconsistencies</td> 
				<td width=\"100\">";
		echo ( $performOrphanPermissionSets > 0 ) ? "<img src=\"images/tick.png\" border=\"0\" alt=\"tick\" />" : "&nbsp;" ;
		echo"</td> 
			  </tr>
			  <tr class=\"row0\"> 
				<td width=\"350\">Orphan tickets </td> 
				<td width=\"150\"><b>".$performOrphanTickets."</b> Inconsistencies</td> 
				<td width=\"100\">";
		echo ( $performOrphanTickets > 0 ) ? "<img src=\"images/tick.png\" border=\"0\" alt=\"tick\" />" : "&nbsp;" ;
		echo"</td> 
			  </tr> 
			  <tr class=\"row1\"> 
				<td width=\"350\">Orphan messages </td> 
				<td width=\"150\"><b>".$performOrphanMessages."</b> Inconsistencies</td> 
				<td width=\"100\"";
		echo ( $performOrphanMessages > 0 ) ? "<img src=\"images/tick.png\" border=\"0\" alt=\"tick\" />" : "&nbsp;" ;
		echo"</td> 
			  </tr> 
			 <tr class=\"row0\"> 
				<td width=\"350\"> Missing permission sets </td> 
				<td width=\"150\"><b>".$performMissingPermissionSets."</b> Inconsistencies</td> 
				<td width=\"100\">";
		echo ( $performMissingPermissionSets > 0 ) ? "<img src=\"images/tick.png\" border=\"0\" alt=\"tick\" />" : "&nbsp;" ;
		echo"</td> 
			  </tr>
			  <tr class=\"row1\"> 
				<td width=\"350\">Permission set format </td> 
				<td width=\"150\"><b>".$performPermissionSetsFormat."</b> Inconsistencies</td> 
				<td width=\"100\">";
		echo ( $performPermissionSetsFormat > 0 ) ? "<img src=\"images/tick.png\" border=\"0\" alt=\"tick\" />" : "&nbsp;" ;
		echo"</td> 
			  </tr> 
              <tr class=\"row0\"> 
                <td width=\"350\">User permissions format </td> 
                <td width=\"150\"><b>".$performUserPermissionsFormat."</b> Inconsistencies</td> 
                <td width=\"100\">&nbsp;</td> 
              </tr> 
              <tfoot>
                  <tr> 
                    <td><strong>7</strong> Actions Performed</td> 
                    <td><b>".$total."</b> Inconsistencies found</td> 
                    <td><b>".$total."</b> Inconsistencies resolved</td> 
                  </tr>
              </tfoot>
            </table>";
	}
}

?>