<?php
/**
 * Toggle Switch Custom Control
 * 
 * Creates a Customizer control that looks like an on/off switch.
 * 
 * @package Magic Hat
 * @subpackage Customizer
 * @since 1.0.0
 */

if ( ! class_exists( 'Magic_Hat_Customize_Toggle_Control' ) ) :
/**
 * Toggle Switch Control Class.
 * 
 * @author Anthony Hortin <http://maddisondesigns.com>
 * @license GPL-2.0+
 * @version 1.0.5
 * @link https://github.com/maddisondesigns
 */
class Magic_Hat_Customize_Toggle_Control extends WP_Customize_Control {
    /**
     * The control's type.
     * 
     * @var string
     */
    public $type = 'toggle_switch';
    
    /**
     * Renders the control content in the Customizer panel.
     */
    public function render_content() {
        ?>
        <div class="toggle-switch-control">
            <div class="toggle-switch">
                <input type="checkbox" id="<?php echo esc_attr( $this->id ); ?>" name="<?php echo esc_attr( $this->id ); ?>" class="toggle-switch-checkbox" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); checked( $this->value() ); ?>>
                <label class="toggle-switch-label" for="<?php echo esc_attr( $this->id ); ?>">
                    <span class="toggle-switch-inner"></span>
                    <span class="toggle-switch-switch"></span>
                </label>
            </div>
            <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
            <?php if( ! empty( $this->description ) ) { ?>
                <span class="customize-control-description"><?php echo esc_html( $this->description ); ?></span>
            <?php } ?>
        </div>
        <?php
    }
}
endif;