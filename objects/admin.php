<?php
	abstract class Admin
	{
		private $id;
		private $email;
		private $varsity;
		private $name;
		private $position;
		private $year;
		
		function Admin($admin_id, $admin_email, $_varsity, $_name, $_position, $_year){
			$this->id = $admin_id;
			$this->email = $admin_email;
			$this->varsity = $_varsity;
			$this->name = $_name;
			$this->position = $_position;
			$this->year = $_year;
		}
		/*
		 * Mutators
		 */
		function set_email($admin_email){
			$this->email = $admin_email;
		}
		
		function set_varsity($_varsity){
			$this->varsity = $_varsity;
		}
		
		function set_name($admin_name){
			$this->name = $admin_name;
		}
		
		function set_position($admin_position){
			$this->position = $admin_position;
		}
		
		function set_year($admin_year){
			$this->year = $admin_year;
		}
		/*
		 * Accessors
		 */
		function get_id(){
			return $this->id;
		}
		
		function get_email(){
			return $this->email;
		}
		
		function get_varsity(){
			return $this->varsity;
		}
		
		function get_name(){
			return $this->name;
		}
		
		function get_pos(){
			return $this->position;
		}
		
		function get_year(){
			return $this->year;
		}
	}
?>