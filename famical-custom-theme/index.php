<?php
/**
 * เทมเพลตสำรองหลัก (WordPress บังคับว่าธีมต้องมีไฟล์นี้)
 *
 * ใช้แสดง: บทความเดี่ยว, หน้า (Page), หมวดหมู่, แท็ก, ค้นหา, หน้ารวมบทความ และ 404
 * ทำให้บทความเดิมทุกลิงก์ยังเปิดได้ตามปกติ = ไม่เสีย SEO
 *
 * @package FAmiCal_Custom_Theme
 */

get_header();
?>

<div class="container content-area <?php echo is_active_sidebar( 'sidebar-1' ) && ! is_page() ? 'has-sidebar' : ''; ?>">
	<div class="content-main">

		<?php if ( is_singular() ) : ?>

			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-entry' ); ?>>
					<header class="entry-header">
						<?php if ( is_single() ) : ?>
							<div class="post-meta">
								<?php
								$famical_cats = get_the_category();
								if ( ! empty( $famical_cats ) ) :
									?>
									<a class="post-cat" href="<?php echo esc_url( get_category_link( $famical_cats[0] ) ); ?>"><?php echo esc_html( $famical_cats[0]->name ); ?></a>
								<?php endif; ?>
								<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
								<span><?php the_author(); ?></span>
							</div>
						<?php endif; ?>
						<h1 class="entry-title"><?php the_title(); ?></h1>
					</header>

					<?php if ( has_post_thumbnail() ) : ?>
						<div class="entry-thumb"><?php the_post_thumbnail( 'large', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?></div>
					<?php endif; ?>

					<div class="entry-content">
						<?php
						the_content();
						wp_link_pages(
							array(
								'before' => '<nav class="page-links">',
								'after'  => '</nav>',
							)
						);
						?>
					</div>

					<?php if ( is_single() ) : ?>
						<footer class="entry-footer">
							<?php the_tags( '<div class="tag-list">', '', '</div>' ); ?>

							<aside class="inline-cta">
								<div>
									<strong><?php esc_html_e( 'อยากวางแผนเรื่องนี้ด้วยตัวเอง?', 'famical' ); ?></strong>
									<p><?php esc_html_e( 'ลองใช้เครื่องมือคำนวณใน FAmiCal ฟรี เห็นตัวเลขจริงของคุณภายในไม่กี่นาที', 'famical' ); ?></p>
								</div>
								<a class="btn btn-primary" href="<?php echo esc_url( famical_app_url( 'single-post' ) ); ?>"><?php echo esc_html( famical_mod( 'hero_cta_text' ) ); ?></a>
							</aside>

							<?php
							the_post_navigation(
								array(
									'prev_text' => '<small>' . esc_html__( 'บทความก่อนหน้า', 'famical' ) . '</small><span>%title</span>',
									'next_text' => '<small>' . esc_html__( 'บทความถัดไป', 'famical' ) . '</small><span>%title</span>',
								)
							);
							?>
						</footer>
					<?php endif; ?>
				</article>

				<?php
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
			endwhile;
			?>

		<?php else : ?>

			<header class="archive-header">
				<?php if ( is_search() ) : ?>
					<h1 class="archive-title">
						<?php
						/* translators: %s: search query */
						printf( esc_html__( 'ผลการค้นหา: %s', 'famical' ), '<span>' . esc_html( get_search_query() ) . '</span>' );
						?>
					</h1>
					<?php get_search_form(); ?>
				<?php elseif ( is_404() ) : ?>
					<h1 class="archive-title"><?php esc_html_e( 'ไม่พบหน้าที่คุณต้องการ', 'famical' ); ?></h1>
					<p><?php esc_html_e( 'ลองค้นหาบทความ หรือกลับไปที่หน้าแรก', 'famical' ); ?></p>
					<?php get_search_form(); ?>
				<?php elseif ( is_archive() ) : ?>
					<?php the_archive_title( '<h1 class="archive-title">', '</h1>' ); ?>
					<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
				<?php else : ?>
					<h1 class="archive-title"><?php esc_html_e( 'บทความทั้งหมด', 'famical' ); ?></h1>
				<?php endif; ?>
			</header>

			<?php if ( have_posts() ) : ?>
				<div class="grid grid-2">
					<?php
					while ( have_posts() ) :
						the_post();
						get_template_part( 'template-parts/content', 'card' );
					endwhile;
					?>
				</div>
				<?php
				the_posts_pagination(
					array(
						'mid_size'  => 1,
						'prev_text' => esc_html__( 'ก่อนหน้า', 'famical' ),
						'next_text' => esc_html__( 'ถัดไป', 'famical' ),
					)
				);
				?>
			<?php elseif ( ! is_404() ) : ?>
				<p><?php esc_html_e( 'ยังไม่มีบทความในส่วนนี้', 'famical' ); ?></p>
			<?php endif; ?>

		<?php endif; ?>
	</div>

	<?php if ( is_active_sidebar( 'sidebar-1' ) && ! is_page() ) : ?>
		<aside class="sidebar" aria-label="<?php esc_attr_e( 'แถบด้านข้าง', 'famical' ); ?>">
			<?php dynamic_sidebar( 'sidebar-1' ); ?>
		</aside>
	<?php endif; ?>
</div>

<?php
get_footer();
