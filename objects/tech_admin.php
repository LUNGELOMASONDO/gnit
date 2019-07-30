<?php	
	class Tech_Admin extends Admin
	{
		private $id;
		
		function Tech_Admin($admin_id, $email, $_varsity, $_name, $_position, $_year, $tech_id){
			Admin::Admin($admin_id, $email, $_varsity, $_name, $_position, $_year);
			$this->id = $tech_id;
		}
		
		function get_tech_id(){
			return $this->id;
		}
	}
?>