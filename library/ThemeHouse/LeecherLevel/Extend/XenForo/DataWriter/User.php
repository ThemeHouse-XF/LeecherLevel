<?php
class ThemeHouse_Leecherlevel_Extend_XenForo_DataWriter_User extends XFCP_ThemeHouse_Leecherlevel_Extend_XenForo_DataWriter_User
{
/*'xf_user' => array(
				'leecher_level_unknown'		=> 'int(3) UNSIGNED DEFAULT \'1\'',
				'leecher_level'       		=> 'DECIMAL(50,10) DEFAULT \'0\'',
				'leecher_level_last_check'	=> 'int(10) UNSIGNED DEFAULT \'0\'',
			)*/
	protected function _getFields()
	{
		$fields = parent::_getFields();
		$fields['xf_user']['leecher_level_unknown'] = array('type' => self::TYPE_BOOLEAN, 'default' => 1);
		$fields['xf_user']['leecher_level'] = array('type' => self::TYPE_FLOAT, 'default' => 0.00);
		$fields['xf_user']['leecher_level_last_check'] = array('type' => self::TYPE_UINT, 'default' => 0);

		return $fields;
	}
}

if (false) {
	class XFCP_ThemeHouse_Leecherlevel_Extend_XenForo_DataWriter_User extends XenForo_DataWriter_User {}
}