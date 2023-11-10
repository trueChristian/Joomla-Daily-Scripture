<?php
/*----------------------------------------------------------------------------------|  io.vdm.dev  |----/
			Vast Development Method
/-------------------------------------------------------------------------------------------------------/

    @package    getBible.net

    @created    3rd December, 2015
    @author     Llewellyn van der Merwe <https://getbible.net>
    @git        Get Bible <https://git.vdm.dev/getBible>
    @github     Get Bible <https://github.com/getBible>
    @support    Get Bible <https://git.vdm.dev/getBible/support>
    @copyright  Copyright (C) 2015. All Rights Reserved
    @license    GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

/------------------------------------------------------------------------------------------------------*/

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
	<?php if ($params->get('link', 3) == 2): ?>
		<a href="<?php echo $today->local_link; ?>"><?php echo $today->date; ?></a>
	<?php elseif ($params->get('link', 2) == 2): ?>
		<a href="<?php echo $today->getbible; ?>" target="_blank"><?php echo $today->date; ?></a>
	<?php elseif ($params->get('link', 2) == 1): ?>
		<a href="https://t.me/s/<?php echo $today->telegram; ?>" target="_blank"><?php echo $today->date; ?></a>
	<?php else: ?>
		<p><?php echo $today->date; ?></p>
	<?php endif; ?>
	<?php if ($today->comments): ?>
		<?php echo $today->comments; ?>
	<?php endif; ?>
<?php elseif ($params->get('type', 1) == 2 && $today->telegram): ?>
	<?php echo $today->telegram; ?>
	<?php if ($today->comments): ?>
		<?php echo $today->comments; ?>
	<?php endif; ?>
<?php else: ?>
	<?php echo JText::_('MOD_DAILYSCRIPTURE_THERE_WAS_AN_ERROR_LOADING_THE_DAILY_SCRIPTURE_PLEASE_TRY_AGAIN_LATTER'); ?>
<?php endif; ?>
