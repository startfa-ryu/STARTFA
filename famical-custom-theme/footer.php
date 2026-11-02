<?php
/**
 * Footer ของธีม FAmiCal Custom Theme
 *
 * @package FAmiCal_Custom_Theme
 */
?>
</main><!-- #main -->

<footer class="site-footer">
	<div class="container footer-cta">
		<div>
			<h2><?php esc_html_e( 'พร้อมวางแผนการเงินของครอบครัวแล้วหรือยัง?', 'famical' ); ?></h2>
			<p><?php esc_html_e( 'เริ่มต้นใช้งาน FAmiCal ได้ทันที ไม่ต้องติดตั้งแอป ใช้ได้ทั้งมือถือและคอมพิวเตอร์', 'famical' ); ?></p>
		</div>
		<a class="btn btn-primary btn-lg" href="<?php echo esc_url( famical_app_url( 'footer' ) ); ?>">
			<?php echo esc_html( famical_mod( 'hero_cta_text' ) ); ?>
			<?php echo famical_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</a>
	</div>

	<div class="container footer-grid">
		<div class="footer-about">
			<a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<span class="logo-mark" aria-hidden="true">S</span>
				<span class="logo-text"><?php bloginfo( 'name' ); ?></span>
			</a>
			<p><?php bloginfo( 'description' ); ?></p>
			<?php
			// ลิงก์ไปหน้า FA ใน FAmiCal จากทุกหน้าของ startfa.com: Google รู้จักเว็บนี้อยู่แล้ว
			// ลิงก์ที่มีชื่อ + STARTFA ในข้อความช่วยให้หน้า FA ถูกค้นเจอเร็วขึ้น (ปรับได้ใน ปรับแต่ง > FAmiCal)
			$famical_fa_url = famical_mod( 'fa_profile_url' );
			if ( $famical_fa_url ) :
				?>
				<p class="footer-fa-link">
					<a href="<?php echo esc_url( add_query_arg( array( 'utm_source' => 'startfa.com', 'utm_medium' => 'referral', 'utm_campaign' => 'landing', 'utm_content' => 'footer-fa' ), $famical_fa_url ) ); ?>">
						<?php echo esc_html( famical_mod( 'fa_profile_label' ) ); ?> →
					</a>
				</p>
			<?php endif; ?>
		</div>

		<nav class="footer-nav" aria-label="<?php esc_attr_e( 'เมนูส่วนท้าย', 'famical' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => false,
					'menu_class'     => 'footer-menu',
					'depth'          => 1,
					'fallback_cb'    => false,
				)
			);
			?>
		</nav>

		<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
			<div class="footer-widgets">
				<?php dynamic_sidebar( 'footer-1' ); ?>
			</div>
		<?php endif; ?>
	</div>

	<div class="container footer-bottom">
		<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'สงวนลิขสิทธิ์', 'famical' ); ?></p>
		<p>
			<a href="<?php echo esc_url( famical_app_url( 'footer-bottom' ) ); ?>">famical.startfa.com</a>
		</p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
