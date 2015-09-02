<?php
if(is_front_page()){
	get_header();
}else{
	get_header('content');
}
?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<?php if(is_front_page()){ ?>
	<?php
		$settings = (array) get_option( 'sdes_theme_settings' ); 

		$departmentInfo = "<!-- Configure a department to show hours, phone, fax, email, and location. -->";
		if( null != $settings['directory_cms_acronym'] && !ctype_space($settings['directory_cms_acronym']) ) {
			$departmentInfo = get_department_info(esc_attr( $settings['directory_cms_acronym'] ));
		}
	?>    

	<?php the_content(); ?>
	
	<div class="row">
		<br>
		<div class="col-sm-8">
			<?php get_template_part('news-list'); ?>
		</div>
		<div class="col-sm-4">
			<?= $departmentInfo ?>

			<div class="panel panel-warning">
				<div class="panel-heading">Other Resources</div>
				<div class="list-group">		
					<a class="list-group-item external" href="https://giving.ucffoundation.org/sslpage.aspx?pid=561">Donate to SDES!</a>
					<a class="list-group-item external" href="http://fye.sdes.ucf.edu/parents/">Parent &amp; Family Resources</a>
					<a class="list-group-item external" href="http://today.ucf.edu">UCF Today</a>
					<a class="list-group-item external" href="http://map.ucf.edu/">UCF Map</a>
					<a class="list-group-item external" href="http://www.emergency.ucf.edu/knightshare/">KNIGHT S.H.A.R.E.</a>
					<a class="list-group-item external" href="http://cares.sdes.ucf.edu/">UCF CARES</a>
					<a class="list-group-item external" href="http://kars.sdes.ucf.edu/">Knights Academic Resource Services</a>	       
				</div>
			</div>

			<?= render_calendar('41') ?>
		</div>	
	</div>

	<?php	}elseif(is_page('Staff')) { ?>
		<?php the_content(); ?>

		<?= get_template_part('staff-list') ?>

	<?php	}elseif(is_page('Departments')) { ?>
		<?php the_content(); ?>

		<?= get_template_part('department-list') ?>

	<?php } else { ?>
		<?php the_content(); ?>
	<?php } ?>		

<?php endwhile; else: ?>
<?php endif; ?>
<?php get_footer(); ?>