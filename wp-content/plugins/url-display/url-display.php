<?php
/**
 * Plugin Name: Child Theme URL Shortcode
 * Plugin URI: http://
 * Description: To return short code for Child Theme URL
 * Version: 1.0
 * Author: John Cashman
 * Author URI: http://www.cashman.biz
 * License: GPL2
 *  Copyright 2014  John Cashman  (email : john@cashman.biz)
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


function url_display( $attrs = array (), $content = '' )
{
    $theme_uri = is_child_theme()
        ? get_stylesheet_directory_uri()
        : get_template_directory_uri();

    return trailingslashit( $theme_uri );
}

add_shortcode('child_theme_url', 'url_display' );


?>