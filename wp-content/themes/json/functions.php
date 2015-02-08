<?php

add_action( 'init', 'codex_custom_init' );
function codex_custom_init() {
    $args = array(
      'public' => true,
      'label'  => 'Players',
      'supports' => array('custom-fields')
    );
    register_post_type('player', $args);

    $args = array(
      'public' => true,
      'label'  => 'Camps',
      'supports' => array('custom-fields')
    );
    register_post_type('camp', $args);
    
    $args = array(
      'public' => true,
      'label'  => 'Contacts',
      'supports' => array('custom-fields')
    );
    register_post_type('contact', $args);

    $args = array(
      'public' => true,
      'label'  => 'Payments',
      'supports' => array('custom-fields')
    );
    register_post_type('payment', $args);        
}

add_action( 'init', 'register_camp_tag' );
function register_camp_tag() {
    register_taxonomy(
        'camp',
        'player',
        array(
            'label' => __( 'Camps' ),
            'public' => true,
            'rewrite' => false
        )
    );
}

add_action('init', 'themes_dir_add_rewrites');  
function themes_dir_add_rewrites() {
  global $wp_rewrite;
  $theme_name = next(explode('/themes/', get_stylesheet_directory())); 
  $new_non_wp_rules = array( 
    'api/(.*)'       => 'wp-content/themes/'. $theme_name . '/dashboard.php?command=$1'
  );  
  $wp_rewrite->non_wp_rules += $new_non_wp_rules;  
}  