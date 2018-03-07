<?php

/**
 * Plugin Name: SPK - Transfer Contents
 * Description: Transfer contents from one placeholder to another
 * Version: 2.0
 * Author: Jake Almeda
 * Author URI: http://smarterwebpackages.com/
 * Network: true
 * License: GPL2
 */

class SWPTransferContents {

    /* --------------------------------------------------------------------------------------------
     * | Register Custom Post Type
     * ----------------------------------------------------------------------------------------- */
    public function spk_jqtransfer() {
        register_post_type( 'jQTransfer',
            array(
                'labels' => array(
                    'name' => __( 'jQTransfer' ),
                    'singular_name' => __( 'jQTransfer' ),
                    'add_new' => __( 'Add New' ),
                    'add_new_item' => __( 'Add New' ),
                    'edit_item' => __( 'Edit' ),
                    'new_item' => __( 'Add New' ),
                    'view_item' => __( 'View' ),
                    'search_items' => __( 'Search' ),
                    'not_found' => __( 'No entries found' ),
                    'not_found_in_trash' => __( 'No entries found in trash' )
                ),

                'public' => true,
                //'supports' => array( 'title', 'editor', 'thumbnail', 'comments' ),
                'supports' => array( 'title' ),
                'capability_type' => 'post',
                'rewrite' => array("slug" => "jqtransfer"), // Permalinks format
                'menu_position' => 5,
                'register_meta_box_cb' => array( $this, 'add_jqtransfer_metaboxes' )
            )
        );
    }

    /* --------------------------------------------------------------------------------------------
     * | Add Metabox
     * ----------------------------------------------------------------------------------------- */
    public function add_jqtransfer_metaboxes() {
        add_meta_box( 'spk_jqtransfers', 'Transfer Details', array( $this, 'spk_jqtransfer_metabox' ), 'jQTransfer', 'normal', 'default' );
    }

    /* --------------------------------------------------------------------------------------------
     * | Metabox Data | Callback function
     * ----------------------------------------------------------------------------------------- */
    public function spk_jqtransfer_metabox() {
        global $post;

        // Validate check boxes - SOURCE
        if( get_post_meta($post->ID, 'spk_source_hide', true) == 'no' ) {
            $spk_source_hide_yradio = '';
            $spk_source_hide_nradio = 'checked="checked"';
        } else {
            $spk_source_hide_yradio = 'checked="checked"';
            $spk_source_hide_nradio = '';
        }

        // Validate check boxes - TARGET
        if( get_post_meta($post->ID, 'spk_target_hide', true) == 'no' ) {
            $spk_target_hide_yradio = '';
            $spk_target_hide_nradio = 'checked="checked"';
        } else {
            $spk_target_hide_yradio = 'checked="checked"';
            $spk_target_hide_nradio = '';
        }

        // Noncename needed to verify where the data originated
        // Echo out the field
        echo '<input type="hidden" name="eventmeta_noncename" id="eventmeta_noncename" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />
        <table class="spk_cpt_table">
            <tr>
                <td style="padding:5px;"><strong>Source</strong></td>
                <td style="padding:5px;"><input type="text" name="spk_source" value="'.get_post_meta($post->ID, 'spk_source', true).'" class="spk_cpt_fields" /></td>
                <td style="padding:5px;">&nbsp;</td>
                <td style="padding:5px;">Hide <strong>source</strong> container after transfer? <input type="radio" name="spk_source_hide" value="checked" '.$spk_source_hide_yradio.' /> Yes | <input type="radio" name="spk_source_hide" value="no" '.$spk_source_hide_nradio.' /> No
            </tr>
            <tr>
                <td style="padding:5px;">&nbsp;</td>
                <td style="padding:5px;">Note: add the symbols hash (#) for ID or dot (.) if class.</td>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="4">&nbsp;</td>
            </tr>
            <tr>
                <td style="padding:5px;"><strong>Target</strong></td>
                <td style="padding:5px;"><input type="text" name="spk_target" value="'.get_post_meta($post->ID, 'spk_target', true).'" class="spk_cpt_fields" /></td>
                <td style="padding:5px;">&nbsp;</td>
                <td style="padding:5px;">Hide <strong>target</strong> container after transfer? <input type="radio" name="spk_target_hide" value="checked" '.$spk_target_hide_yradio.' /> Yes | <input type="radio" name="spk_target_hide" value="no" '.$spk_target_hide_nradio.' /> No
            </tr>
            <tr>
                <td style="padding:5px;">&nbsp;</td>
                <td style="padding:5px;">Note: add the symbols hash (#) for ID or dot (.) if class.</td>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="4">&nbsp;</td>
            </tr>
            <tr>
                <td style="padding:5px;"><strong>Trigger</strong></td>
                <td style="padding:5px;"><input type="number" name="spk_trigger" value="'.get_post_meta($post->ID, 'spk_trigger', true).'" class="spk_cpt_fields" /></td>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td style="padding:5px;">&nbsp;</td>
                <td style="padding:5px;" colspan="3">Indicate the screen\'s width at which the transition will happen. Note: numbers only please.</td>
            </tr>
        </table>';
    }

    /* --------------------------------------------------------------------------------------------
     * | Save the Metabox Data
     * | @param int $post_id The post ID.
     * | @param post $post The post object.
     * | @param bool $update Whether this is an existing post being updated or not.
     * ----------------------------------------------------------------------------------------- */
    public function spk_save_jqtransfer_meta( $post_id, $post, $update ) {

        /*
         * In production code, $slug should be set only once in the plugin,
         * preferably as a class property, rather than in each function that needs it.
         */
        $post_type = get_post_type($post_id);

        // If this isn't a 'jqtransfer' post, don't update it.
        if ( "jqtransfer" != $post_type ) return;

        // - Pick up all meta field values
        $transfer_meta['spk_source'] = $_POST['spk_source'];
        $transfer_meta['spk_source_hide'] = $_POST['spk_source_hide'];
        $transfer_meta['spk_target'] = $_POST['spk_target'];
        $transfer_meta['spk_target_hide'] = $_POST['spk_target_hide'];
        $transfer_meta['spk_trigger'] = $_POST['spk_trigger'];

        foreach ($transfer_meta as $key => $value) {
            if( get_post_meta( $post->ID, $key, FALSE ) ) { // If the custom field already has a value
                update_post_meta($post->ID, $key, $value);
            } else { // If the custom field doesn't have a value
                add_post_meta($post->ID, $key, $value);
            }
            if( !$value ) delete_post_meta($post->ID, $key); // Delete if blank
        }

    }

    /* --------------------------------------------------------------------------------------------
     * | Register JS file
     * ----------------------------------------------------------------------------------------- */
    public function swp_jqtransfer_scripts() {
        
        wp_register_script( 'swp_jqtransfer_js', plugins_url( 'js/asset.js', __FILE__ ), NULL, '1.0', TRUE );
         
        // Localize the script with additional data
        wp_localize_script( 'swp_jqtransfer_js', 'jqtransfer_extra_var', $this->swp_jqtransfer_query() );
         
        // Enqueued script with localized data.
        wp_enqueue_script( 'swp_jqtransfer_js' );

    }

    /* --------------------------------------------------------------------------------------------
     * | Query the database for entries
     * ----------------------------------------------------------------------------------------- */
    public function swp_jqtransfer_query() {

        global $wpdb;

        $posts = $wpdb->get_results( "SELECT
            b.meta_key,
            b.meta_value
            FROM ".$wpdb->posts." a, ".$wpdb->postmeta." b
            WHERE
            a.ID = b.post_id and
            a.post_status = 'publish' and
            b.meta_key in ( 'spk_trigger', 'spk_target', 'spk_source', 'spk_source_hide', 'spk_target_hide' )
            ORDER BY a.post_date ASC" );

        // this variable will be used to distinctly identify each set
        $entry_counter = 1;

        // set variable as array
        $return = array();

        foreach( $posts as $post ) {
            
            // source
            if( $post->meta_key == "spk_source" ) {

                $spk_source_name = $post->meta_key;

                $spk_source = $post->meta_value;
                $counter++;

            }

            // source - hide
            if( $post->meta_key == "spk_source_hide" ) {

                $spk_source_hide_name = $post->meta_key;

                $spk_source_hide = $post->meta_value;
                $counter++;

            }

            // target
            if( $post->meta_key == "spk_target" ) {

                $spk_target_name = $post->meta_key;

                $spk_target = $post->meta_value;
                $counter++;

            }

            // target - hide
            if( $post->meta_key == "spk_target_hide" ) {

                $spk_target_hide_name = $post->meta_key;

                $spk_target_hide = $post->meta_value;
                $counter++;

            }

            // trigger
            if( $post->meta_key == "spk_trigger" ) {

                $spk_trigger_name = $post->meta_key;

                $spk_trigger = $post->meta_value;
                $counter++;

            }

            if( $counter == 5 ) {

                /*if( ! is_array( $return ) ) {
                    $return[ $entry_counter ] = array(
                            $spk_source_name    => $spk_source,
                            $spk_target_name    => $spk_target,
                            $spk_trigger_name   => $spk_trigger,
                        );
                    //$spk_source.", ".$spk_target.", ".$spk_trigger."|";
                } else {*/
                    //array_push($return, 'the_source' => $spk_source, 'the_target' => $spk_target, 'the_trigger'   => $spk_trigger, );
                    $return[ $entry_counter ][ $spk_source_name ]       = $spk_source;
                    $return[ $entry_counter ][ $spk_source_hide_name ]  = $spk_source_hide;
                    $return[ $entry_counter ][ $spk_target_name ]       = $spk_target;
                    $return[ $entry_counter ][ $spk_target_hide_name ]  = $spk_target_hide;
                    $return[ $entry_counter ][ $spk_trigger_name ]      = $spk_trigger;
                //}

                // reset
                $counter = 0;

            }

            // add only when $counter is divisible by 3
            if( fmod( $counter, 5) == 0 ) {
                $entry_counter++;
            }

        }
        
        // return
        return $return;

        // Restore original Post Data
        wp_reset_postdata();

    }

    // display
    function __construct() {

        // register CPT
        add_action( 'init', array( $this, 'spk_jqtransfer' ) );
        // add metaboxes
        add_action( 'add_meta_boxes', array( $this, 'add_jqtransfer_metaboxes' ) );
        // save - include contents from the metaboxes
        add_action( 'save_post', array( $this, 'spk_save_jqtransfer_meta' ), 10, 3 );
        // enqueue scripts
        add_action( "wp_enqueue_scripts", array( $this, "swp_jqtransfer_scripts" ) );

    }

}

$swp_load = new SWPTransferContents();