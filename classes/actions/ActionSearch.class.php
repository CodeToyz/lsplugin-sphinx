<?php

class PluginSphinx_ActionSearch extends ActionPlugin
{	
	
	private $sTypesEnabled = array('topics' => array('topic_publish' => 1), 'comments' => array('comment_delete' => 0),'users' => array('user_activate' => 1),'blogs' => array());
	private $aSphinxRes = null;
	private $bIsResults = FALSE;
	
    public function Init()
    {
        $this->SetDefaultEvent('index');
		$this->Viewer_AddHtmlTitle($this->Lang_Get('search'));
		
    }

    protected function RegisterEvent() {
		$this->AddEvent('index','EventIndex');
		$this->AddEvent('topics','EventTopics');
		$this->AddEvent('comments','EventComments');
		$this->AddEvent('opensearch','EventOpenSearch');
		$this->AddEvent('users', 'EventUsers');
		$this->AddEvent('blogs', 'EventBlogs');
	}
	
	function EventIndex(){
	}
	
	function EventOpenSearch(){
		Router::SetIsShowStats(false);
		$this->Viewer_Assign('sAdminMail', Config::Get('sys.mail.from_email'));
	}
	/**
	 * ����� �������
	 *
	 * @return unknown
	 */
	function EventTopics(){
		/**
		 * ����
		 */
		$aReq = $this->PrepareRequest();
		$aRes = $this->PrepareResults($aReq, Config::Get('module.topic.per_page'));
		if(FALSE === $aRes) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'));
			return Router::Action('error');
		}
		/**
		 * ���� ����� ��� ����������
		 */
		if($this->bIsResults){
			/**
			 * �������� �����-������� �� ������ ���������������
			 */
			$aTopics = $this->Topic_GetTopicsAdditionalData(array_keys($this->aSphinxRes['matches']));			
			/**
			 * ������������� ������ jevix
			 */
			$this->Text_LoadJevixConfig('search');			
			/**
			 *  ������ �������� 
			 */
			foreach($aTopics AS $oTopic){
				/**
				 * �.�. ����� � ��������� ���������, �� ����� �������� ����� ������
				 */
				$oTopic->setTextShort($this->Text_JevixParser($this->Sphinx_GetSnippet(
					$oTopic->getText(), 
					'topics', 
					$aReq['q'], 
					'<span class="searched-item">', 
					'</span>'
				)));
			}						
			/**
			 *  ���������� ������ � ������ 
			 */
			$this->Viewer_Assign('bIsResults', TRUE);
			$this->Viewer_Assign('aRes', $aRes);
			$this->Viewer_Assign('aTopics', $aTopics);
		}
	}
	/**
	 * ����� ������������
	 *
	 * @return unknown
	 */
	function EventComments(){
		/**
		 * ����
		 */
		$aReq = $this->PrepareRequest();
		$aRes = $this->PrepareResults($aReq, Config::Get('module.comment.per_page'));
		if(FALSE === $aRes) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'));
			return Router::Action('error');
		}
		/**
		 * ���� ����� ��� ����������
		 */
		if($this->bIsResults){
			/**
			 *  �������� �����-������� �� ������ ���������������
			 */		
			$aComments = $this->Comment_GetCommentsAdditionalData(array_keys($this->aSphinxRes['matches']));			
			/**
			 * ������������� ������ jevix
			 */
			$this->Text_LoadJevixConfig('search');
			/** 
			 * ������ �������� 
			 */
			foreach($aComments AS $oComment){
				$oComment->setText($this->Text_JevixParser($this->Sphinx_GetSnippet(
					htmlspecialchars($oComment->getText()), 
					'comments', 
					$aReq['q'], 
					'<span class="searched-item">', 
					'</span>'
				)));
			}			
			/**
			 *  ���������� ������ � ������ 
			 */
			$this->Viewer_Assign('aRes', $aRes);
			$this->Viewer_Assign('aComments', $aComments);
		}
	}
	
    function EventUsers(){
	
		/**
		 * ����
		 */
		$aReq = $this->PrepareRequest();
		$aRes = $this->PrepareResults($aReq, Config::Get('module.topic.per_page'));
		if(FALSE === $aRes) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'));
			return Router::Action('error');
		}
		/**
		 * ���� ����� ��� ����������
		 */
		if($this->bIsResults){
			/**
			 * �������� �����-������� �� ������ ���������������
			 */
			$aUsers = $this->User_GetUsersAdditionalData(array_keys($this->aSphinxRes['matches']));			
			
			$this->Viewer_Assign('bIsResults', TRUE);
			$this->Viewer_Assign('aRes', $aRes);
			$this->Viewer_Assign('aUsers', $aUsers);
		}
	
	}
	
	function EventBlogs(){
	
		/**
		 * ����
		 */
		$aReq = $this->PrepareRequest();
		$aRes = $this->PrepareResults($aReq, Config::Get('module.topic.per_page'));
		if(FALSE === $aRes) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'));
			return Router::Action('error');
		}
		/**
		 * ���� ����� ��� ����������
		 */
		if($this->bIsResults){
			/**
			 * �������� �����-������� �� ������ ���������������
			 */
			$aBlogs = $this->Blog_GetBlogsAdditionalData(array_keys($this->aSphinxRes['matches']));			
			
			$this->Viewer_Assign('bIsResults', TRUE);
			$this->Viewer_Assign('aRes', $aRes);
			$this->Viewer_Assign('aBlogs', $aBlogs);
		}
	
	}
	
	private function PrepareRequest(){
		$aReq['q'] = getRequest('q');
		if (!func_check($aReq['q'],'text', 2, 255)) {
			/**
			 *  ���� ������ ������� �������� �������������� �� ��������� �������� ������
			 * ���� ��� ����� ���������� ����� � ��� �� �������
			 */
			Router::Location(Router::GetPath('search'));
		}
		$aReq['sType'] = strtolower(Router::GetActionEvent());		
		/**
		 * ���������� ������� �������� ������ ����������
		 */
		$aReq['iPage'] = intval(preg_replace('#^page(\d+)$#', '\1', $this->getParam(0)));
		if(!$aReq['iPage']) { $aReq['iPage'] = 1; }		
		/**
		 *  �������� ������ � ������������ 
		 */
		$this->Viewer_Assign('aReq', $aReq);		
		return $aReq;
	}
	/**
	 * ����� � ������������ ����������
	 *
	 * @param unknown_type $aReq
	 * @param unknown_type $iLimit
	 * @return unknown
	 */
	private function PrepareResults($aReq, $iLimit){
		/**
		 *  ���������� ����������� �� �����
		 */
		foreach($this->sTypesEnabled as $sType => $aExtra){
			$aRes['aCounts'][$sType] = intval($this->Sphinx_GetNumResultsByType($aReq['q'], $sType, $aExtra));
		}		
		if($aRes['aCounts'][$aReq['sType']] == 0){ 
			/**
			 *  �������� ������������ ���� �� ������� 
			 */
			unset($this->sTypesEnabled[$aReq['sType']]);
			/**
			 * ��������� ��������� ����
			 */
			foreach(array_keys($this->sTypesEnabled) as $sType){
				if($aRes['aCounts'][$sType])
					Router::Location(Router::GetPath('search').$sType.'/?q='.$aReq['q']);
			}
		} elseif(($aReq['iPage']-1)*$iLimit <= $aRes['aCounts'][$aReq['sType']]) {
			/**
			 * ����
			 */
			$this->aSphinxRes = $this->Sphinx_FindContent(
				$aReq['q'], 
				$aReq['sType'], 
				($aReq['iPage']-1)*$iLimit, 
				$iLimit, 
				$this->sTypesEnabled[$aReq['sType']]
			);
			/**
			 * �������� ����� ������� �� ��������
			 */
			if (FALSE === $this->aSphinxRes) {
				return FALSE;
			}
			
			$this->bIsResults = TRUE;
			/**
			 * ��������� ������������ �����
			 */
			$aPaging = $this->Viewer_MakePaging(
				$aRes['aCounts'][$aReq['sType']], 
				$aReq['iPage'], 
				$iLimit, 
				4, 
				Router::GetPath('search').$aReq['sType'], 
				array(
					'q' => $aReq['q']
				)
			);
			$this->Viewer_Assign('aPaging', $aPaging);
		}
		$this->SetTemplateAction('results');
		$this->Viewer_AddHtmlTitle($aReq['q']);
		$this->Viewer_Assign('bIsResults', $this->bIsResults);
		return $aRes;
	}
	

}