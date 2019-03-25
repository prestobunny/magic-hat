<?php
/**
 * Simple Notice Custom Control
 * 
 * Creates a visual Customizer control that shows a header and optional description.
 * 
 * @package Magic Hat
 * @subpackage Customizer
 * @since 1.0.0
 */

if ( ! class_exists( 'Magic_Hat_Customize_Heading_Control' ) ) :
/**
 * Heading Control Class.
 * 
 * @author Anthony Hortin <http://maddisondesigns.com>
 * @license GPL-2.0+
 * @version 1.0.5
 * @link https://github.com/maddisondesigns
 */
class Magic_Hat_Customize_Heading_Control extends WP_Customize_Control {
    /**
     * The control's type.
     * 
     * @var string
     */
    public $type = 'heading';

    /**
     * Renders the control content in the Customizer panel.
     */
    public function render_content() {
        $allowed_html = array(
            'a' => array(
                'href' => array(),
                'title' => array(),
                'class' => array(),
                'target' => array(),
            ),
            'br' => array(),
            'em' => array(),
            'strong' => array(),
            'i' => array(
                'class' => array()
            ),
            'span' => array(
                'class' => array(),
            ),
            'code' => array(),
        );
        ?>
        
        <div class="heading_control">
            <?php if( ! empty( $this->label ) ) { ?>
                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
            <?php } ?>
            <?php if( ! empty( $this->description ) ) { ?>
                <span class="customize-control-description"><?php echo wp_kses( $this->description, $allowed_html ); ?></span>
            <?php } ?>
        </div>
        <?php
    }
}
endif;