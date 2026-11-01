<?php
/**
 * การ์ดบทความ (ใช้ทั้งหน้าแรกและหน้ารวมบทความ / หมวดหมู่ / ค้นหา)
 *
 * @package FAmiCal_Custom_Theme
 */

$famical_cats = get_the_category();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
	<a class="post-card-thumb" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'famical-card', array( 'loading' => 'lazy', 'alt' => '' ) ); ?>
		<?php else : ?>
			<span class="thumb-placeholder"><?php echo famical_icon( 'calc' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<?php endif; ?>
	</a>
	<div class="post-card-body">
		<div class="post-meta">
			<?php if ( ! empty( $famical_cats ) ) : ?>
				<a class="post-cat" href="<?php echo esc_url( get_category_link( $famical_cats[0] ) ); ?>"><?php echo esc_html( $famical_cats[0]->name ); ?></a>
			<?php endif; ?>
			<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
		</div>
		<h3 class="post-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p class="post-card-excerpt"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
		<a class="link-arrow" href="<?php the_permalink(); ?>">
			<?php esc_html_e( 'อ่านต่อ', 'famical' ); ?>
			<span class="screen-reader-text"><?php the_title(); ?></span>
			<?php echo famical_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</a>
	</div>
</article>
