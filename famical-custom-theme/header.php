<?php
/**
 * Header ของธีม FAmiCal Custom Theme
 *
 * หมายเหตุ SEO: ห้ามใส่ <title> หรือ meta description เองในไฟล์นี้
 * ให้ wp_head() + Rank Math / Yoast เป็นคนจัดการทั้งหมด
 *
 * @package FAmiCal_Custom_Theme
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#0b1530">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'ข้ามไปยังเนื้อหา', 'famical' ); ?></a>

<header class="site-header" id="site-header">
	<div class="container header-inner">
		<div class="site-branding">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<span class="logo-mark" aria-hidden="true">S</span>
					<span class="logo-text"><?php bloginfo( 'name' ); ?></span>
				</a>
			<?php endif; ?>
		</div>

		<button class="menu-toggle" type="button" aria-controls="site-navigation" aria-expanded="false">
			<?php echo famical_icon( 'menu' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<span class="screen-reader-text"><?php esc_html_e( 'เปิดเมนู', 'famical' ); ?></span>
		</button>

		<nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e( 'เมนูหลัก', 'famical' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'menu',
					'depth'          => 2,
					'fallback_cb'    => 'famical_fallback_menu',
				)
			);
			?>
			<a class="btn btn-primary btn-sm header-cta" href="<?php echo esc_url( famical_app_url( 'header' ) ); ?>">
				<?php echo esc_html( famical_mod( 'header_cta_text' ) ); ?>
				<?php echo famical_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</a>
		</nav>
	</div>
</header>

<main id="main" class="site-main">
