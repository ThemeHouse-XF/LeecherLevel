<?php
class ThemeHouse_Leecherlevel_Extend_XenForo_Model_User extends XFCP_ThemeHouse_Leecherlevel_Extend_XenForo_Model_User
{
	protected static $_varCache = array();
	public function calculateLeecherLevel(array $user)
	{
		$xenOptions = XenForo_Application::getOptions();
		$formula = $xenOptions->th_leecherLevel_formula;

		$userId = $user['user_id'];

		if ($user['message_count'] < $xenOptions->th_leecherLevel_minPostsToCalculate) {
			return false;
		}

		if (!isset(self::$_varCache[$user['user_id']])) {
			self::$_varCache[$user['user_id']] = array();
		}

		preg_match_all('/\{\$(.*?)\}/', $formula, $vars);

		foreach ($vars[1] as $fullVar) {
			if (isset(self::$_varCache[$user['user_id']][$fullVar])) {
				$this->_updateFormula($formula, $fullVar, self::$_varCache[$user['user_id']][$fullVar], $userId);
				continue;
			}
			$varParts = explode('.', $fullVar);
			$var = $varParts[0];
			$param = false;
			if (isset($varParts[1])) {
				$param = $varParts[1];
			}

			switch ($var) {
				case 'posts':
					if ($param) {
						$numPosts = $this->countPostsByUserInNode($userId, $param);
						$this->_updateFormula($formula, $fullVar, $numPosts, $userId);
					} else {
						$this->_updateFormula($formula, $fullVar, $user['message_count'], $userId);
					}
					break;
				case 'threads':
					if ($param) {
						$numThreads = $this->countThreadsByUserInNode($userId, $param);
						$this->_updateFormula($formula, $fullVar, $numThreads, $userId);
					} else {
						$numThreads = $this->countThreadsByUser($userId);
						$this->_updateFormula($formula, $fullVar, $numThreads, $userId);
					}
					break;
				case 'likesReceived':
					$this->_updateFormula($formula, $fullVar, $user['like_count'], $userId);
					break;
				case 'daysSinceRegistration':
					$numDays = round((XenForo_Application::$time - $user['register_date']) / 86400);
					$this->_updateFormula($formula, $fullVar, $numDays, $userId);

					break;
			}
		}

		try {
			$eq = new ThemeHouse_LeecherLevel_Util_Equation();
			$leecherLevel = $eq->solveIF($formula);
		} catch (Exception $e) {
			return false;
		}

		return $leecherLevel;
	}

	public function getUsersToUpdateLeecherLevel()
	{
		$xenOptions = XenForo_Application::getOptions();
		return $this->fetchAllKeyed("
			SELECT *
			FROM xf_user
			WHERE message_count >= ?
			ORDER BY leecher_level_last_check ASC
			LIMIT ".(int) $xenOptions->th_leecherLevel_cronUsersToCheck
			, 'user_id', $xenOptions->th_leecherLevel_minPostsToCalculate);
	}

	public function countPostsByUserInNode($userId, $nodeId)
	{
		return $this->_getDb()->fetchOne("
			SELECT COUNT(post.post_id)
			FROM xf_post AS post
			LEFT JOIN xf_thread AS thread ON (thread.thread_id=post.thread_id)
			WHERE post.user_id=?
				AND thread.node_id=?
			", array($userId, $nodeId));
	}

	public function countThreadsByUser($userId)
	{
		return $this->_getDb()->fetchOne("
			SELECT COUNT(thread.thread_id)
			FROM xf_thread AS thread
			WHERE thread.user_id=?
			", array($userId));
	}

	public function countThreadsByUserInNode($userId, $nodeId)
	{
		return $this->_getDb()->fetchOne("
			SELECT COUNT(thread.thread_id)
			FROM xf_thread AS thread
			WHERE thread.user_id=?
				AND thread.node_id=?
			", array($userId, $nodeId));
	}

	protected function _updateFormula(&$formula, $fullVar, $value, $userId)
	{
		$formula = str_replace('{$'.$fullVar.'}', $value, $formula);
		self::$_varCache[$userId][$fullVar] = $value;
	}
}

if (false) {
	class XFCP_ThemeHouse_Leecherlevel_Extend_XenForo_Model_User extends XenForo_Model_User {}
}