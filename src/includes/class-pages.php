<?php
/**
 * Callbacks to generate pages
 *
 * @author      feeling4design
 * @category    Admin
 * @package     SUPER_Forms/Classes
 * @class       SUPER_Pages
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if( !class_exists( 'SUPER_Pages' ) ) :

/**
 * SUPER_Pages
 */
class SUPER_Pages {


    /**
     * Handles the output for the settings page in admin
     */
    public static function settings() {
    
        // Get all available setting fields
        $fields = SUPER_Settings::fields();
        
        wp_enqueue_script( 'jquery-ui-datepicker', false, array( 'jquery' ), SUPER_VERSION );

        // Include the file that handles the view
        include_once(SUPER_PLUGIN_DIR.'/includes/admin/views/page-settings.php' );

    }
    
    
    /**
     * Handles the output for the create form page in admin
     */
    public static function create_form() {
    
        // Get all Forms created with Super Forms (post type: super_form)
        $args = array(
            'post_type' => 'super_form', //We want to retrieve all the Forms
            'posts_per_page' => -1 //Make sure all matching forms will be retrieved
        );
        $forms = get_posts( $args );

        // Check if we are editing an existing Form
        if( isset( $_GET['id'] ) ) {
            $form_id = absint( $_GET['id'] );
            $title = get_the_title( $form_id );
            // @since 3.1.0 - get all Backups for this form.
            $args = array(
                'post_parent' => $form_id,
                'post_type' => 'super_form',
                'post_status' => 'backup',
                'posts_per_page' => -1 //Make sure all matching backups will be retrieved
            );
            $backups = get_posts( $args );
        }else{
            $form_id = 0;
            $title = __( 'Form Name', 'super-forms' );
        }
        $settings = SUPER_Common::get_form_settings($form_id);

        // Retrieve all settings with the correct default values
        $form_settings = SUPER_Settings::fields( $settings, 0 );

        // Get all available shortcodes
        $shortcodes = SUPER_Shortcodes::shortcodes();
        
        // Include the file that handles the view
        include_once( SUPER_PLUGIN_DIR . '/includes/admin/views/page-create-form.php' );
       
    }


    /**
     * List of all the demo forms & community forms
     */
    public static function marketplace() {
        wp_enqueue_script( 'thickbox' );
        wp_enqueue_style( 'thickbox' );  
        include_once( SUPER_PLUGIN_DIR . '/includes/admin/views/page-marketplace.php' );
    }


    /**
     * List of all the contact entries
     */
    public static function contact_entries() {

    }


    /**
     * Handles the output for the view contact entry page in admin
     */
    public static function contact_entry() {
        $id = $_GET['id'];
        if ( (FALSE === get_post_status($id)) && (get_post_type($id)!='super_contact_entry') ) {
            // The post does not exist
            echo 'This contact entry does not exist.';
        } else {
            $my_post = array(
                'ID' => $id,
                'post_status' => 'super_read',
            );
            wp_update_post($my_post);
            $date = get_the_date(false,$id);
            $time = get_the_time(false,$id);
            $ip = get_post_meta($id, '_super_contact_entry_ip', true);
            $entry_status = get_post_meta($id, '_super_contact_entry_status', true);
            $global_settings = SUPER_Common::get_global_settings();
            $data = get_post_meta($_GET['id'], '_super_contact_entry_data', true);
            $data[] = array();
            foreach($data as $k => $v){
                if((isset($v['type'])) && (($v['type']=='varchar') || ($v['type']=='var') || ($v['type']=='text') || ($v['type']=='field') || ($v['type']=='barcode') || ($v['type']=='files'))){
                    $data['fields'][] = $v;
                }elseif((isset($v['type'])) && ($v['type']=='form_id')){
                    $data['form_id'][] = $v;
                }
            }
                                    
            // @since 3.4.0  - custom contact entry status
            $statuses = SUPER_Settings::get_entry_statuses($global_settings);
            ?>
            <script>
                jQuery('.toplevel_page_super_forms').removeClass('wp-not-current-submenu').addClass('wp-menu-open wp-has-current-submenu');
                jQuery('.toplevel_page_super_forms').find('li:eq(4)').addClass('current');
            </script>
            <div class="wrap">

                <div id="poststuff">

                    <div id="titlediv" style="margin-bottom:10px;">
                        <div id="titlewrap">
                            <input placeholder="<?php _e( 'Contact Entry Title', 'super-forms' ); ?>" type="text" name="super_contact_entry_post_title" size="30" value="<?php echo get_the_title($id); ?>" id="title" spellcheck="true" autocomplete="off">
                        </div>
                    </div>

                    <div id="post-body" class="metabox-holder columns-2">
                        <div id="postbox-container-1" class="postbox-container">
                            <div id="side-sortables" class="meta-box-sortables ui-sortable">
                                <div id="submitdiv" class="postbox ">
                                    <div class="handlediv" title="">
                                        <br>
                                    </div>
                                    <h3 class="hndle ui-sortable-handle">
                                        <span><?php echo __('Lead Details', 'super-forms' ); ?>:</span>
                                    </h3>
                                    <div class="inside">
                                        <div class="submitbox" id="submitpost">
                                            <div id="minor-publishing">
                                                <div class="misc-pub-section">
                                                    <span><?php echo __('Submitted', 'super-forms' ).':'; ?> <strong><?php echo $date.' @ '.$time; ?></strong></span>
                                                </div>
                                                <div class="misc-pub-section">
                                                    <span><?php echo __('IP-address', 'super-forms' ).':'; ?> <strong><?php if(empty($ip)){ echo __('Unknown', 'super-forms' ); }else{ echo $ip; } ?></strong></span>
                                                </div>
                                                <div class="misc-pub-section">
                                                    <?php echo '<span>' . __('Based on Form', 'super-forms' ) . ': <strong><a href="admin.php?page=super_create_form&id=' . $data['form_id'][0]['value'] . '">' . get_the_title( $data['form_id'][0]['value'] ) . '</a></strong></span>'; ?>
                                                </div>
                                                <?php
                                                if(SUPER_WC_ACTIVE){
                                                    $wc_order_id = get_post_meta($id, '_super_contact_entry_wc_order_id', true);
                                                    if(!empty($wc_order_id)){
                                                        ?>
                                                        <div class="misc-pub-section">
                                                            <span><?php echo __('WooCommerce Order', 'super-forms' ).':'; ?> <strong><?php echo '<a href="'.get_edit_post_link($wc_order_id,'').'">#'.$wc_order_id.'</a>'; ?></strong></span>
                                                        </div>
                                                        <?php
                                                    }
                                                }
                                                $post_author_id = get_post_field( 'post_author', $id );
                                                if( !empty($post_author_id) ) {
                                                    $user_info = get_userdata($post_author_id);
                                                    echo '<div class="misc-pub-section">';
                                                        echo '<span>' . __( 'Submitted by', 'super-forms' ) . ': <a href="' . get_edit_user_link($user_info->ID) . '"><strong>' . $user_info->display_name . '</strong></a></span>';
                                                    echo '</div>';
                                                }
                                                ?>
                                                <div class="misc-pub-section">
                                                    <?php
                                                    echo '<span>' . __('Entry status', 'super-forms' ).':&nbsp;</span>';
                                                    echo '<select name="entry_status">';
                                                    foreach($statuses as $k => $v){
                                                        echo '<option value="'.$k.'" ' . ($entry_status==$k ? 'selected="selected"' : '') . '>'.$v['name'].'</option>';
                                                    }
                                                    echo '</select>';
                                                    ?>
                                                </div>
                                                <div class="clear"></div>
                                            </div>

                                            <div id="major-publishing-actions">
                                                <div id="delete-action">
                                                    <a class="submitdelete super-delete-contact-entry" data-contact-entry="<?php echo absint($id); ?>" href="#"><?php echo __('Move to Trash', 'super-forms' ); ?></a>
                                                </div>
                                                <div id="publishing-action">
                                                    <span class="spinner"></span>
                                                    <input name="print" type="submit" class="super-print-contact-entry button button-large" value="<?php echo __('Print', 'super-forms' ); ?>">
                                                    <input name="save" type="submit" class="super-update-contact-entry button button-primary button-large" data-contact-entry="<?php echo absint($id); ?>" value="<?php echo __('Update', 'super-forms' ); ?>">
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="postbox-container-2" class="postbox-container">
                            <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                                <div id="super-contact-entry-data" class="postbox ">
                                    <div class="handlediv" title="">
                                        <br>
                                    </div>
                                    <h3 class="hndle ui-sortable-handle">
                                        <span><?php echo __('Lead Information', 'super-forms' ); ?>:</span>
                                    </h3>
                                    <?php
                                    $shipping = 0;
                                    $currency = '';
                                    ?>
                                    <div class="inside">
                                        <?php
                                        echo '<table>';
                                            if( ( isset($data['fields']) ) && (count($data['fields'])>0) ) {
                                                foreach( $data['fields'] as $k => $v ) {
                                                    if( $v['type']=='barcode' ) {
                                                        echo '<tr><th align="right">' . $v['label'] . '</th><td>';
                                                        echo '<div class="super-barcode">';
                                                            echo '<div class="super-barcode-target"></div>';
                                                            echo '<input type="hidden" value="' . $v['value'] . '" data-barcodetype="' . $v['barcodetype'] . '" data-modulesize="' . $v['modulesize'] . '" data-quietzone="' . $v['quietzone'] . '" data-rectangular="' . $v['rectangular'] . '" data-barheight="' . $v['barheight'] . '" data-barwidth="' . $v['barwidth'] . '" />';
                                                        echo '</div>';
                                                    }else if( $v['type']=='files' ) {
                                                        if( isset( $v['files'] ) ) {
                                                            foreach( $v['files'] as $fk => $fv ) {
                                                                $url = $fv['url'];
                                                                if( isset( $fv['attachment'] ) ) {
                                                                    $url = wp_get_attachment_url( $fv['attachment'] );
                                                                }
                                                                if( $fk==0 ) {
                                                                    echo '<tr><th align="right">' . $fv['label'] . '</th><td><span class="super-contact-entry-data-value"><a target="_blank" href="' . $url . '">' . $fv['value'] . '</a></span></td></tr>';
                                                                }else{
                                                                    echo '<tr><th align="right">&nbsp;</th><td><span class="super-contact-entry-data-value"><a target="_blank" href="' . $url . '">' . $fv['value'] . '</a></span></td></tr>';
                                                                }
                                                            }
                                                        }else{
                                                            echo '<tr><th align="right">' . $v['label'] . '</th><td><span class="super-contact-entry-data-value">';
                                                            echo '<input type="text" disabled="disabled" value="' . __( 'No files uploaded', 'super-forms' ) . '" />';
                                                            echo '</span></td></tr>';
                                                        }
                                                    }else if( ($v['type']=='varchar') || ($v['type']=='var') || ($v['type']=='field') ) {
                                                        if( !isset($v['value']) ) $v['value'] = '';
                                                        if ( strpos( $v['value'], 'data:image/png;base64,') !== false ) {
                                                            echo '<tr><th align="right">' . $v['label'] . '</th><td><span class="super-contact-entry-data-value"><img src="' . $v['value'] . '" /></span></td></tr>';

                                                            // @since 2.3 - convert it to an actual image (for future reference)
                                                            /*
                                                            $img_data = $v['value'];
                                                            list($type, $img_data) = explode(';', $img_data);
                                                            list(, $img_data) = explode(',', $img_data);
                                                            $img_data = base64_decode($img_data);
                                                            $img_path = SUPER_PLUGIN_DIR . "/uploads/php/files/" . $v['name'] . "-" . $data['form_id'][0]['value'] . ".png"; 
                                                            file_put_contents($img_path, $img_data);
                                                            $img_url = SUPER_PLUGIN_FILE . "uploads/php/files/" . $v['name'] . "-" . $data['form_id'][0]['value'] . ".png";
                                                            echo '<tr><th align="right">' . $v['label'] . '</th><td><span class="super-contact-entry-data-value"><img src="' . $img_url . '" /></span></td></tr>';
                                                            */

                                                        }else{
                                                            echo '<tr>';
                                                            if( empty($v['label']) ) $v['label'] = '&nbsp;';
                                                            echo '<th align="right">' . $v['label'] . '</th>';
                                                            echo '<td>';
                                                            echo '<span class="super-contact-entry-data-value">';

                                                            echo '<input class="super-shortcode-field" type="text" name="' . esc_attr($v['name']) . '" value="' . sanitize_text_field($v['value']) . '" />';
                                                            echo '</span>';
                                                            echo '</td>';
                                                            echo '</tr>';
                                                        }
                                                    }else if( $v['type']=='text' ) {
                                                        echo '<tr>';
                                                        echo '<th align="right">' . $v['label'] . '</th>';
                                                        echo '<td>';
                                                        echo '<span class="super-contact-entry-data-value">';
                                                        echo '<textarea class="super-shortcode-field" name="' . esc_attr($v['name']) . '">' . $v['value'] . '</textarea>';
                                                        echo '</span>';
                                                        echo '</td>';
                                                        echo '</tr>';
                                                    }
                                                }
                                            }
                                            echo '<tr><th align="right">&nbsp;</th><td><span class="super-contact-entry-data-value">&nbsp;</span></td></tr>';
                                            echo '<tr><th align="right">' . __( 'Based on Form', 'super-forms' ) . ':</th><td><span class="super-contact-entry-data-value">';
                                            echo '<input type="hidden" class="super-shortcode-field" name="form_id" value="' . absint($data['form_id'][0]['value']) . '" />';
                                            echo '<a href="admin.php?page=super_create_form&id=' . $data['form_id'][0]['value'] . '">' . get_the_title( $data['form_id'][0]['value'] ) . '</a>';
                                            echo '</span></td></tr>';

                                            echo apply_filters( 'super_after_contact_entry_data_filter', '', array( 'entry_id'=>$_GET['id'], 'data'=>$data ) );

                                        echo '</table>';
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div id="advanced-sortables" class="meta-box-sortables ui-sortable"></div>
                        </div>
                    </div>
                    <!-- /post-body -->
                    <br class="clear">
                </div>
            <?php
        }
    }   
    
}
endif;