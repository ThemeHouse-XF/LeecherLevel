<?php

class ThemeHouse_Leecherlevel_Listener_LoadClass extends ThemeHouse_Listener_LoadClass
{
	protected function _getExtendedClasses()
	{
		return array(
			'ThemeHouse_Leecherlevel' => array(
				'datawriter' => array(
					'XenForo_DataWriter_User',
				),
				'model' => array(
					'XenForo_Model_User',
				)
			),
		);
	}

	public static function loadClassDataWriter($class, array &$extend)
	{
		$loadClassDataWriter = new ThemeHouse_Leecherlevel_Listener_LoadClass($class, $extend, 'datawriter');
		$extend = $loadClassDataWriter->run();
	}

	public static function loadClassModel($class, array &$extend)
	{
		$loadClassModel = new ThemeHouse_Leecherlevel_Listener_LoadClass($class, $extend, 'model');
		$extend = $loadClassModel->run();
	}
}