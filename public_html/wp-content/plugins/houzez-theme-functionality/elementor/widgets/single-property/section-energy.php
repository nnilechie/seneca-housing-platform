<?php
namespace Elementor;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Property_Section_Energy_Class extends Widget_Base {
    use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;
    use Houzez_Style_Traits;

	public function get_name() {
		return 'houzez-property-section-energy-class';
	}

	public function get_title() {
		return __( 'Section Energy Class', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon eicon-featured-image';
	}

	public function get_categories() {
		if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-listing')  {
            return ['houzez-single-property-builder']; 
        }

        return [ 'houzez-single-property' ];
	}

	public function get_keywords() {
		return ['property', 'energy class', 'houzez' ];
	}

	protected function register_controls() {
		parent::register_controls();


		$this->start_controls_section(
            'engergy_content',
            [
                'label' => __( 'Content', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

		$this->add_control(
            'section_header',
            [
                'label' => esc_html__( 'Section Header', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'section_title',
            [
                'label' => esc_html__( 'Section Title', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'description' => '',
                'condition' => [
                	'section_header' => 'true'
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'titles_content',
            [
                'label' => __( 'Labels', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
			'energy_titles_note',
			[
				'label' => __( 'Add your custom labels, it will overwrite default labels which are manageable in theme options -> translation', 'houzez-theme-functionality' ),
				'type' => 'houzez-info-note',
			]
		);


        $this->add_control(
            'energetic_class_title',
            [
                'label' => esc_html__( 'Energetic class', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'description' => '',
            ]
        );

        $this->add_control(
            'global_energy_index',
            [
                'label' => esc_html__( 'Global Energy Performance Index', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'label_block' => true,
            ]
        );
        $this->add_control(
            'renewable_energy_index',
            [
                'label' => esc_html__( 'Renewable energy performance index', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'energy_performance',
            [
                'label' => esc_html__( 'Energy performance of the building', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'epc_current_rating',
            [
                'label' => esc_html__( 'EPC Current Rating', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'epc_potential_rating',
            [
                'label' => esc_html__( 'EPC Potential Rating', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'energy_class_title',
            [
                'label' => esc_html__( 'Energy class', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'label_block' => true,
            ]
        );

        $this->end_controls_section();
	
		$this->start_controls_section(
            'sec_style',
            [
                'label' => __( 'Section Style', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->houzez_single_property_section_styling_traits();

		$this->end_controls_section();

		$this->start_controls_section(
            'content_style',
            [
                'label' => __( 'Content Style', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_control(
			'heading_section_title',
			[
				'label' => esc_html__( 'Section Title', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
            'sec_title_color',
            [
                'label'     => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .block-title-wrap h2' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typo',
                'label'    => esc_html__( 'Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .block-title-wrap h2',
            ]
        );

        $this->add_control(
			'heading_labels',
			[
				'label' => esc_html__( 'Meta Labels', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
            'meta_label_color',
            [
                'label'     => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .class-energy-list li strong' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'meta_label_typo',
                'label'    => esc_html__( 'Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .class-energy-list li strong',
            ]
        );

        $this->add_control(
			'heading_values',
			[
				'label' => esc_html__( 'Meta Data', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
            'meta_data_color',
            [
                'label'     => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .class-energy-list li span' => 'color: {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'meta_data_typo',
                'label'    => esc_html__( 'Typography', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .class-energy-list li span',
            ]
        );

        $this->add_control(
			'heading_other',
			[
				'label' => esc_html__( 'Other', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
            'li_border',
            [
                'label' => esc_html__( 'Hide Border', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'options' => array(
                    '' => esc_html__('Default', 'houzez-theme-functionality'),
                    'none' => esc_html__('None', 'houzez-theme-functionality'),
                    'solid' => esc_html__('Solid', 'houzez-theme-functionality'),
                    'dashed' => esc_html__('Dashed', 'houzez-theme-functionality'),
                    'dotted' => esc_html__('Dotted', 'houzez-theme-functionality'),
                    'groove' => esc_html__('Groove', 'houzez-theme-functionality'),
                    'double' => esc_html__('Double', 'houzez-theme-functionality'),
                    'ridge' => esc_html__('Ridge', 'houzez-theme-functionality'),
                ),
                'selectors' => [
                    '{{WRAPPER}} .class-energy-list li' => 'border-bottom-style: {{VALUE}};',
                ],
            ]
        );

		$this->add_control(
            'liborder_color',
            [
                'label'     => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .class-energy-list li' => 'border-color: {{VALUE}}',
                ],
                'condition' => [
                	'li_border!' => 'none'
                ]
            ]
        );

        $this->add_responsive_control(
            'line_height',
            [
                'label' => esc_html__( 'Line Height', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'desktop_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .class-energy-list li' => 'line-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

      

		$this->end_controls_section();

	}

	protected function render() {
		
		global $post, $energy_class, $ele_settings;

		$settings = $this->get_settings_for_display();

		$ele_settings = $settings;

        $this->single_property_preview_query(); // Only for preview

		$section_title = isset($settings['section_title']) && !empty($settings['section_title']) ? $settings['section_title'] : houzez_option('sps_energy_class', 'Energy Class');
		
		$energy_class = houzez_get_listing_data_by_id('energy_class', $post->ID);

		if( Plugin::$instance->editor->is_edit_mode() ) { ?>

			<div class="property-energy-class-wrap property-section-wrap" id="property-energy-class-wrap">
				<div class="block-wrap">

					<?php if( $settings['section_header'] ) { ?>
					<div class="block-title-wrap">
						<h2><?php echo $section_title; ?></h2>
					</div><!-- block-title-wrap -->
					<?php } ?>

					<div class="block-content-wrap">
						<?php get_template_part('property-details/partials/energy-class'); ?> 
					</div><!-- block-content-wrap -->
				</div><!-- block-wrap -->
			</div><!-- property-address-wrap -->
		<?php
		} else {

			if(!empty($energy_class)) { ?>
			<div class="property-energy-class-wrap property-section-wrap" id="property-energy-class-wrap">
				<div class="block-wrap">

					<?php if( $settings['section_header'] ) { ?>
					<div class="block-title-wrap">
						<h2><?php echo $section_title; ?></h2>
					</div><!-- block-title-wrap -->
					<?php } ?>

					<div class="block-content-wrap">
						<?php get_template_part('property-details/partials/energy-class'); ?> 
					</div><!-- block-content-wrap -->
				</div><!-- block-wrap -->
			</div><!-- property-address-wrap -->
			<?php
			}

		}
        $this->reset_preview_query(); // Only for preview
	}

}
Plugin::instance()->widgets_manager->register( new Property_Section_Energy_Class );