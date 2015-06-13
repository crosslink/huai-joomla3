<?php
/**
 * @version		1.0.0
 * @package		JoomlaXTC Ocular for Joomla! 2.5.x
 * @author		JoomlaXTC http://www.joomlaxtc.com
 * @copyright	Copyright (C) 2014 Monev Software LLC. All rights reserved.
 * @license		http://opensource.org/licenses/GPL-2.0 GNU Public License, version 2.0
 */

defined( '_JEXEC' ) or die;

JHtml::_('behavior.framework');
$uri = JFactory::getURI();
$return = $uri->toString(array('path', 'query', 'fragment'));
$return_mobile = $return_default.'&mobilepref=mobile';
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<link rel="stylesheet" href="<?php echo $xtc->templateUrl; ?>css/bootstrap.min.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $xtc->templateUrl; ?>css/bootstrap-responsive.min.css" type="text/css" />
</head>
<body class="mobile">
	<center><?php echo $return; ?>
		Select a view mode for this site:
		<br/><br/>
		<form action="<?php echo $return; ?>" name="mobileform" method="GET" class="row row-fluid" style="width:20%">
			<button type="submit" class="btn btn-small pull-left" title="Default" onclick="javascript:mobileform.mobiledetect.value=0;">Default</a>
			<button type="submit" class="btn btn-small pull-right" title="Mobile" onclick="javascript:mobileform.mobiledetect.value=1;">Mobile</a>
			<input type="hidden" name="mobiledetect" value="" />
		</div>
	</center>
</body>
</html>