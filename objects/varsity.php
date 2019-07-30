<?php
	class Varsity
	{
		private $id;
		private $name;
		private $abbrev;
		
		function Varsity($_id, $_name, $_abbrev){
			$this->id = $_id;
			$this->name = $_name;
			$this->abbrev = $_abbrev;
		}
		
		function set_name($_name){
			$this->name = $_name;
		}
		
		function set_abbrev($_abbrev){
			$this->abbrev = $_abbrev;
		}
		
		function get_id(){
			return $this->id;
		}
		
		function get_name(){
			return $this->name;
		}
		
		function get_abbrev(){
			return $this->abbrev;
		}
	}
?>