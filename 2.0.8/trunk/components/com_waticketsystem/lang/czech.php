<?php
/**
 * FileName: czech.php
 * Date: 09/03/2007
 * License: GNU General Public License
 * File Version #: 1
 * WATS Version #: 2.0.6
 * Author: Lukáš Nìmec info@vtiger-crm.cz (www.vtiger-crm.cz)
 **/

// NAVIGATION
DEFINE("_WATS_NAV_NEW","Nový Ticket");
DEFINE("_WATS_NAV_CATEGORY","Kategorie podpory");
DEFINE("_WATS_NAV_TICKET","Èíslo Ticketu");

// USER
DEFINE("_WATS_USER","Uživatel");
DEFINE("_WATS_USER_SET","Uživatelé");
DEFINE("_WATS_USER_NAME","Jméno");
DEFINE("_WATS_USER_USERNAME","Uživ. jméno");
DEFINE("_WATS_USER_GROUP","Skupina");
DEFINE("_WATS_USER_ORG","Firma");
DEFINE("_WATS_USER_ORG_SELECT","Zadajte firmu");
DEFINE("_WATS_USER_EMAIL","Email");
DEFINE("_WATS_USER_NEW","Vytvoøit nového uživat.");
DEFINE("_WATS_USER_NEW_SELECT","Vybrat uživatela");
DEFINE("_WATS_USER_NEW_CREATED","Vytvoøení uživatelie");
DEFINE("_WATS_USER_NEW_FAILED","Tento užívatel už má úèet:");
DEFINE("_WATS_USER_DELETED","Užívatel smazán");
DEFINE("_WATS_USER_EDIT","Upravit uživatele");
DEFINE("_WATS_USER_DELETE_REC","Odstranit Tickety uživatela (doporuèeno)");
DEFINE("_WATS_USER_DELETE_NOTREC","Odstranit Tickety užívatele i jeho odpovìdi na jiné tickety (nedoporuèeno)");
DEFINE("_WATS_USER_DELETE","Smazat uživatele");
DEFINE("_WATS_USER_ADD","Pøidat uživatele");
DEFINE("_WATS_USER_SELECT","Vyber uživatele");
DEFINE("_WATS_USER_SET_DESCRIPTION","Správa uživatelù");
DEFINE("_WATS_USER_ADD_LIST","Následující uživatelé byly pøidáni");

// GROUPS
DEFINE("_WATS_GROUP_SELECT","Vyber skupinu");

// CATEGORIES
DEFINE("_WATS_CATEGORY","Kategorie");

// TICKETS
DEFINE("_WATS_TICKETS_USER_OPEN","Moje Otevøené Tickety");
DEFINE("_WATS_TICKETS_USER_CLOSED","Moje Uzavøené Tickety");
DEFINE("_WATS_TICKETS_OPEN","Otevøené Tickety");
DEFINE("_WATS_TICKETS_CLOSED","Uzavøené Tickety");
DEFINE("_WATS_TICKETS_DEAD","Neaktivní Tickety");
DEFINE("_WATS_TICKETS_OPEN_VIEW","Zobrazit všechny otevøené tickety");
DEFINE("_WATS_TICKETS_CLOSED_VIEW","Zobrazit všechny uzavøené tickety");
DEFINE("_WATS_TICKETS_DEAD_VIEW","Zobrazit všechny neaktivní tickety");
DEFINE("_WATS_TICKETS_NAME","Název Ticketu");
DEFINE("_WATS_TICKETS_POSTS","Vytvoøeno");
DEFINE("_WATS_TICKETS_DATETIME","Poslední zápis");
DEFINE("_WATS_TICKETS_PAGES","Stránek");
DEFINE("_WATS_TICKETS_SUBMIT","Vytvoøit nový ticket");
DEFINE("_WATS_TICKETS_SUBMITING","Vytváøení ticketu");
DEFINE("_WATS_TICKETS_SUBMITTED","Ticket je vytvoøen");
DEFINE("_WATS_TICKETS_DESC","Popis");
DEFINE("_WATS_TICKETS_CLOSE","Uzavøít Ticket");
DEFINE("_WATS_TICKETS_CLOSED_COMP","Ticket uzavøen");
DEFINE("_WATS_TICKETS_DELETED_COMP","Ticket vymazán");
DEFINE("_WATS_TICKETS_PURGED_COMP","Ticket uvolnìn");
DEFINE("_WATS_TICKETS_NONE","nebyli nlezeny tickety");
DEFINE("_WATS_TICKETS_FIRSTPOST","zaèátek: ");
DEFINE("_WATS_TICKETS_LASTPOST","poslední reakce: ");
DEFINE("_WATS_TICKETS_REPLY","Odpovìdìt");
DEFINE("_WATS_TICKETS_REPLY_CLOSE","Odpovìdìt a uzavøít");
DEFINE("_WATS_TICKETS_ASSIGN","Pøidìlit ticket");
DEFINE("_WATS_TICKETS_ASSIGNEDTO","Pøidìlit");
DEFINE("_WATS_TICKETS_ID","Ticket ID");
DEFINE("_WATS_TICKETS_REOPEN","Znovu otevøít");
DEFINE("_WATS_TICKETS_REOPEN_REASON","Prosím, udejte dùvod, proè jste znovu otevøeli tento Ticket");
DEFINE("_WATS_TICKETS_STATE_ALL","Vše");
DEFINE("_WATS_TICKETS_STATE_PERSONAL","Osobní");
DEFINE("_WATS_TICKETS_STATE_OPEN","Otevøené");
DEFINE("_WATS_TICKETS_STATE_CLOSED","Uzavøené");
DEFINE("_WATS_TICKETS_STATE_DEAD","Neaktivní");
DEFINE("_WATS_TICKETS_PURGE","Uvolnit neaktivní tickety v ");

//MAIL
DEFINE("_WATS_MAIL_TICKET","Byl vytvoøen Ticket: ");
DEFINE("_WATS_MAIL_REPLY","Byla vytvoøena Odpovìï: ");

//MISC
DEFINE("_WATS_MISC_DELETE_VERIFY","Vymazat ?");
DEFINE("_WATS_MISC_GO","Provést");

//ERRORS
DEFINE("_WATS_ERROR","Vyskytla sa chyba");
DEFINE("_WATS_ERROR_ACCESS","Nemáte dostateèné oprávnìní pro uzavøení tohoto požadavku");
DEFINE("_WATS_ERROR_NOUSER","Némate oprávnìní prohlížet tyto zdroje.<br>Pøihlaste se nebo požádejte administrátora o údaje pro pøístup.");
DEFINE("_WATS_ERROR_NODATA","Nevyplnili jste správnì formuláø, prosím zkuste to znovu.");
DEFINE("_WATS_ERROR_NOT_FOUND","Položka nenalezena");

//BBCODE
DEFINE("_WATS_BB_HELP","<p><i>Pro úèely formátování vašeho textu mùžete použít tyto 'znaèky':</i></p> 
<table width='100%'border='0'cellspacing='5'cellpadding='0'> 
  <tr valign='top'> 
    <td><b>bold</b></td> 
    <td><b>[b]</b>bold<b>[/b]</b></td> 
  </tr> 
  <tr valign='top'> 
    <td><i>italic</i> </td> 
    <td><b>[i]</b>italic<b>[/i]</b></td> 
  </tr> 
  <tr valign='top'> 
    <td> <u>underline</u></td> 
    <td><b>[u]</b>underline<b>[/u]</b></td> 
  </tr> 
  <tr valign='top'> 
    <td>code</td> 
    <td><b>[code]</b>value='123';<b>[/code] </b></td> 
  </tr> 
  <tr valign='top'> 
    <td><font size='+2'>SIZE</font></td> 
    <td><b>[size=25]</b>HUGE<b>[/size]</b></td> 
  </tr> 
  <tr valign='top'> 
    <td><font color='#FF0000'>RED</font></td> 
    <td><b>[color=red]</b>RED<b> [/color]</b></td> 
  </tr> 
  <tr valign='top'> 
    <td style='cursor: pointer; color: #0000FF;'><u>weblink </u></td> 
    <td><b>[url=http://vtiger-crm.cz]vtiger[/url]</b></td> 
  </tr> 
  <tr valign='top'> 
    <td style='cursor: pointer; color: #0000FF;'><u>support@vtiger-crm.cz</u></td> 
    <td><b>[email=support@vtiger-crm.cz]email[/email]</b></td> 
  </tr> 
</table> ");
?>
