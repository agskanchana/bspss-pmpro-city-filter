<?php
/*
Plugin Name: Bspss PMPPro city filterr
Plugin URI: https://github.com/agskanchana/bspss-pmpro-city-filter
Version: 1.0.3
Author: sameera
Author URI: www.linkedin.com/in/sameera-kanchana-3b4660198

*/


require 'includes/plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/agskanchana/bspss-pmpro-city-filter/',
	__FILE__,
	'bspss-pmpro-city-filter'
);



add_shortcode('pmp_filter',function(){
    ob_start();

    $users = get_users( array( 'fields' => array( 'ID' ) ) );
$user_cities = [];
foreach($users as $user){

       if(isset(get_user_meta ( $user->ID)['pharmacy_town_city'][0])){
        array_push($user_cities, get_user_meta ( $user->ID)['pharmacy_town_city'][0]);
       }
}

    ?>
    <style>
form.pmpro_member_directory_search{
	display: none !important;
}

    </style>

    <form role="search" method="post" class="search-form" data-hs-cf-bound="true">
		<label>
			<span class="screen-reader-text">Search for:</span>
			<input type="search" class="search-field" placeholder="Search Members" name="ps" value="" title="Search Members">
			<input type="hidden" name="limit" value="12">
		</label>
		<input type="submit" class="search-submit" value="Search Members" style="display: none;">
	</form>
    <form>
        <select name="town">
            <option value="">Any</option>
            <?php
            if($user_cities):
                $user_cities = array_unique($user_cities);
        foreach($user_cities as $user_city):?>
            <option
            <?php
             if( ! empty( $_REQUEST['town'] ) ) {
                if($_REQUEST['town'] == $user_city){
                    echo "selected";
                }
             }
            ?>
            ><?php echo $user_city;?></option>
            <?php
        endforeach;
    endif;
        ?>
        </select>
        <button type="submit">Filter</button>
    </form>

    <?php


function my_pmpro_directory_widget_filter_sql_parts( $sql_parts, $levels, $s, $pn, $limit, $start, $end, $order_by, $order ) {
	global $wpdb;

	// Filter results based on province if a province is selected.
	if ( ! empty( $_REQUEST['town'] ) ) {
		$sql_parts['JOIN'] .= " LEFT JOIN $wpdb->usermeta um_pharmacy_town_city ON um_pharmacy_town_city.meta_key = 'pharmacy_town_city' AND u.ID = um_pharmacy_town_city.user_id ";
		$sql_parts['WHERE'] .= " AND um_pharmacy_town_city.meta_value in ('" . $_REQUEST['town']. "') ";
	}



	return $sql_parts;
}
add_filter( 'pmpro_member_directory_sql_parts', 'my_pmpro_directory_widget_filter_sql_parts', 10, 9 );







     return ob_get_clean();


});
