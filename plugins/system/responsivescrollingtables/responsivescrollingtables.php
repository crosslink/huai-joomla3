<?php
/**
 * @package     ResponsiveScrollingTables
 *
 * @copyright   Copyright (C) 2014 T J Dixon Limited. All rights reserved. http://www.tjdixon.com/
 * @license     Licensed under the GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die;

/**
 * Responsive Scrolling Tables plugin class.
 */
class PlgSystemResponsivescrollingtables extends JPlugin
{
	public function onBeforeRender()
	{
		//$plugin = JPluginHelper::getPlugin('system', 'sslredirect');
		//$params = new JRegistry($this->params);
		if (strpos(JFactory::getURI(), 'layout=edit') === false)
		{
			JFactory::getDocument()->addScriptDeclaration('
			function responsiveTables(){for(var e=document.querySelectorAll("table"),t=0;t<e.length;t++)if(e[t].scrollWidth>e[t].parentNode.clientWidth&&("div"!=e[t].parentNode.tagName.toLowerCase()||"res-div"!=e[t].parentNode.getAttribute("data-responsive"))){var r=document.createElement("div"),o=e[t].parentNode;r.appendChild(document.createTextNode("' . $this->params->get('scrollRightText', '') . '")),r.appendChild(e[t].cloneNode(!0)),r.setAttribute("style","overflow-x:scroll;"),r.setAttribute("data-responsive","res-div"),o.replaceChild(r,e[t])}else if(e[t].scrollWidth<=e[t].parentNode.clientWidth&&"div"==e[t].parentNode.tagName.toLowerCase()&&"res-div"==e[t].parentNode.getAttribute("data-responsive")){var a=e[t].parentNode,d=a.parentNode;d.replaceChild(e[t].cloneNode(!0),a)}}window.addEventListener("resize",function(){responsiveTables()}),document.onreadystatechange=function(){"complete"==document.readyState&&responsiveTables()};
			');
			//JFactory::getDocument()->addScript(JURI::root() . 'media/plg_responsivescrollingtables/js/responsivescrollingtables.min.js.php?v=1.2.1');
		}
	}
}
