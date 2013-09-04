<?php
/*
 * Copyright MADE/YOUR/DAY OG <mail@madeyourday.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * RockSolid Icon Picker configuration
 *
 * @author Martin Auswöger <martin@madeyourday.net>
 */

$GLOBALS['BE_FFL']['rocksolid_icon_picker'] = 'MadeYourDay\\Contao\\Widget\\IconPicker';

$GLOBALS['TL_PURGE']['custom']['rocksolid_icon_picker'] = array(
	'callback' => array('MadeYourDay\\Contao\\Widget\\IconPicker', 'purgeCache'),
);
