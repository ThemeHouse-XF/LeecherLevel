<?php

class ThemeHouse_LeecherLevel_Install_Controller extends ThemeHouse_Install
{
	protected function _getTableChanges()
	{
		return array(
			'xf_user' => array(
				'leecher_level_unknown'		=> 'int(3) UNSIGNED DEFAULT \'1\'',
				'leecher_level'       		=> 'DECIMAL(50,10) DEFAULT \'0\'',
				'leecher_level_last_check'	=> 'int(10) UNSIGNED DEFAULT \'0\'',
			)
		);
	}
}