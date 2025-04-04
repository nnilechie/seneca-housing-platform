<?php
global $agent_listing_ids;

$stats = houzez_get_realtor_tax_stats('property_city', 'fave_agents', $agent_listing_ids);

$taxonomies = $stats['taxonomies'];
$tax_chart_data = $stats['tax_chart_data'];
$taxs_list_data = $stats['taxs_list_data'];
$total_count = $stats['total_count'];
$total_top_count = $stats['total_top_count'];
$other_percent = $stats['other_percent'];
$others = $stats['others'];

if( !empty($taxonomies) ) { ?>
	
<div class="agent-profile-chart-wrap">
	<h2><?php echo wp_kses(__( '<span>Property</span> Cities', 'houzez' ), houzez_allowed_html() ); ?></h2>
	<div class="d-flex align-items-center">
		<div class="agent-profile-chart">
			<canvas id="stats-property-cities" data-chart="<?php echo json_encode($tax_chart_data); ?>" width="100" height="100"></canvas>
		</div><!-- agent-profile-chart -->
		<div class="agent-profile-data">
			<ul class="list-unstyled">
				<?php
				$j = $k = 0;
				if(!empty($taxs_list_data) && !empty($total_count)) {
					foreach ($taxs_list_data as $taxnonomy) { $j++;

						if($j <= $total_top_count) {

							$percent = round($tax_chart_data[$k]);
							if(!empty($percent)) {
							echo '<li class="stats-data-'.$j.'">
									<i class="houzez-icon icon-sign-badge-circle mr-1"></i> <strong>'.esc_attr($percent).'%</strong> <span>'.esc_attr($taxnonomy).'</span>
								</li>';
							}
						}
						$k++;
					}

					if(!empty($others)) {
						$num = '4';
						if($j <= 2) {
							$num = '3';
						}
						echo '<li class="stats-data-'.$num.'">
								<i class="houzez-icon icon-sign-badge-circle mr-1"></i> <strong>'.round($other_percent).'%</strong> <span>'.esc_html__('Other', 'houzez').'</span>
							</li>';
					}
					
				}
				?>
			</ul>
		</div><!-- agent-profile-data -->
	</div><!-- d-flex -->
</div><!-- agent-profile-chart-wrap -->
<?php } ?>