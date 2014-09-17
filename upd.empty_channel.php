<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Empty_Channel_upd {

	var $version = '1.0';
	
	function __construct()
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
	}
	




	// --------------------------------------------------------------------

	/**
	 * Module Installer
	 *
	 * @access	public
	 * @return	bool
	 */	
	function install()
	{
		$this->EE->load->dbforge();

		$data = array(
			'module_name' => 'Empty_Channel' ,
			'module_version' => $this->version,
			'has_cp_backend' => 'y',
			'has_publish_fields' => 'n'
		);

		$this->EE->db->insert('modules', $data);


		

		return TRUE;
/*

		$sql[] = "CREATE TABLE IF NOT EXISTS exp_download_files (
				file_id int(10) unsigned NOT NULL auto_increment,
				dir_id int(4) unsigned NOT NULL,
				file_name VARCHAR(250) NOT NULL,
				file_title VARCHAR(250) NULL DEFAULT NULL,
				member_access varchar(255) NULL DEFAULT 'all',
				PRIMARY KEY `file_id` (`file_id`)
				)";

		$sql[] = "CREATE TABLE IF NOT EXISTS exp_download_posts (
				file_id int(10) unsigned NOT NULL,
				entry_id int(10) unsigned NOT NULL,
				PRIMARY KEY `entry_id_cat_id` (`entry_id`, `file_id`)
				)";
*/
	}
	
	
	// --------------------------------------------------------------------

	/**
	 * Module Uninstaller
	 *
	 * @access	public
	 * @return	bool
	 */
	function uninstall()
	{		
		
		$this->EE->db->where('module_name', 'Empty_Channel');
		$this->EE->db->delete('modules');

		
		return TRUE;
	}



	// --------------------------------------------------------------------

	/**
	 * Module Updater
	 *
	 * @access	public
	 * @return	bool
	 */	
	
	function update($current='')
	{
		return TRUE;
	}
	
}
/* END Class */

/* End of file upd.download.php */
/* Location: ./system/expressionengine/third_party/modules/download/upd.download.php */