<?php
	class Content_Admin extends Admin
	{
		private $id;
		
		function Content_Admin($admin_id, $email, $_varsity, $_name, $_position, $_year, $contentadmin_id){
			Admin::Admin($admin_id, $email, $_varsity, $_name, $_position, $_year);
			$this->id = $contentadmin_id;
		}
		
		function get_content_id(){
			return $this->id;
		}	
	}
?>