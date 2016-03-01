<?php
$noThumbnail = '<img src="https://assets.sdes.ucf.edu/images/blank.png" alt="thumb" class="img-responsive">';

$post = array('post_type' => 'news');
$loop = new WP_Query($post);
?>

<h2 class="page-header">News and Announcements</h2>

<?php while ($loop->have_posts()) : $loop->the_post();?>
	
	<?php if (get_custom_field( 'news_start_date' ) <= date("m-d-Y") && get_custom_field( 'news_end_date' ) >= date("m-d-Y")){ ?> 	

	<div class="news">		
		<?= has_post_thumbnail()? get_the_post_thumbnail($post->ID, '', array('class' => 'img-responsive')) : $noThumbnail ?>
		<div class="news-content">
			<div class="news-title">
				<?= get_custom_field('news_link') ? '<a href="'. get_custom_field('news_link') .'" >' .  get_the_title() . '</a>' : the_title() ?>
			</div>
			<?= get_custom_field('news_strapline') ? '<div class="news-strapline">'. get_custom_field( 'news_strapline' ) . '</div>' : false; ?>
			<div class="datestamp">
				Posted on <?= the_time('F j, Y'); ?> at <?= the_time('g:i a'); ?>
			</div>
			<div class="news-summary">
				<p>
					<?= the_excerpt(); ?>
				</p>
			</div>
		</div>
	</div>

	<?php } ?> 

<?php endwhile; ?>

<?php wp_reset_query(); ?>