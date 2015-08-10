<?php

class wp_reaction_walker_nav_menu extends Walker_Nav_Menu {
  
	// add classes to ul sub-menus
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		// depth dependent classes
		$indent = ( $depth > 0  ? str_repeat( "\t", $depth ) : '' ); // code indent
		$display_depth = ( $depth + 1); // because it counts the first submenu as 0
		$classes = array(
			'sub-menu',
			( $display_depth % 2  ? 'menu-odd' : 'menu-even' ),
			( $display_depth >=2 ? 'sub-sub-menu' : '' ),
			'menu-depth-' . $display_depth
			);
		$class_names = implode( ' ', $classes );
	  
		// build html
		$output .= "";
	}
	  
	// add main/sub classes to li's and links
	 function start_el(  &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $post;
		global $wp_query;
		$indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent
	  
		// depth dependent classes
		$depth_classes = array(
			( $depth == 0 ? 'main-menu-item' : 'sub-menu-item' ),
			( $depth >=2 ? 'sub-sub-menu-item' : '' ),
			( $depth % 2 ? 'menu-item-odd' : 'menu-item-even' ),
			'menu-item-depth-' . $depth
		);
		$depth_class_names = esc_attr( implode( ' ', $depth_classes ) );
	  
		// passed classes
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );
		// link attributes
		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		if( 'post_type' == $item->type ) {
			if( 'page' == $item->object ) {
				// set the react.state (should be conditional depending on post_type (page/post))
				$href = site_url() . '/#/pages/' . $item->object_id;
			} else {
				// set the react.state (should be conditional depending on post_type (page/post))
				$href = '#/' . $item->object . '/' . $item->object_id;	
			}
		} else {
			$href =  $item->url;
		}
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_url( $href ) .'"' : '';
		$attributes .= ' data-id="' . (int) url_to_postid( $item->url ) . '"';
		// setup the active class
		if( isset( $item->object_id ) ) {
			if( $item->object_id == $post->ID ) {
				$extra_class = 'menu-link-active';
			} else {
				$extra_class = '';
			}
		}
		
		$classes[] = 'menu-item-' . $item->ID;
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="menu-link ' . ( $depth > 0 ? 'sub-menu-link' : 'main-menu-link' ) . ' ' . $post->ID . ' ' . esc_attr( $class_names ) . '"' : '';
	  
		// font awesome icon class
		$icon_class = ( ( isset( $item->icon_class ) && '' != $item->icon_class ) ? $item->icon_class : 'star-o' );
		
		$item_output = sprintf( '%1$s<a%2$s %3$s><i class="fa fa-fw %4$s"></i><span>%5$s%6$s%7$s</i><span></a>%8$s',
			$args->before,
			$attributes,
			$class_names,
			$icon_class,
			$args->link_before,
			apply_filters( 'the_title', $item->title, $item->ID ),
			$args->link_after,
			$args->after
		);

		// build html
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}



function special_nav_class ($classes, $item) {
    if (in_array('current-menu-item', $classes) ){
        $classes[] = 'menu-link-active ';
    }
    return $classes;
}
add_filter('nav_menu_css_class' , 'special_nav_class' , 10 , 2);