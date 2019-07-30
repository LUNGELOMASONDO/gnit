<?php
	class Module
	{
		private $id;
		private $code;
		private $name;
		private $varsity;
		
		function Module($_id, $_code, $_name, $_varsity)
		{
			$this->id = $_id;
			$this->code = $_code;
			$this->name = $_name;
			$this->varsity = $_varsity;
		}		
		/*
		 * Mutator methods
		 */
		function set_code($_code)
		{
			$this->code = $_code;
		}
		
		function set_name($_name)
		{
			$this->name = $name;
		}
		
		function set_varsity($_varsity)
		{
			$this->varsity = $_varsity;
		}
		/*
		 * Accessor methods
		 */
		function get_id()
		{
			return $this->id;
		}
		 
		function get_name()
		{
			return $this->name;
		}
		
		function get_code()
		{
			return $this->code;
		}
		
		function get_varsity()
		{
			return $this->varsity;
		}
	}
?>