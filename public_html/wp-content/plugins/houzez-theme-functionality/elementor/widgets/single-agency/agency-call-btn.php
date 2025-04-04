<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Houzez_Agency_Call_Button extends Widget_Base {
    use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;
	use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Button_Traits;

	public function get_name() {
		return 'houzez-agency-call-btn';
	}

	public function get_title() {
		return __( 'Agency Call Button', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon houzez-single-agency eicon-button';
	}

	public function get_categories() {
		if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-agency')  {
            return ['houzez-single-agency-builder']; 
        }

        return [ 'houzez-single-agency' ];
	}

	public function get_keywords() {
		return [ 'houzez', 'agency call', 'button' ];
	}

	protected function register_controls() {
		parent::register_controls();

		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Call Button', 'houzez-theme-functionality' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

        $this->add_control(
            'text',
            [
                'label' => esc_html__( 'label', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'Call',
                'placeholder' => esc_html__( 'Add label', 'houzez-theme-functionality' ),
            ]
        );

        $this->add_control(
            'show_number',
            [
                'label' => esc_html__( 'Show Number as Text', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
                'default' => '',
            ]
        );

        $this->add_control(
            'btn_icon',
            [
                'label' => esc_html__( 'Button Icon', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'default' => esc_html__( 'Default', 'houzez-theme-functionality' ),
                    'custom' => esc_html__( 'Custom Icon', 'houzez-theme-functionality' ),
                    'no-icon' => esc_html__( 'No Icon', 'houzez-theme-functionality' ),
                ],
                'default' => 'default',
            ]
        );

        $this->add_control(
            'selected_icon',
            [
                'label' => esc_html__( 'Icon', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'skin' => 'inline',
                'label_block' => false,
                'condition' => [
                    'btn_icon' => 'custom'
                ]
            ]
        );

        $start = is_rtl() ? 'right' : 'left';
        $end = is_rtl() ? 'left' : 'right';

        $this->add_control(
            'icon_align',
            [
                'label' => esc_html__( 'Icon Position', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::CHOOSE,
                'default' => is_rtl() ? 'row-reverse' : 'row',
                'options' => [
                    'row' => [
                        'title' => esc_html__( 'Start', 'houzez-theme-functionality' ),
                        'icon' => "eicon-h-align-{$start}",
                    ],
                    'row-reverse' => [
                        'title' => esc_html__( 'End', 'houzez-theme-functionality' ),
                        'icon' => "eicon-h-align-{$end}",
                    ],
                ],
                'selectors_dictionary' => [
                    'left' => is_rtl() ? 'row-reverse' : 'row',
                    'right' => is_rtl() ? 'row' : 'row-reverse',
                ],
                'selectors' => [
                    '{{WRAPPER}} .houzez-ele-button .houzez-ele-button-content-wrapper' => 'flex-direction: {{VALUE}};',
                ],
                'condition' => [
                	'text!' => '',
                    'selected_icon[value]!' => '',
                    'btn_icon!' => 'no-icon', 
                ]
            ]
        );

        $this->add_control(
            'icon_indent',
            [
                'label' => esc_html__( 'Icon Spacing', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em', 'rem', 'custom' ],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                    'em' => [
                        'max' => 5,
                    ],
                    'rem' => [
                        'max' => 5,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .houzez-ele-button .houzez-ele-button-content-wrapper' => 'gap: {{SIZE}}{{UNIT}};',
                ],
                'condition' =>[
                   'text!' => '',
                   'selected_icon[value]!' => '', 
                   'btn_icon!' => 'no-icon', 
                ]
            ]
        );

		
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Call Button', 'houzez-theme-functionality' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->register_houzez_button_style_controls();

		$this->end_controls_section();
		
	}

	protected function render() {
        global $post;
        $settings = $this->get_settings_for_display();

        $this->single_agency_preview_query();

        $migrated = isset( $settings['__fa4_migrated']['selected_icon'] );
        $is_new = empty( $settings['icon'] ) && Icons_Manager::is_migration_allowed();

        $agency_number = get_post_meta( get_the_ID(), 'fave_agency_phone', true );
        $agency_number_call = str_replace(array('(',')',' ','-'),'', $agency_number);
        if( empty($agency_number) ) {
            $agency_number = get_post_meta( get_the_ID(), 'fave_agency_mobile', true );
            $agency_number_call = str_replace(array('(',')',' ','-'),'', $agency_number);
        }

        $this->add_render_attribute( 'wrapper', 'class', 'houzez-ele-button-wrapper' );

        $this->add_render_attribute(
            [
                'button' => [
                    'class' => [
                        'houzez-ele-button',
                        'btn',
                        'btn-call',
                    ],
                ],
            ]
        );

        $this->add_render_attribute( [
            'content-wrapper' => [
                'class' => 'houzez-ele-button-content-wrapper',
            ],
            'icon' => [
                'class' => 'elementor-button-icon',
            ],
            'text' => [
                'class' => 'elementor-button-text',
            ],
        ] );

        if ( ! empty( $settings['link']['url'] ) ) {
            $this->add_link_attributes( 'button', $settings['link'] );
            $this->add_render_attribute( 'button', 'class', 'elementor-button-link' );
        } else {
            $this->add_render_attribute( 'button', 'role', 'button' );
        }

        if ( ! empty( $settings['hover_animation'] ) ) {
            $this->add_render_attribute( 'button', 'class', 'elementor-animation-' . $settings['hover_animation'] );
        }

        if( ! empty( $agency_number ) ) {
        ?>
        <div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
            
            <a <?php $this->print_render_attribute_string( 'button' ); ?> href="tel:<?php echo esc_attr($agency_number_call); ?>">

                <span <?php $this->print_render_attribute_string( 'content-wrapper' ); ?>>
                    <?php 
                    if( $settings['btn_icon'] == 'default' ) { ?>
                        <span <?php $this->print_render_attribute_string( 'icon' ); ?>>
                            <i class="houzez-icon icon-phone-actions-ring"></i>
                        </span>
                    <?php
                    } else if( $settings['btn_icon'] == 'custom' ) {
                        if ( ! empty( $settings['icon'] ) || ! empty( $settings['selected_icon']['value'] ) ) : ?>
                            <span <?php $this->print_render_attribute_string( 'icon' ); ?>>
                                <?php if ( $is_new || $migrated ) :
                                    Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] );
                                else : ?>
                                    <i class="<?php echo esc_attr( $settings['icon'] ); ?>" aria-hidden="true"></i>
                                <?php endif; ?>
                            </span>
                            <?php endif; 
                        }?>
                    <?php if ( ! empty( $settings['text'] ) && $settings['show_number'] != 'yes' ) { ?>
                    <span class="elementor-button-text hide-on-click"><?php $this->print_unescaped_setting( 'text' ); ?></span>
                    <span class="elementor-button-text show-on-click"><?php echo esc_attr($agency_number); ?></span>
                    <?php } else { ?>
                        <span class="elementor-button-text"><?php echo esc_attr($agency_number); ?></span>
                    <?php } ?>
                </span>
            </a>


        </div>
	   <?php
        } // ! empty( $agency_number )
        $this->reset_preview_query();
	}

}
Plugin::instance()->widgets_manager->register( new Houzez_Agency_Call_Button );