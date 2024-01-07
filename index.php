<?php
/*
Plugin Name: Bspss PMPPro city filterr
Plugin URI: https://github.com/agskanchana/bspss-pmpro-city-filter
Version: 1.0.6
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
form.bspss-search-form input{
    border: 1px solid #bbb !important;
    padding: 5px !important;
    /* width: 100% !important; */
}
.bsspss-filter-col{
    display: flex;
    gap: 25px;
}
.bspss-filter-select select,
.bspss-filter-select input{
    padding: 5px;
    border-radius: 5px;
}
.bspss-filter-select input,
form.bspss-search-form input.search-submit{
    padding: 5px;
    background-color: #0C71C3;
    color: #fff;
    padding-left: 10px !important;
    padding-right: 10px !important;
    border-radius: 5px !important;
    /* font-weight: bold; */
    border: none;
    /* border: namespace; */
}
.pmpro_member_directory-item{
    background-color: #ededed;
    padding-top: 35px;
    padding-bottom: 35px;
}
.pmpro_member_directory_avatar img{
    border-radius: 50%;
}
.pmpro_member_directory_link a{
    display: inline-block;
    background-color: #0C71C3;
    color: #fff !important;
    padding: 5px 15px;
    font-size: 16px !important;
}
/*
.bsspss-filter-col form{
    flex: 0 0 50%;
}*/

    </style>


    <?php


    ?>
    <div class="bsspss-filter-col">
    <form  role="search" method="post" class="search-form bspss-search-form">
		<label>
			<span class="screen-reader-text"><?php _e('Search for:','pmpromd'); ?></span>
			<input type="search" class="search-field" placeholder="<?php _e('Search Members','pmpromd'); ?>" name="ps" value="<?php if(!empty($_REQUEST['ps'])) echo stripslashes( esc_attr($_REQUEST['ps']) );?>" title="<?php _e('Search Members','pmpromd'); ?>" />
			<!-- <input type="hidden" name="limit" value="<?php echo esc_attr($limit);?>" /> -->
		</label>
		<input type="submit" class="search-submit" value="<?php _e('Search Members','pmpromd'); ?>">
	</form>
    <form class="bspss-filter-select">
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

        <input type="submit" value="Filter">
    </form>
    </div>
    <?php


function my_pmpro_directory_widget_filter_sql_parts( $sql_parts, $levels, $s, $pn, $limit, $start, $end, $order_by, $order ) {
	global $wpdb;

	// Filter results based on province if a province is selected.
	if ( ! empty( $_REQUEST['town'] )  && empty($_REQUEST['ps'])) {
		$sql_parts['JOIN'] .= " LEFT JOIN $wpdb->usermeta um_pharmacy_town_city ON um_pharmacy_town_city.meta_key = 'pharmacy_town_city' AND u.ID = um_pharmacy_town_city.user_id ";
		$sql_parts['WHERE'] .= " AND um_pharmacy_town_city.meta_value in ('" . $_REQUEST['town']. "') ";
	}



	return $sql_parts;
}
add_filter( 'pmpro_member_directory_sql_parts', 'my_pmpro_directory_widget_filter_sql_parts', 10, 9 );







     return ob_get_clean();


});
