<?php
	abstract class Upload
	{
		protected $upld_id;
		protected $content; // or location if its a file
		protected $module;
		protected $timestamp;
		
		function Upload($_upld_id, $_content, $_timestamp, $_module)
		{
			$this->upld_id = $_upld_id;
			$this->content = $_content;
			$this->timestamp = $_timestamp;
			$this->module = $_module;
		}
		
		protected abstract function get_searchkey();
		
		function get_upld_id()
		{
			return $this->upld_id;
		}
		
		function get_content()
		{
			return $this->content;
		}
		
		function get_module()
		{
			return $this->module;
		}
		
		function get_timestamp()
		{
			return $this->timestamp;
		}
	}
?>