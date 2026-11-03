<?php
/**
 * หน้าแรก (Landing Page / Content Hub)
 *
 * WordPress จะใช้ไฟล์นี้กับหน้าแรกเสมอ ไม่ว่าจะตั้งค่า "การอ่าน" เป็นบทความล่าสุดหรือหน้าคงที่
 * โครงสร้าง: Hero → Features → Tools → Testimonials → Blog → (CTA ใน footer)
 *
 * @package FAmiCal_Custom_Theme
 */

get_header();

$features = array(
	array(
		'icon'  => 'calc',
		'title' => __( 'คำนวณง่าย แม่นยำ', 'famical' ),
		'text'  => __( 'กรอกข้อมูลไม่กี่ช่อง ระบบคำนวณภาษี เงินออม และ IRR ให้ทันที ตามหลักการวางแผนการเงินมืออาชีพ', 'famical' ),
	),
	array(
		'icon'  => 'devices',
		'title' => __( 'ใช้งานได้ทุกที่', 'famical' ),
		'text'  => __( 'เปิดผ่านเบราว์เซอร์ได้ทั้งมือถือ แท็บเล็ต และคอมพิวเตอร์ ไม่ต้องติดตั้งแอปเพิ่ม', 'famical' ),
	),
	array(
		'icon'  => 'shield',
		'title' => __( 'ปลอดภัยสูง', 'famical' ),
		'text'  => __( 'เชื่อมต่อแบบเข้ารหัส HTTPS ข้อมูลการเงินของคุณถูกดูแลอย่างเป็นส่วนตัว', 'famical' ),
	),
);

$tools = array(
	array( 'user', 'Life Blueprint', __( 'พิมพ์เขียวการเงินส่วนบุคคลแบบมืออาชีพ', 'famical' ) ),
	array( 'baby', 'Kids Blueprint', __( 'วางแผนเงินเพื่อลูก ค่าคลอด ค่าเล่าเรียน', 'famical' ) ),
	array( 'calc', 'Tax Shield', __( 'วางแผนลดหย่อนภาษี เงินได้ 40(1)-(8)', 'famical' ) ),
	array( 'reset', 'Financial Reset', __( 'รายรับรายจ่าย 3 สี + Debt Simulator', 'famical' ) ),
	array( 'shield', 'Value Ensure', __( 'คำนวณ IRR และเปรียบเทียบแบบประกัน', 'famical' ) ),
	array( 'building', 'Business Pilot', __( 'แยกบัญชีธุรกิจ บันทึกรายรับ-รายจ่าย ภาษี', 'famical' ) ),
);

$testimonials = famical_get_testimonials();
?>

<!-- ============ HERO ============ -->
<section class="hero">
	<div class="container hero-inner">
		<div class="hero-content">
			<?php if ( famical_mod( 'hero_badge' ) ) : ?>
				<span class="badge"><span class="badge-dot" aria-hidden="true"></span><?php echo esc_html( famical_mod( 'hero_badge' ) ); ?></span>
			<?php endif; ?>

			<h1 class="hero-title"><?php echo esc_html( famical_mod( 'hero_title' ) ); ?></h1>
			<p class="hero-subtitle"><?php echo esc_html( famical_mod( 'hero_subtitle' ) ); ?></p>

			<div class="hero-actions">
				<a class="btn btn-primary btn-lg" href="<?php echo esc_url( famical_app_url( 'hero' ) ); ?>">
					<?php echo esc_html( famical_mod( 'hero_cta_text' ) ); ?>
					<?php echo famical_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</a>
				<a class="btn btn-ghost btn-lg" href="#articles"><?php echo esc_html( famical_mod( 'hero_cta2_text' ) ); ?></a>
			</div>

			<ul class="hero-points">
				<li><?php esc_html_e( 'ไม่ต้องติดตั้งแอป', 'famical' ); ?></li>
				<li><?php esc_html_e( 'ออกแบบโดยนักวางแผนการเงิน', 'famical' ); ?></li>
				<li><?php esc_html_e( 'รองรับภาษาไทย / EN', 'famical' ); ?></li>
			</ul>
		</div>

		<!-- ภาพจำลองหน้าจอ FAmiCal (สร้างด้วย HTML/CSS ล้วน ไม่ต้องโหลดรูป = เร็ว) -->
		<div class="hero-visual" aria-hidden="true">
			<div class="mock-window">
				<div class="mock-bar"><span></span><span></span><span></span></div>
				<div class="mock-title">FAmiCal</div>
				<?php foreach ( array_slice( $tools, 0, 4 ) as $tool ) : ?>
					<div class="mock-card">
						<span class="mock-icon"><?php echo famical_icon( $tool[0] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span>
							<strong><?php echo esc_html( $tool[1] ); ?></strong>
							<small><?php echo esc_html( $tool[2] ); ?></small>
						</span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>

<!-- ============ จิ๋วแจ๋วออกแบบประกัน (จุดขายหลัก) ============ -->
<section class="section buddy-design" id="design">
	<div class="container buddy-design-inner">
		<div>
			<span class="eyebrow"><?php esc_html_e( 'ใหม่ · ฟรี · ไม่ต้องสมัคร', 'famical' ); ?></span>
			<h2><?php esc_html_e( '🐱 ออกแบบประกันกับจิ๋วแจ๋ว ใน 2 นาที', 'famical' ); ?></h2>
			<p><?php esc_html_e( 'บอกอายุ เป้าหมาย และงบต่อปี จิ๋วแจ๋วคำนวณจากอัตราเบี้ยจริงของบริษัท แล้วเสนอ 3 แผนที่คุ้มครองได้มากที่สุดในงบของคุณ', 'famical' ); ?></p>
			<ol class="buddy-steps">
				<li><?php esc_html_e( 'ตอบ 5 ข้อสั้นๆ (อายุ · เป้าหมาย · งบ · จ่ายกี่ปี · คุ้มครองถึงอายุ)', 'famical' ); ?></li>
				<li><?php esc_html_e( 'ดู 3 แผนพร้อมชื่อแบบประกัน ทุน และเบี้ยต่อปี', 'famical' ); ?></li>
				<li><?php esc_html_e( 'รู้ทันทีว่าต้องตรวจสุขภาพไหม และลดหย่อนภาษีได้เท่าไหร่', 'famical' ); ?></li>
			</ol>
			<a class="btn btn-primary btn-lg" href="<?php echo esc_url( famical_design_url( 'home-section' ) ); ?>">
				<?php esc_html_e( 'เริ่มออกแบบประกัน', 'famical' ); ?>
				<?php echo famical_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</a>
			<p class="buddy-design-note"><?php esc_html_e( 'ข้อมูลเพื่อประกอบการตัดสินใจ ผลประโยชน์เป็นไปตามเงื่อนไขกรมธรรม์ · อยากให้ FA ตัวจริงช่วยดูต่อ กดได้ในหน้าผลลัพธ์', 'famical' ); ?></p>
		</div>
	</div>
</section>

<!-- ============ FEATURES ============ -->
<section class="section features" id="features">
	<div class="container">
		<header class="section-head">
			<span class="eyebrow"><?php esc_html_e( 'ทำไมต้อง FAmiCal', 'famical' ); ?></span>
			<h2><?php esc_html_e( 'เครื่องมือการเงินที่ใช้ง่าย สำหรับทุกคนในครอบครัว', 'famical' ); ?></h2>
		</header>

		<div class="grid grid-3">
			<?php foreach ( $features as $feature ) : ?>
				<article class="feature-card">
					<span class="feature-icon"><?php echo famical_icon( $feature['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<h3><?php echo esc_html( $feature['title'] ); ?></h3>
					<p><?php echo esc_html( $feature['text'] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>

		<!-- เครื่องมือใน FAmiCal -->
		<div class="tools">
			<h3 class="tools-title"><?php esc_html_e( 'เครื่องมือใน FAmiCal', 'famical' ); ?></h3>
			<div class="grid grid-3">
				<?php foreach ( $tools as $tool ) : ?>
					<a class="tool-card" href="<?php echo esc_url( famical_app_url( 'tool-' . sanitize_title( $tool[1] ) ) ); ?>">
						<span class="tool-icon"><?php echo famical_icon( $tool[0] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span class="tool-text">
							<strong><?php echo esc_html( $tool[1] ); ?></strong>
							<small><?php echo esc_html( $tool[2] ); ?></small>
						</span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>

<!-- ============ TESTIMONIALS ============ -->
<?php if ( ! empty( $testimonials ) ) : ?>
<section class="section testimonials" id="testimonials">
	<div class="container">
		<header class="section-head">
			<span class="eyebrow"><?php esc_html_e( 'เสียงจากผู้ใช้งาน', 'famical' ); ?></span>
			<h2><?php esc_html_e( 'ผู้ใช้งานจริงพูดถึง FAmiCal', 'famical' ); ?></h2>
		</header>

		<div class="grid grid-3">
			<?php foreach ( $testimonials as $item ) : ?>
				<figure class="testimonial-card">
					<span class="quote-icon"><?php echo famical_icon( 'quote' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<blockquote><p><?php echo esc_html( $item['text'] ); ?></p></blockquote>
					<figcaption>
						<span class="avatar" aria-hidden="true"><?php echo esc_html( mb_substr( $item['name'], 0, 1 ) ); ?></span>
						<span>
							<strong><?php echo esc_html( $item['name'] ); ?></strong>
							<?php if ( $item['role'] ) : ?>
								<small><?php echo esc_html( $item['role'] ); ?></small>
							<?php endif; ?>
						</span>
					</figcaption>
				</figure>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- ============ BLOG / SEO CONTENT ============ -->
<?php
$latest_posts = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 6,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true, // ไม่ต้องนับหน้า = query เร็วขึ้น
	)
);
?>
<?php if ( $latest_posts->have_posts() ) : ?>
<section class="section blog" id="articles">
	<div class="container">
		<header class="section-head section-head-row">
			<div>
				<span class="eyebrow"><?php esc_html_e( 'คลังความรู้การเงิน', 'famical' ); ?></span>
				<h2><?php esc_html_e( 'บทความและความรู้ทางการเงินล่าสุด', 'famical' ); ?></h2>
			</div>
			<a class="link-arrow" href="<?php echo esc_url( famical_blog_url() ); ?>">
				<?php esc_html_e( 'ดูบทความทั้งหมด', 'famical' ); ?>
				<?php echo famical_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</a>
		</header>

		<div class="grid grid-3">
			<?php
			while ( $latest_posts->have_posts() ) :
				$latest_posts->the_post();
				get_template_part( 'template-parts/content', 'card' );
			endwhile;
			wp_reset_postdata();
			?>
		</div>

		<?php
		// แสดงหมวดหมู่หลัก เพื่อกระจายลิงก์ภายใน (internal links) ช่วย SEO
		$cats = get_categories(
			array(
				'orderby'    => 'count',
				'order'      => 'DESC',
				'number'     => 8,
				'hide_empty' => true,
			)
		);
		if ( $cats ) :
			?>
			<nav class="category-pills" aria-label="<?php esc_attr_e( 'หมวดหมู่บทความ', 'famical' ); ?>">
				<?php foreach ( $cats as $cat ) : ?>
					<a href="<?php echo esc_url( get_category_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

<!-- ============ AD (ด้านล่างหน้าแรก) ============ -->
<?php if ( is_active_sidebar( 'ad-home-bottom' ) ) : ?>
<div class="container ad-home-wrap">
	<?php famical_ad_slot( 'ad-home-bottom' ); ?>
</div>
<?php endif; ?>

<?php
// ถ้าตั้ง "หน้าแรก" เป็นหน้าคงที่ และหน้านั้นมีเนื้อหา ให้แสดงต่อท้าย (ใช้ใส่ข้อความ SEO เพิ่มเติมได้)
if ( 'page' === get_option( 'show_on_front' ) && have_posts() ) :
	while ( have_posts() ) :
		the_post();
		if ( '' !== trim( get_the_content() ) ) :
			?>
			<section class="section front-content">
				<div class="container entry-content narrow">
					<?php the_content(); ?>
				</div>
			</section>
			<?php
		endif;
	endwhile;
endif;

get_footer();
