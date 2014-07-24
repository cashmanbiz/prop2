<?php
/*
Name: Set of helpful functions
Description: Helpful functions
*/

if( !function_exists( 'array_htmlspecialchars_recursive' ) ) {
  /**
   * Convert special characters to HTML entities recursively for array or string
   *
   * @see htmlspecialchars()
   * @param mixed $data
   * @param integer $flags
   * @param string $encoding
   * @author peshkov@UD
   * @version 0.1
   */
  function array_htmlspecialchars_recursive( $data, $flags = '', $encoding = 'UTF-8' ) {
    if ( empty($flags) ) {
      $flags = ENT_COMPAT | ENT_HTML401 ;
    }
    if ( !is_array( $data ) ) {
      $data = htmlspecialchars( $data, $flags, $encoding );
    } else {
      foreach( $data as $k => $v ) {
        $data[$k] = array_htmlspecialchars_recursive( $v, $flags, $encoding );
      }
    }
    return $data;
  }

}

?>