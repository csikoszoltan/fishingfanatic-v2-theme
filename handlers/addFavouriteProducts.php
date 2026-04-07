<?php

function addFavouriteProducts()
{
    check_ajax_referer( 'ff_favourites_nonce', 'nonce' );

    $product_id = isset( $_POST['product_id'] ) ? (int) $_POST['product_id'] : 0;
    if ( ! $product_id ) wp_send_json_error( 'Invalid product ID' );

    $user_id = get_current_user_id();
    $favs    = get_user_meta( $user_id, '_favourites', true );
    if ( ! is_array( $favs ) ) $favs = [];

    if ( in_array( $product_id, $favs ) ) {
        $favs = array_values( array_diff( $favs, [ $product_id ] ) );
        $is_fav = false;
    } else {
        $favs[] = $product_id;
        $is_fav = true;
    }

    update_user_meta( $user_id, '_favourites', $favs );

    wp_send_json_success( [ 'is_favourite' => $is_fav ] );
}