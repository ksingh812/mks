<?php
 /**
 * Export module
 * http://www.aa-team.com
 * ======================
 *
 * @package		BKM_
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */
! defined( 'ABSPATH' ) and exit;

if(class_exists('BKM_import') != true) {
	class BKM_import {
		
		const WXR_VERSION = '1.0';
		
		/**
		 * parent storage
		 *
		 * @var array
		 */
		public $parent = array();
		
		private $action = '';
		private $wp_import = null;
		
		/**
		 * The constructor
		 */
		public function __construct( $parent=array() )
		{
			// load parent
			$this->parent = $parent;
			
			$this->action = isset($_REQUEST['action']) ? $_REQUEST['action'] : ''; 
			
			// include importer file parsers
	        if ( ! defined( 'WP_LOAD_IMPORTERS' ) )
	            define('WP_LOAD_IMPORTERS', true);
			
	        require_once dirname( __FILE__ ) . '/wordpress-importer/wordpress-importer.php';
			
			$this->wp_import = new BKM_wp_import();
		}

		private function get_backup_files()
		{
			$files = array();
			if (!file_exists( $this->parent->path('UPLOAD_BASE_DIR') . '/aa-backup-manager' )) {
			    @mkdir( $this->parent->path('UPLOAD_BASE_DIR') . '/aa-backup-manager', 0755, true);
			}
			
			$local_files = glob( $this->parent->path('UPLOAD_BASE_DIR') . '/aa-backup-manager/*.zip' );
			if( count($local_files) > 0 ){
				foreach ($local_files as $file) {
					$files[] = array(
						'file_name' => basename($file),
						'file_path' => $file,
						'create_on' => BKM_time_elapsed_string(filemtime($file)),
						'create_timestamp' => filemtime($file)
				   );
				}
			}
			
			return $files;
		}
		
		public function start_import( $file='' )
		{
			// Unzip package to working directory
			$result = unzip_file( $file, $this->parent->path('UPLOAD_BASE_DIR') );
			
			ob_start();
			$this->wp_import->import( $this->parent->path('UPLOAD_BASE_DIR') . '/backup.xml' );
			$__output = ob_get_contents();
			ob_clean();
			
			@unlink( $this->parent->path('UPLOAD_BASE_DIR') . '/backup.xml' );
			
			return $__output;
		}
		
		public function print_interface()
		{
			global $wp;
			$html = array();
			$backup_files = $this->get_backup_files();
			
			$type = isset($_REQUEST['BKM_iw-upload-type']) ? $_REQUEST['BKM_iw-upload-type'] : 'none';
			
			if( $type == 'from-disk' ){
				$file_path = isset($_REQUEST['BKM_iw-select-from-disk']) ? $_REQUEST['BKM_iw-select-from-disk'] : '';
				
				$html[] = '
				<div id="BKM_iw-section-content">
					<h3>Import Status</h3>';
				
				$html[] = 	$this->start_import( $file_path ); 
				$html[] = '</div>';
			}
			
			else if( $type == 'upload-form' ) {
				$file_upload = new File_Upload_Upgrader( 'backupzip', 'package' );
				
				$html[] = '
				<div id="BKM_iw-section-content">
					<h3>Import Status</h3>';
				
				$html[] = 	$this->start_import( $file_upload->package ); 
				$html[] = '</div>';
			}
			else{
				$html[] = '
				<div id="BKM_iw-section-headline">
					<h3>Import Content</h3>
			        <p>This module will help you to get the core pages, categories,
			        and meta setup correctly and let you see how the pages/posts work.<br />
					<div><div style="background-color:red; float:left; color:#fff; padding:2px; margin-right:5px;">WARNING:</div> <b>IF YOU ALREADY HAVE POSTS, PAGES, AND CATEGORIES SETUP IN YOUR WORDPRESS DO NOT INSTALL THIS.
			        IT WILL MOST CERTAINLY DESTROY YOUR PAST WORK. THIS TOOL SHOULD BE USED ONLY ON A FRESH INSTALL</b></div></p> 
				</div>';
					
					
				$html[] = '<div id="BKM_iw-section-content">
					<h2>First, specify how you want to import your data</h2>
			        <div class="BKM_iw-import-choose">
					<form method="post" enctype="multipart/form-data" action="' . ( admin_url("admin.php?page=BKM&BKM_action=import") ) . '" class="wp-upload-form">
						<input type="hidden" name="BKM_iw-upload-type" value="upload-form" />
						<h3>Choose what to import</h3>
						<p>
							<label>' . __('Backup zip file') .'</label>
							<input type="file" id="backupzip" name="backupzip" />
						</p>';
			 
				$html[] = '
					    	<p class="submit"><input type="submit" class="button" id="submit" class="button-secondary" value="&nbsp Import Content &nbsp"></p>
						</form>
						
						<form class="BKM_iw-existent-file" method="post" enctype="multipart/form-data" action="' . ( admin_url("admin.php?page=BKM&BKM_action=import") ) . '">
							<input type="hidden" name="BKM_iw-upload-type" value="from-disk" />
							<h3>Choose file from local</h3>
							<p style="padding-right: 10px;">Upload files to <code>' . ( $this->parent->path("UPLOAD_BASE_DIR") ) . '/aa-backup-manager/</code> and they will appear in this list </p>';
				
				if( count($backup_files) == 0 ){
					$html[] = '<div class="BKM_iw-status-error" style="display:inline-block;">You need to upload backup files first</div>';
				}else{		
					$html[] = '<select name="BKM_iw-select-from-disk">';
					$html[] = 	'<option value="none" selected="true">Please select from list</option>';
					$__files = array();
					if( count($backup_files) > 0 ){
						foreach ($backup_files as $file) {
							$__files[$file['create_timestamp']] =  '<option value="' . ( $this->parent->path("UPLOAD_BASE_DIR") ) . '/aa-backup-manager/' . ( $file['file_name'] ) . '">(' . ( $file['create_on'] ) . ') - ' . ( $file['file_name'] ) . '</option>';
						}
					}
					 
					ksort( $__files );
					foreach ($__files as $file) {
						$html[] = $file;
					}
					
					
					$html[] = '';
					$html[] = '</select>';
				}
				$html[] = '<br />
							<input type="submit" value="&nbsp; Use file &nbsp;" id="submit" class="button" disabled="" style="margin-top: 10px;">
						</form>
					</div>';
				$html[] = '</div>';	
				
			}
			
			return implode( "\n", $html );
		}
	}
}