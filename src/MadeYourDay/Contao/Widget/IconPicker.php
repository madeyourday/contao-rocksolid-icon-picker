<?php
/*
 * Copyright MADE/YOUR/DAY OG <mail@madeyourday.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MadeYourDay\Contao\Widget;

/**
 * Icon picker widget
 *
 * @author Martin Auswöger <martin@madeyourday.net>
 */
class IconPicker extends \Widget
{
	/**
	 * @var boolean Submit user input
	 */
	protected $blnSubmitInput = true;

	/**
	 * @var string Template
	 */
	protected $strTemplate = 'be_widget';

	/**
	 * Generate the widget and return it as string
	 *
	 * @return string
	 */
	public function generate()
	{
		$assetsDir = version_compare(VERSION, '4.0', '>=')
			? 'bundles/rocksolidiconpicker'
			: 'system/modules/rocksolid-icon-picker/assets';

		$GLOBALS['TL_JAVASCRIPT'][] = $assetsDir . '/js/be_main.js';
		$GLOBALS['TL_CSS'][] = $assetsDir . '/css/be_main.css';
		$this->loadLanguageFile('rocksolid_icon_picker');

		$fontPath = $GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['iconFont'];
		$fontPathNoSuffix = implode('.', explode('.', $fontPath, -1));

		if (!file_exists(TL_ROOT . '/' . $fontPath)) {
			return '<p class="tl_gerror"><strong>'
				. sprintf($GLOBALS['TL_LANG']['rocksolid_icon_picker']['font_not_found'], $fontPath)
				. '</strong></p>';
		}

		// IE does not support font names longer than 31 characters
		$fontFamily = substr('rip_font_' . md5($fontPath), 0, 31);

		$html = '<div class="rip_selected_icon" id="rip_selected_' . $this->strId . '" style="font-family: ' . $fontFamily . ';">';
		$html .= $this->varValue ? '&#x' . $this->varValue . ';' : '&nbsp;';
		$html .= '</div>';

		$html .= '<a href="#" class="tl_submit" onclick="ripOpen(\'rip_icons_' . $this->strId . '\'); return false;">' . $GLOBALS['TL_LANG']['rocksolid_icon_picker']['pick_icon'] . '</a>';

		$icons = $this->getIconsFromFont($fontPath);
		$searchEnabled = false;
		foreach ($icons as $icon) {
			if (!empty($icon['name'])) {
				$searchEnabled = true;
			}
		}

		$icons = array_merge(
			array(array('code' => '', 'name' => 'none')),
			$icons
		);

		$html .= '<div class="rip_icons rip_collapsed" id="rip_icons_' . $this->strId . '" style="font-family: ' . $fontFamily . ';">';
		$html .= '<div class="rip_icons_toolbar">';

		if ($searchEnabled) {
			$html .= $GLOBALS['TL_LANG']['MSC']['searchLabel'] . ': ';
			$html .= '<input type="search" id="rip_search_' . $this->strId . '" class="tl_text">';
		}

		$html .= '<a href="" class="rip_icons_toolbar_close">&#xd7;</a>';
		$html .= '</div>';

		$html .= '<span data-rip-codes="' . htmlspecialchars(json_encode($icons), ENT_QUOTES) . '"></span>';

		$html .= '</div>';

		/*
		$html .= '<script>(function(){';
		$html .= '$(\'rip_icons_' . $this->strId . '\').getChildren(\'a\').addEvent(\'click\', function(event){';
		$html .= '	$(\'ctrl_' . $this->strId . '\').set(\'value\', this.get(\'data-code\'));';
		$html .= '	$(\'rip_selected_' . $this->strId . '\').set(\'html\', this.get(\'html\'));';
		$html .= '	$(\'rip_icons_' . $this->strId . '\').addClass(\'rip_collapsed\');';
		$html .= '	event.preventDefault();';
		$html .= '});';

		if ($searchEnabled) {
			$html .= 'var updateSearch = function(event){';
			$html .= '	if (event && event.key === \'enter\') {';
			$html .= '		event.preventDefault();';
			$html .= '	}';
			$html .= '	var value = this.get(\'value\').replace(/[^a-z0-9_-]/gi, \'\');';
			$html .= '	if (value) {';
			$html .= '		var searchRegExp = new RegExp(value.split(\'\').join(\'.*?\'), \'i\');';
			$html .= '		$(\'rip_icons_' . $this->strId . '\').getChildren(\'a\').each(function(el){';
			$html .= '			if (el.get(\'data-name\') && searchRegExp.test(el.get(\'data-name\'))) {';
			$html .= '				el.setStyle(\'display\', \'\')';
			$html .= '			}';
			$html .= '			else {';
			$html .= '				el.setStyle(\'display\', \'none\')';
			$html .= '			}';
			$html .= '		});';
			$html .= '	}';
			$html .= '	else {';
			$html .= '		$(\'rip_icons_' . $this->strId . '\').getChildren(\'a\').each(function(el){';
			$html .= '			el.setStyle(\'display\', \'\')';
			$html .= '		});';
			$html .= '	}';
			$html .= '};';
			$html .= '$(\'rip_search_' . $this->strId . '\').addEvent(\'keydown\', updateSearch).addEvent(\'keyup\', updateSearch).addEvent(\'click\', updateSearch).addEvent(\'change\', updateSearch);';
		}

		$html .= '})();</script>';
		*/

		$html .= '<style>';
		$html .= '@font-face {';
		$html .= '	font-family: ' . $fontFamily . ';';
		$html .= '	src: url(\'' . $fontPathNoSuffix . '.eot\');';
		$html .= '	src: url(\'' . $fontPathNoSuffix . '.eot?#iefix\') format(\'embedded-opentype\'),';
		$html .= '	     url(\'' . $fontPathNoSuffix . '.woff\') format(\'woff\'),';
		$html .= '	     url(\'' . $fontPathNoSuffix . '.ttf\') format(\'truetype\'),';
		$html .= '	     url(\'' . $fontPathNoSuffix . '.svg#svg_fontregular\') format(\'svg\');';
		$html .= '	font-weight: normal;';
		$html .= '	font-style: normal;';
		$html .= '}';
		$html .= '</style>';

		$html .= '<input type="hidden" name="' . $this->strName . '" id="ctrl_' . $this->strId . '" value="' . $this->varValue . '">';

		return $html;
	}

	/**
	 * Get path and fullPath to the cache directory
	 *
	 * @return string
	 */
	public static function getCacheDirPaths()
	{
		if (version_compare(VERSION, '4.0', '>=')) {
			$cacheDir = \System::getContainer()->getParameter('kernel.cache_dir') . '/contao';
		}
		else {
			$cacheDir = TL_ROOT . '/system/cache';
		}

		$dirFullPath = $cacheDir . '/rocksolid_icon_picker';
		$dirPath = $dirFullPath;
		if (substr($dirPath, 0, strlen(TL_ROOT) + 1) === TL_ROOT . '/') {
			$dirPath = substr($dirPath, strlen(TL_ROOT) + 1);
		}

		return array(
			'path' => $dirPath,
			'fullPath' => $dirFullPath,
		);
	}

	/**
	 * Get the icon list from a SVG font and read class names from HTML or CSS
	 *
	 * @param  string $fontPath Path to the SVG font file
	 * @return array            All icons as arrays (code, name)
	 */
	static public function getIconsFromFont($fontPath)
	{
		if (!$fontPath || !file_exists(TL_ROOT . '/' . $fontPath)) {
			return array();
		}

		// calculate the cache key
		$cacheKey = md5_file(TL_ROOT . '/' . $fontPath);
		if (file_exists($infoFilePath = TL_ROOT . '/' . substr($fontPath, 0, -4) . '.html')) {
			$cacheKey = md5($cacheKey . md5_file($infoFilePath));
		}
		if (file_exists($infoFilePath = TL_ROOT . '/' . substr($fontPath, 0, -4) . '.css')) {
			$cacheKey = md5($cacheKey . md5_file($infoFilePath));
		}
		$cacheDirPaths = static::getCacheDirPaths();
		$cacheFilePath = $cacheDirPaths['path'] . '/' . $cacheKey . '.php';
		$cacheFileFullPath = $cacheDirPaths['fullPath'] . '/' . $cacheKey . '.php';
		if (file_exists($cacheFileFullPath)) {
			return include $cacheFileFullPath;
		}

		$font = new \SimpleXMLElement(TL_ROOT . '/' . $fontPath, null, true);
		if(
			!isset($font->defs[0]->font[0]->glyph) ||
			!count($font->defs[0]->font[0]->glyph)
		) {
			return array();
		}

		$glyphs = array();

		foreach ($font->defs[0]->font[0]->glyph as $xmlGlyph) {

			if ($xmlGlyph['unicode']) {

				$glyph = array();
				$char = (string)$xmlGlyph['unicode'];

				$unicode = unpack('N', mb_convert_encoding($char, 'UCS-4BE', 'UTF-8'));
				$glyph['code'] = dechex($unicode[1]);

				if(isset($xmlGlyph['glyph-name'])){
					$glyph['name'] = (string)$xmlGlyph['glyph-name'];
				}

				// ignore white space and control characters
				if (hexdec($glyph['code']) > 32 && !empty($xmlGlyph['d']) && (string)$xmlGlyph['d'] !== 'M0 0v0v0v0v0z') {
					$glyphs[] = $glyph;
				}
			}

		}

		if (
			file_exists($infoFilePath = TL_ROOT . '/' . substr($fontPath, 0, -4) . '.html') ||
			file_exists($infoFilePath = TL_ROOT . '/' . substr($fontPath, 0, -4) . '.css')
		) {

			$infoFileContents = file_get_contents($infoFilePath);

			if (
				preg_match_all(
					'(\\sdata-icon="(?P<key>[^"]+)".*?\\sclass="class-name"[^>]*>icon-(?P<value>[0-9a-z_-]*))is',
					$infoFileContents,
					$matches
				) ||
				preg_match_all(
					'((?P<value>[^}]+)?content\\s*:\\s*["\'](?P<key>[^"\']+)["\'])is',
					$infoFileContents,
					$matches
				)
			) {

				$iconNames = array_combine(array_map(function($key) {
					if (substr($key, 0, 3) === '&#x') {
						return rtrim(substr($key, 3), ';');
					}
					if (substr($key, 0, 1) === '\\') {
						return substr($key, 1);
					}
					if (mb_strlen($key, 'UTF-8') === 1) {
						return ltrim(implode('',
							unpack('H*', mb_convert_encoding($key, 'UCS-4BE', 'UTF-8'))
						), '0');
					}
					return '';

				}, $matches['key']), $matches['value']);

				$iconNames = array_map('trim', preg_replace(
					array('(/\\*.*?\\*/|:before|:after)is', '([^a-z0-9]+)is'),
					array('', ' '),
					$iconNames
				));

				foreach ($glyphs as $key => $glyph) {
					if (isset($iconNames[$glyph['code']]) && empty($glyph['name'])) {
						$glyphs[$key]['name'] = $iconNames[$glyph['code']];
					}
				}

			}

		}

		$cacheFile = new \File($cacheFilePath, true);
		$cacheFile->write('<?php' . "\n" . 'return ' . var_export($glyphs, true) . ';');
		$cacheFile->close();

		return $glyphs;
	}

	/**
	 * Purge cache files rocksolid_icon_picker/*.php
	 *
	 * @return void
	 */
	public static function purgeCache()
	{
		$cacheDirPaths = static::getCacheDirPaths();
		if (is_dir($cacheDirPaths['fullPath'])) {
			foreach (scandir($cacheDirPaths['fullPath']) as $file) {
				if (substr($file, -4) === '.php') {
					$file = new \File($cacheDirPaths['path'] . '/' . $file);
					$file->delete();
				}
			}
		}
	}
}
