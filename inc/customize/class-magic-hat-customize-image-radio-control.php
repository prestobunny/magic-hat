<?php
/**
 * Image Radio Button Custom Control
 * 
 * Creates a Customizer control that allows users to select from a group of images.
 *
 * @package Magic Hat
 * @subpackage Customizer
 * @since 1.0.0
 */

if ( ! class_exists( 'Magic_Hat_Customize_Image_Radio_Control' ) ) :
/**
 * Image Radio Button Control Class.
 * 
 * @author Anthony Hortin <http://maddisondesigns.com>
 * @license GPL-2.0+
 * @version 1.0.5
 * @link https://github.com/maddisondesigns
 */
class Magic_Hat_Customize_Image_Radio_Control extends WP_Customize_Control {
    /**
     * The control's type.
     * 
     * @var string
     */
    public $type = 'image_radio_button';
    
    /**
     * Renders the control content in the Customizer panel.
     */
    public function render_content() {
        ?>
        <div class="image_radio_button_control">
            <?php if( ! empty( $this->label ) ) { ?>
                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
            <?php } ?>
            <?php if( ! empty( $this->description ) ) { ?>
                <span class="customize-control-description"><?php echo esc_html( $this->description ); ?></span>
            <?php } ?>

            <?php foreach ( $this->choices as $key => $value ) { ?>
                <label class="radio-button-label">
                    <input type="radio" name="<?php echo esc_attr( $this->id ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php $this->link(); ?> <?php checked( esc_attr( $key ), $this->value() ); ?>/>
                    <img src="<?php echo esc_attr( $value['image'] ); ?>" alt="<?php echo esc_attr( $value['name'] ); ?>" title="<?php echo esc_attr( $value['name'] ); ?>" />
                </label>
            <?php } ?>
        </div>
        <?php
    }
}
endif;
