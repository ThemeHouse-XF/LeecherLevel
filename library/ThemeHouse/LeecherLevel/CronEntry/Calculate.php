<?php

class ThemeHouse_LeecherLevel_CronEntry_Calculate
{

	public static function run()
	{
		/* @var $addOnModel XenForo_Model_User */
		$userModel = XenForo_Model::create('XenForo_Model_User');
		$users = $userModel->getUsersToUpdateLeecherLevel();

		foreach ($users as $user) {
			$leecherLevel = $userModel->calculateLeecherLevel($user);

			$userWriter = XenForo_DataWriter::create('XenForo_DataWriter_User');
			$userWriter->setExistingData($user['user_id']);
			if ($leecherLevel !== false) {
				$userWriter->set('leecher_level_unknown', 0);
				$userWriter->set('leecher_level', $leecherLevel);
			} else {
				$userWriter->set('leecher_level_unknown', 1);
				$userWriter->set('leecher_level', 0);
			}
			$userWriter->set('leecher_level_last_check', XenForo_Application::$time);
			$userWriter->save();
		}
	}
}