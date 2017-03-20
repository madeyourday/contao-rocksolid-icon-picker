<?php

namespace MadeYourDay\RockSolidIconPicker;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;

class ContaoManagerPlugin implements BundlePluginInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function getBundles(ParserInterface $parser)
	{
		return [
			BundleConfig::create(RockSolidIconPickerBundle::class)
				->setLoadAfter([ContaoCoreBundle::class])
				->setReplace(['rocksolid-icon-picker']),
		];
	}
}
