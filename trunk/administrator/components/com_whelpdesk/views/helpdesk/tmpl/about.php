<?php

JHTML::_('behavior.mootools');

$document = JFactory::getDocument();

$document->addScript('components/com_whelpdesk/assets/javascript/class.noobSlide.packed.js');


$document->addStyleDeclaration("/* Sample 8*/
#box8{
	position:absolute;
}
#box8 div{
	width:480px;
	float:left;
}
#box8 .buttons{
	text-align:left
}
#box8 .next{
    display: block;
	float:right
}
.sample8 .buttons{
	text-align:center;
	clear:both;
}
.sample8 .mask1{
	border-top:1px solid #ccc;
	border-bottom:1px solid #ccc;
}

/* ********************************** */

.thumbs{
	width:54px;
}
.thumbs div{
	display:block;
	width:54px;
	height:41px;
	margin:3px 0;
	cursor:pointer;
}

.thumbs div img{
	display:block;
	width:100%;
	height:100%;
	border:none
}

.info{
	width:240px;
	height:50px;
	background:#000;
	position:absolute;
}
.info p, .info h4{
	color:#fff;
	padding:3px 8px;
	font-family:Arial;
}
.info h4{
	font-size:14px;
}
.info h4 a{
	float:right;
	background:#fff;
	color:#000;
	font-size:10px;
	padding:0 3px;
	text-decoration:none
}

.mask1{
	position:relative;
	width:480px;
	height:180px;
	overflow:hidden;
}
.mask2{
	position:relative;
	width:240px;
	height:180px;
	overflow:hidden;
}
.mask3{
	position:relative;
	width:480px;
	height:240px;
	overflow:hidden;
}

span img{
	display:block;
	border:none;
}");

?>

<script type="text/javascript">
    window.addEvent('domready', function(){
    
        var slides = {
            current: 0,
            panes:   new Array()
        };

        slides.panes[0] = new Fx.Slide('slidesPane1');
        slides.panes[1] = new Fx.Slide('slidesPane2');
        slides.panes[2] = new Fx.Slide('slidesPane3');
        slides.panes[3] = new Fx.Slide('slidesPane4');
        
        slides.panes[1].slideOut();
        slides.panes[2].slideOut();
        slides.panes[3].slideOut();

        $('slidesNext').addEvent('click', function(e){
            e.stop();
            slides.current = slides.current + 1;
            if (slides.current > slides.panes.length - 1) {
                slides.current = 0;
            }
            
            for (var i = 0; i < slides.panes.length; i++) {
                if (i == slides.current) {
                    slides.panes[i].slideIn();
                } else {
                    slides.panes[i].slideOut();
                }
            }
        });
        
        $('slidesPrev').addEvent('click', function(e){
            e.stop();
            slides.current = slides.current - 1;
            if (slides.current < 0) {
                slides.current = slides.panes.length - 1;
            }
            
            for (var i = 0; i < slides.panes.length; i++) {
                if (i == slides.current) {
                    slides.panes[i].slideIn();
                } else {
                    slides.panes[i].slideOut();
                }
            }
        });

	});
</script>

<p class="buttons" style="height: 2em; background-color: #F6F6F6; padding: 1em; border: 1px solid #CCCCCC; margin-bottom: 0px;">
    <span class="prev" id="slidesPrev" style="float: left;  background-color: #0B55C4; color: #FFFFFF; padding: 0.2em 1em; cursor: pointer;">&lt;&lt; Previous</span>
    <span class="next" id="slidesNext" style="float: right; background-color: #0B55C4; color: #FFFFFF; padding: 0.2em 1em; cursor: pointer;">Next &gt;&gt;</span>
</p>

<div style="background-color: #FBFBFB; padding: 1em; border: 1px solid #CCCCCC; border-top: 0px;">
<div id="slidesPane1">
    <h1>Overview</h1>
    
    <p>The Webamoeba Help Desk is a Joomla! 1.6 component. Previously called Webamoeba Ticket System, or WATS for short, the component provides a comprehensive help desk solution for small to medium sized businesses.</p>
    
    <h2>Developers</h2>

    <p>James Kennard</p>
    <p><a href="http://www.webamoeba.co.uk">www.webamoeba.co.uk</a></p>

    <h2>Getting Help</h2>

    <div style="float: left; height:32px; width:32px; overflow: hidden; margin-right: 10px;">
    <img
         src="templates/khepri/images/toolbar/icon-32-help.png">
    </div>
    <p>Having difficulties configuring the Webamoeba Help Desk? Try using the Help buttons in the toolbar!<br />
    Still having problems? Why not mosey on down to the <a href="http://joomlacode.org/gf/project/wats/forum/" target="_blank">forums</a>.</p>
    
    <h2>Support Webamoeba Help Desk</h2>
    
    <p>If you like the Webamoeba Help Desk and you want to help support the component, please wire a review or submit your rating of the component at <a href="http://extensions.joomla.org/extensions/clients/help-desk/151/details" target="_blank">extensions.joomla.org</a></p>
</div>
<div id="slidesPane2">
    <h1>Translations</h1>

    <p>
    <img src="components/com_whelpdesk/assets/flags/gb.png"> English - James Kennard <a href="mailto:james@webamoeba.com">james@webamoeba.com</a> (<a href="http://www.webamoeba.co.uk" target="_blank">www.webamoeba.co.uk</a>)
    </p>
    <p>
    <img src="components/com_whelpdesk/assets/flags/fr.png"> French - Johan Aubry <a href="mailto:jaubry@a-itservices.com">jaubry@a-itservices.com</a> (<a href="http://www.a-itservices.com" target="_blank">www.a-itservices.com</a>)
    </p>
    <p>
    <img src="components/com_whelpdesk/assets/flags/de.png"> German - Chr.G&auml;rtner
    </p>
    <p>
    <img src="components/com_whelpdesk/assets/flags/pt.png"> Portuguese - Jorge Rosado <a href="mailto:info@jrpi.pt">info@jrpi.pt</a> (<a href="http://www.jrpi.pt" target="_blank">www.jrpi.pt</a>)
    </p>
    <p>
    <img src="components/com_whelpdesk/assets/flags/si.png"> Slovak - Daniel K·Ëer <a href="mailto:kacer@aceslovakia.sk">kacer@aceslovakia.sk</a> (<a href="http://www.aceslovakia.sk" target="_blank">www.aceslovakia.sk</a>)
    </p>
    <p>
    <img src="components/com_whelpdesk/assets/flags/it.png"> Italian - Leonardo Lombardi (<a href="http://www.dimsat.unicas.it" target="_blank">www.dimsat.unicas.it</a>)
    </p>
    <p>
    <img src="components/com_whelpdesk/assets/flags/es.png"> Spanish - Urano Gonzalez <a href="mailto:urano@uranogonzalez.com">urano@uranogonzalez.com</a> (<a href="http://www.uranogonzalez.com" target="_blank">www.uranogonzalez.com</a>)
    </p>
    <p>
    <img src="components/com_whelpdesk/assets/flags/se.png"> Swedish  - Thomas Westman <a href="mailto:Westman%20info@backupnow.se">info@backupnow.se</a> (<a href="http://www.backupnow.se" target="_blank">www.backupnow.se</a>)
    </p>
</div>
<div id="slidesPane3">
	<h1>Special Thanks</h1>
	<ul>
        <li>72dpi</li>
        <li>ateul</li>
        <li>backupnow</li>
        <li>claudio</li>
        <li>DanielMD</li>
        <li>elmar</li>
        <li>gaertner65</li>
        <li>gdude66</li>
        <li>jrpi</li>
        <li>laurie_lewis</li>
        <li>lexel</li>
        <li>peternie</li>
        <li>ravenswood</li>
        <li>Skye</li>
        <li>tvinhas</li>
        <li>urano</li>
    </ul>
</div>
<div id="slidesPane4">
    <iframe src="http://rcm-uk.amazon.co.uk/e/cm?t=mythix-21&o=2&p=8&l=as1&asins=1847192823&fc1=000000&IS2=1&lt1=_blank&m=amazon&lc1=0B55C4&bc1=FFFFFF&bg1=FBFBFB&f=ifr"
            style="width:120px;height:240px; float: right; margin-left: 5px; margin-bottom: 5px; " 
            scrolling="no" 
            marginwidth="0" 
            marginheight="0" 
            frameborder="0">
    </iframe>
	<h1>Support Joomla!</h1>
    <p>When you buy a book from Packt Publishing that is all about Joomla!, you not only get a great book
    you also get the satisfaction of knowing that you are helping the Joomla! project. But how can this be?
    Joomla! and Packt aren't linked...!! Packt have an Open Source royalty policy. This means that whenever
    anyone buys a book from Packt about an Open Source project Packt makes a donation to that project.</p>
    <p>Why not show your support for Joomla! buy buying a Joomla! book from Packt today.</p>
	<h2>Mastering Joomla! 1.5 Extension and Framework Development</h2>
    <!--<a href="#" title="Mastering Joomla! 1.5 Extension and Framework Development" onclick="window.open('http://www.amazon.co.uk/gp/product/1847192823?ie=UTF8&tag=mythix-21&linkCode=as2&camp=1634&creative=6738&creativeASIN=1847192823','help','scrollbars=yes,resizable=yes,width=600,height=725,left=180,top=20'); return false;"><img title="Mastering Joomla! 1.5 Extension and Framework Development" class="left" alt="Mastering Joomla! 1.5 Extension and Framework Development" src="http://ecx.images-amazon.com/images/I/51rVtasYRqL._SL500_AA240_.jpg" widtht="99" border="0" heightt="123" style="float: right; padding-left: 30px;"></a>-->
    <ul>
        <li>In-depth guide to programming Joomla!</li>
        <li>Design and build secure and robust components, modules and plugins</li>
        <li>Includes a comprehensive reference to the major areas of the Joomla! framework</li>
    </ul>
    <h3>In Detail</h3>
    <p>Joomla! is the world's hottest open-source content management system, and the winner of the 2006 Open Source CMS Prize. Out of the box, Joomla! does a great job of managing the content needed to make your website sing. But for many people, the true power of Joomla! lies in its application framework that makes it possible for thousands of developers around the world to create powerful add-ons and extensions. Many companies or organizations have requirements that go beyond what is available in the basic Joomla! package or in a freely available extension. Thankfully, Joomla! offers a powerful application framework that makes it easy for developers to create sophisticated add-ons that extend the power of Joomla! into virtually unlimited directions.<br><br>If you use PHP programming to extend or customize Joomla!, this book is essential reading. If you feel that you've mastered the basics of creating Joomla! extensions, then this book will take you to the next level. Packed with expert advice on all aspects of development with Joomla!, you will learn about best-practice design and coding for Joomla! components, modules, plugins and other extensions.</p>
    <p>You will also learn about customizing the page output, using JavaScript effects, making use of Web Services from within Joomla! and ensuring that your code is secure and error-free.</p>
    <p>A unique and comprehensive reference to the main areas of interest within the Joomla! framework is also included in the book.</p>
</div>
</div>