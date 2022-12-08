<?php
use NewfoldLabs\WP\Module\CustomerBluehost;
use NewfoldLabs\WP\ModuleLoader\Container;
use Bluehost\SiteMeta; // From plugin

use function NewfoldLabs\WP\ModuleLoader\register as registerModule;

/**
 * Register the data module
 */
if ( function_exists( 'add_action' ) ) {

	add_action(
		'plugins_loaded',
		function () {

			registerModule(
				array(
					'name'     => 'newfold-customer-bluehost',
					'label'    => __( 'Customer Bluehost', 'newfold-customer-bluehost' ),
					'callback' => function () {
						$module = new Customer\Bluehost();
						$module->start();
					},
					'isActive' => true,
					'isHidden' => true,
				)
			);

		}
	);
    
	// Add filter callback to add bluehost customer data to data module in cron event data
	add_filter( 
        'newfold_wp_data_module_cron_data_filter',
        function( $data ) {
            // Filter the cron event data object with bluehost specific customer data
            $data['customer'] = Customer\Bluehost::collect();
            return $data;
        }
    );

	// Add filter callback to add site_id to core data module data
	add_filter( 
        'newfold_wp_data_module_core_data_filter',
        function( $data ) {
            $data['site_id'] = SiteMeta::get_id();
            return $data;
        }
    );

}
