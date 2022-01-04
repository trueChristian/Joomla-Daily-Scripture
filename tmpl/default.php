<?php
/*-------------------------------------------------------------------------------------------------------------|  www.vdm.io  |------/
 ____                                                  ____                 __               __               __
/\  _`\                                               /\  _`\   __         /\ \__         __/\ \             /\ \__
\ \,\L\_\     __   _ __    ___ ___     ___     ___    \ \ \/\ \/\_\    ____\ \ ,_\  _ __ /\_\ \ \____  __  __\ \ ,_\   ___   _ __
 \/_\__ \   /'__`\/\`'__\/' __` __`\  / __`\ /' _ `\   \ \ \ \ \/\ \  /',__\\ \ \/ /\`'__\/\ \ \ '__`\/\ \/\ \\ \ \/  / __`\/\`'__\
   /\ \L\ \/\  __/\ \ \/ /\ \/\ \/\ \/\ \L\ \/\ \/\ \   \ \ \_\ \ \ \/\__, `\\ \ \_\ \ \/ \ \ \ \ \L\ \ \ \_\ \\ \ \_/\ \L\ \ \ \/
   \ `\____\ \____\\ \_\ \ \_\ \_\ \_\ \____/\ \_\ \_\   \ \____/\ \_\/\____/ \ \__\\ \_\  \ \_\ \_,__/\ \____/ \ \__\ \____/\ \_\
    \/_____/\/____/ \/_/  \/_/\/_/\/_/\/___/  \/_/\/_/    \/___/  \/_/\/___/   \/__/ \/_/   \/_/\/___/  \/___/   \/__/\/___/  \/_/

/------------------------------------------------------------------------------------------------------------------------------------/

	@version		2.0.x
	@created		22nd October, 2015
	@package		Sermon Distributor
	@subpackage		default.php
	@author			Llewellyn van der Merwe <https://truechristian.church/>	
	@copyright		Copyright (C) 2015. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html 

	A sermon distributor that links to Dropbox. 

/----------------------------------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');


?>
<?php if ($today->scripture): ?>
	<<?php echo $params->get('name_header', 'h3'); ?>><?php echo $today->name; ?></<?php echo $params->get('name_header', 'h3'); ?>>
	<ul style="list-style-type: none;">
	<?php foreach ($today->scripture as $scripture): ?>
		<li><b><?php echo $scripture->nr; ?></b> <?php echo $scripture->text; ?></li>
	<?php endforeach; ?>
	</ul>
	<?php if ($params->get('link', 1) == 1): ?>
		<a href="https://t.me/s/<?php echo $today->telegram; ?>" target="_blank"><?php echo $today->date; ?></a>
	<?php else: ?>
		<?php echo $today->date; ?>
	<?php endif; ?>
<?php else: ?>
	<?php echo JText::_('MOD_DAILYSCRIPTURE_THERE_WAS_AN_ERROR_PLEASE_TRY_AGAIN_LATTER'); ?>
<?php endif; ?>
