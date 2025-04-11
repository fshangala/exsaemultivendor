<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function exsaemultivendor_login_logo() {
  $custom_logo_id = get_theme_mod( 'custom_logo' );
  $logo_url = wp_get_attachment_image_src( $custom_logo_id, 'full' );

  if ( $logo_url ) {
      $logo_url = esc_url( $logo_url[0] ); // Extract the URL from the array
  } else {
      // Fallback to a default logo or leave it as the WordPress logo
      $logo_url = ''; // Or set a default image URL
  }

  ?>
  <style type="text/css">
      #login h1 a, .login h1 a {
          background-image: url(<?php echo $logo_url; ?>);
          height: 100px; /* Adjust as needed */
          width: 320px; /* Adjust as needed */
          background-size: contain;
          background-repeat: no-repeat;
          background-position: center center;
      }
  </style>
  <?php
}
add_action( 'login_enqueue_scripts', 'exsaemultivendor_login_logo' );

function exsaemultivendor_login_logo_url() {
    return esc_url( home_url() );
}
add_filter( 'login_headerurl', 'exsaemultivendor_login_logo_url' );

function exsaemultivendor_login_logo_title() {
    return esc_attr( get_bloginfo( 'name' ) );
}
add_filter( 'login_headertext', 'exsaemultivendor_login_logo_title' );
?>