<?php
	function mod_arr_to_str($module, $info_num)
	{
		/*
		 * recieves module array of type Module as para
		 */
		$js_arr_str = "";
		$count = 0;
		if($info_num == 2)
		{
			foreach($module as $mod)
			{
				if($count == 0)
				{
					$js_arr_str .= $mod->get_code() . "- " . $mod->get_name();
				}
				else 
				{
					$js_arr_str .= '^' . $mod->get_code() . "- " . $mod->get_name();
				}
				$count++;
			}
		}
		else
		{
			foreach($module as $mod)
			{
				if($count == 0)
				{
					$js_arr_str .= $mod->get_code();
				}
				else 
				{
					$js_arr_str .= '^' . $mod->get_code();
				}
				$count++;
			}		
		}
		return $js_arr_str;
	}
?>