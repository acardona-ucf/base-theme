<?php
$noThumbnail = '<img src="https://assets.sdes.ucf.edu/images/blank.png" alt="thumb" class="img-responsive">';

$post = array( 'post_type' => 'staff_list');
$loop = new WP_Query( $post );
?>

<script type="text/javascript">
	$(function(){
		var collapsedSize = 60;
		$(".staff-details").each(function() {
			var h = this.scrollHeight;
			var div = $(this);
			if (h > 30) {
				div.css("height", collapsedSize);
				div.after("<a class=\"staff-more\" href=\"\">[Read More]</a>");
				var link = div.next();
				link.click(function(e) {
					e.stopPropagation();
					e.preventDefault();
					if (link.text() != "[Collapse]") {
						link.text("[Collapse]");
						div.animate({ "height": h });
					} else {
						div.animate({ "height": collapsedSize });
						link.text("[Read More]");
					}
				});
			}
		});
	});
</script>

<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
	<?php if (!strlen(get_the_title()) == 0): ?>
		<div class="staff">
			<?= has_post_thumbnail()? get_the_post_thumbnail($post->ID, '', array('class' => 'img-responsive')) : $noThumbnail ?>
			<div class="staff-content">
				<div class="staff-name">
					<?= the_title(); ?>
				</div>
				<div class="staff-title">
					<?= get_custom_field('position_title')? get_custom_field('position_title') : false ?>
				</div>
				<div class="staff-phone"><?= get_custom_field('staff_phone')? get_custom_field('staff_phone') : false ?></div>
				<div class="staff-email">
					<a href="mailto:<?= get_custom_field('staff_email') ?>"><?= get_custom_field('staff_email') ?></a>
				</div>

				<div class="staff-details">
					<?php the_content(); ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
<?php endwhile; ?>

<?php wp_reset_query(); ?>