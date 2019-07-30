<?php
	class Probelm extends Upload
	{
		private $prob_id;
		private $prob_year;
		private $prob_subject;
		
		function Problem($_upld_id, $_content, $_timestamp, $_prob_id, $_year, $_subject)
		{
			Upload::Upload($_upld_id, $_content, $_timestamp);
			$this->prob_id = $_prob_id;
			$this->prob_year = $_year;
			$this->prob_subject = $_subject;
		}
		
		function get_searchkey()
		{
			return "GNIT" . Upload::get_id() . "P-" . $this->module->get_code();
		}
		
		function get_problemid()
		{
			return $this->prob_id;
		}
		
		function get_year()
		{
			return $this->prob_year;
		}
		
		function get_subject()
		{
			return $this->prob_subject;
		}
	}
?>