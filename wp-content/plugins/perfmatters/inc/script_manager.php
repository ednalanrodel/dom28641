<?php
//Security Check
if(!current_user_can('manage_options') || is_admin() || !isset($_GET['perfmatters']) || !perfmatters_network_access()) {
	return;
}

//Set Variables
global $perfmatters_extras;
global $wp;
global $wp_scripts;
global $wp_styles;
global $perfmatters_options;
global $currentID;
$currentID = get_queried_object_id();

//Process Settings Form
if(isset($_POST['perfmatters_script_manager_settings'])) {

	//Validate Settings Nonce
	if(!isset($_POST['perfmatters_script_manager_settings_nonce']) || !wp_verify_nonce($_POST['perfmatters_script_manager_settings_nonce'], 'perfmatter_script_manager_save_settings')) {
		print 'Sorry, your nonce did not verify.';
	    exit;
	} else {

		//Update Settings
		update_option('perfmatters_script_manager_settings', $_POST['perfmatters_script_manager_settings']);
	}
}

//Process Reset Form
if(isset($_POST['perfmatters_script_manager_settings_reset'])) {
	delete_option('perfmatters_script_manager');
	delete_option('perfmatters_script_manager_settings');
}

//Load Script Manager Settings
global $perfmatters_script_manager_settings;
$perfmatters_script_manager_settings = get_option('perfmatters_script_manager_settings');

//Build Array of Existing Disables
global $perfmatters_disables;
$perfmatters_disables = array();
if(!empty($perfmatters_options['disable_google_maps']) && $perfmatters_options['disable_google_maps'] == "1") {
	$perfmatters_disables[] = 'maps.google.com';
	$perfmatters_disables[] = 'maps.googleapis.com';
	$perfmatters_disables[] = 'maps.gstatic.com';
}

//Setup Filters Array
global $perfmatters_filters;
$perfmatters_filters = array(
	"js" => array (
		"title" => "JS",
		"scripts" => $wp_scripts
	),
	"css" => array(
		"title" => "CSS",
		"scripts" => $wp_styles
	)
);

//Load Script Manager Options
global $perfmatters_script_manager_options;
$perfmatters_script_manager_options = get_option('perfmatters_script_manager');

//Load Styles
require_once('script_manager_css.php');

//Script Manager Wrapper
echo "<div id='perfmatters-script-manager-wrapper' " . (isset($_GET['perfmatters']) ? "style='display: block;'" : "") . ">";

	echo "<div id='perfmatters-script-manager'>";

		$master_array = perfmatters_script_manager_load_master_array();

		//Header
		echo "<div id='perfmatters-script-manager-header'>";

			//Logo
			echo "<img src='" . plugins_url('img/logo.svg', dirname(__FILE__)) . "' title='Perfmatters' id='perfmatters-logo' />";
		
			//Main Navigation Form
			echo "<form method='POST'>";
				echo "<div id='perfmatters-script-manager-tabs'>";
					echo "<button name='tab' value='' class='"; if(empty($_POST['tab'])){echo "active";} echo "' title='" . __('Script Manager', 'perfmatters') . "'>" . __('Script Manager', 'perfmatters') . "</button>";
					echo "<button name='tab' value='global' class='"; if(!empty($_POST['tab']) && $_POST['tab'] == "global"){echo "active";} echo "' title='" . __('Global View', 'perfmatters') . "'>" . __('Global View', 'perfmatters') . "</button>";
					echo "<button name='tab' value='settings' class='"; if(!empty($_POST['tab']) && $_POST['tab'] == "settings"){echo "active";} echo "' title='" . __('Settings', 'perfmatters') . "'>" . __('Settings', 'perfmatters') . "</button>";
				echo "</div>";
			echo "</form>";

		echo "</div>";

		//Disclaimer
		if(empty($perfmatters_script_manager_settings['hide_disclaimer']) || $perfmatters_script_manager_settings['hide_disclaimer'] != "1") {
			echo "<div id='perfmatters-script-manager-disclaimer'>";
				echo "<p>";
					_e("Below you can disable/enable CSS and JS files on a per page/post basis, as well as by custom post types. We recommend testing this locally or on a staging site first, as you could break the appearance of your live site. If you aren't sure about a certain script, you can try clicking on it, as a lot of authors will mention their plugin or theme in the header of the source code.", 'perfmatters');
				echo "</p>";
				echo "<p>";
					_e("If for some reason you run into trouble, you can always enable everything again to reset the settings. Make sure to check out the <a href='https://perfmatters.io/docs/' target='_blank' title='Perfmatters Knowledge Base'>Perfmatters knowledge base</a> for more information.", 'perfmatters');
				echo "</p>";
			echo "</div>";
		}

		echo "<div id='perfmatters-script-manager-container'>";

			//Default/Main Tab
			if(empty($_POST['tab'])) {

				echo "<div class='perfmatters-script-manager-title-bar'>";
					echo "<h1>" . __('Script Manager', 'perfmatters') . "</h1>";
					echo "<p>" . __('Manage scripts loading on the current page.', 'perfmatters') . "</p>";
				echo "</div>";

				//Form
				echo "<form method='POST'>";

					foreach($master_array as $category => $groups) {
						if(!empty($groups)) {
							echo "<h3>" . $category . "</h3>";
							if($category != "misc") {
								echo "<div style='background: #ffffff; padding: 10px;'>";
								foreach($groups as $group => $details) {
									if(!empty($details['assets'])) {
										echo "<div class='perfmatters-script-manager-group'>";
											echo "<h4>" . $details['name'];

												//Status
												echo "<div class='perfmatters-script-manager-group-status' style='float: right;'>";
												    perfmatters_script_manager_print_status($category, $group);
												echo "</div>";

											echo "</h4>";

											perfmatters_script_manager_print_section($category, $group, $details['assets']);
										echo "</div>";
									}
								}
								echo "</div>";
							}
							else {
								if(!empty($groups)) {
									perfmatters_script_manager_print_section($category, $category, $groups);
								}
							}
						}
					}

					//Save Button
					echo "<input type='submit' name='perfmatters_script_manager' value='" . __('Save', 'perfmatters') . "' />";

				echo "</form>";

			}
			//Global View Tab
			elseif(!empty($_POST['tab']) && $_POST['tab'] == "global") {

				echo "<div class='perfmatters-script-manager-title-bar'>";
					echo "<h1>" . __('Global View', 'perfmatters') . "</h1>";
					echo "<p>" . __('This is a visual representation of the Script Manager configuration across your entire site.', 'perfmatters') . "</p>";
				echo "</div>";
				
				if(!empty($perfmatters_script_manager_options)) {
					foreach($perfmatters_script_manager_options as $category => $types) {
						echo "<h3>" . $category . "</h3>";
						if(!empty($types)) {
							echo "<table>";
								echo "<thead>";
									echo "<tr>";
										echo "<th>" . __('Type', 'perfmatters') . "</th>";
										echo "<th>" . __('Script', 'perfmatters') . "</th>";
										echo "<th>" . __('Setting', 'perfmatters') . "</th>";
									echo "</tr>";
								echo "</thead>";
								echo "<tbody>";
									foreach($types as $type => $scripts) {
										if(!empty($scripts)) {
											foreach($scripts as $script => $details) {
												if(!empty($details)) {
													foreach($details as $detail => $values) {
														echo "<tr>";
															echo "<td><span style='font-weight: bold;'>" . $type . "</span></td>";
															echo "<td><span style='font-weight: bold;'>" . $script . "</span></td>";
															echo "<td>";
																echo "<span style='font-weight: bold;'>" . $detail . "</span>";
																if($detail == "current" || $detail == "post_types") {
																	if(!empty($values)) {
																		echo " (";
																		$valueString = "";
																		foreach($values as $key => $value) {
																			if($detail == "current") {
																				$valueString.= "<a href='" . get_page_link($value) . "' target='_blank'>" . $value . "</a>, ";
																			}
																			elseif($detail == "post_types") {
																				$valueString.= $value . ", ";
																			}
																		}
																		echo rtrim($valueString, ", ");
																		echo ")";
																	}
																}
															echo "</td>";
														echo "</tr>";
													}
												}
											}
										}
									}
								echo "</tbody>";
							echo "</table>";
						}
					}
				}
				else {
					echo "<div class='perfmatters-script-manager-section'>";
						echo "<p style='margin: 20px; text-align: center;'>" . __("You don't have any scripts disabled yet.") . "</p>";
					echo "</div>";
				}
			}
			//Settings Tab
			elseif(!empty($_POST['tab']) && $_POST['tab'] == "settings") {

				echo "<div class='perfmatters-script-manager-title-bar'>";
					echo "<h1>" . __('Settings', 'perfmatters') . "</h1>";
					echo "<p>" . __('View and manage all of your Script Manager settings.', 'perfmatters') . "</p>";
				echo "</div>";

				echo "<div class='perfmatters-script-manager-section'>";
					
					//Form
					echo "<form method='POST' id='script-manager-settings'>";
					
						echo "<input type='hidden' name='tab' value='settings' />";

						echo "<table>";
							echo "<tbody>";
								echo "<tr>";
									echo "<th>" . perfmatters_title(__('Hide Disclaimer', 'perfmatters'), 'hide_disclaimer') . "</th>";
									echo "<td>";
										echo "<input type='hidden' name='perfmatters_script_manager_settings[hide_disclaimer]' value='0' />";
										$args = array(
								            'id' => 'hide_disclaimer',
								            'option' => 'perfmatters_script_manager_settings',
								            'tooltip' => __('Hide the disclaimer message box across all Script Manager views.', 'perfmatters')
								        );
										perfmatters_print_input($args);
									echo "</td>";
								echo "</tr>";
								echo "<tr>";
									echo "<th>" . perfmatters_title(__('Display Archives', 'perfmatters'), 'separate_archives') . "</th>";
									echo "<td>";
									$args = array(
							            'id' => 'separate_archives',
							            'option' => 'perfmatters_script_manager_settings',
							            'tooltip' => __('Add WordPress archives to your Script Manager selection options. Archive posts will no longer be grouped with their post type.', 'perfmatters')
							        );
									perfmatters_print_input($args);
									echo "</td>";
								echo "</tr>";
							echo "</tbody>";
						echo "</table>";

						//Nonce
						wp_nonce_field('perfmatter_script_manager_save_settings', 'perfmatters_script_manager_settings_nonce');

						//Save Button
						echo "<input type='submit' name='perfmatters_script_manager_settings_submit' value='" . __('Save', 'perfmatters') . "' />";

					echo "</form>";	

					//Reset Form
					echo "<form method='POST' onSubmit=\"return confirm('" . __('Are you sure? This will remove and reset all of your existing Script Manager settings and cannot be undone!') . "');\">";
						echo "<input type='hidden' name='tab' value='settings' />";
						echo "<input type='submit' name='perfmatters_script_manager_settings_reset' class='pmsm-reset' value='" . __('Reset Script Manager', 'perfmatters') . "' />";
					echo "</form>";



				echo "<div>";
				
			}
		echo "</div>";
	echo "</div>";
echo "</div>";