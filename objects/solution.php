<?php
	class Solution
	{
		private $sol_id;
		private $description;
		
		function Solution($_upld_id, $_content, $_timestamp, $_sol_id, $_description)
		{
			Upload::Upload($_upld_id, $_content, $_timestamp);
			$this->sol_id = $_sol_id;
			$this->description = $_description;
		}
		
		function get_searchkey()
		{
			return "GNIT" . Upload::get_id() . "S-" . $this->module->get_code();
		}
		
		function get_sol_id()
		{
			return $this->sol_id;
		}
		
		function get_description()
		{
			return $this->description;
		}
	}
?>