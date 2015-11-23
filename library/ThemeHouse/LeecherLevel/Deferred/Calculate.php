<?php

class ThemeHouse_LeecherLevel_Deferred_Calculate extends XenForo_Deferred_Abstract
{
	public function execute(array $deferred, array $data, $targetRunTime, &$status)
	{
		$data = array_merge(array(
			'position' => 0,
			'batch' => 70
		), $data);
		$data['batch'] = max(1, $data['batch']);

		/* @var $userModel XenForo_Model_User */
		$userModel = XenForo_Model::create('XenForo_Model_User');

		/* @var $conversationModel XenForo_Model_Conversation */
		$conversationModel = XenForo_Model::create('XenForo_Model_Conversation');

		$userIds = $userModel->getUserIdsInRange($data['position'], $data['batch']);
		if (sizeof($userIds) == 0)
		{
			return true;
		}

		foreach ($userIds AS $userId)
		{
			$data['position'] = $userId;

			$userWriter = XenForo_DataWriter::create('XenForo_DataWriter_User');
			$userWriter->setExistingData($userId);
			$user = $userWriter->getMergedData();

			$leecherLevel = $userModel->calculateLeecherLevel($user);

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

		$actionPhrase = new XenForo_Phrase('rebuilding');
		$typePhrase = new XenForo_Phrase('users');
		$status = sprintf('%s... %s (%s)', $actionPhrase, $typePhrase, XenForo_Locale::numberFormat($data['position']));

		return $data;
	}

	public function canCancel()
	{
		return true;
	}
}