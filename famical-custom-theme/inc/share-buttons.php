<?php
/**
 * ปุ่มแชร์ท้ายบทความทุกเรื่องอัตโนมัติ (ไม่ต้องใส่บล็อกเองทีละบทความ · ใช้แทนปลั๊กอิน Simple Share Buttons)
 *
 * - Facebook · LINE · X · คัดลอกลิงก์ · บนมือถือมีปุ่ม "แชร์…" ของเครื่อง (Web Share)
 * - ลิงก์ตรงไปหน้าแชร์ของแต่ละแอป ไม่โหลดสคริปต์จากเว็บอื่น = เร็ว + ไม่มีโค้ดภายนอกมาแทรกในเว็บ
 * - ปิดได้ที่ ปรับแต่ง > FAmiCal > ปุ่มแชร์ หรือรายบทความด้วย Custom Field: famical_no_share = 1
 *
 * @package FAmiCal_Custom_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function famical_share_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'famical_share',
		array(
			'title' => __( 'ปุ่มแชร์', 'famical' ),
			'panel' => 'famical_panel',
		)
	);
	$wp_customize->add_setting(
		'share_buttons_enabled',
		array(
			'default'           => true,
			'sanitize_callback' => 'wp_validate_boolean',
		)
	);
	$wp_customize->add_control(
		'share_buttons_enabled',
		array(
			'label'       => __( 'แสดงปุ่มแชร์ท้ายบทความ', 'famical' ),
			'description' => __( 'ถ้าเปิดไว้ ควรปิดปลั๊กอินปุ่มแชร์อื่น (Simple Share Buttons) เพื่อไม่ให้ซ้ำกัน', 'famical' ),
			'section'     => 'famical_share',
			'type'        => 'checkbox',
		)
	);
}
add_action( 'customize_register', 'famical_share_customize_register', 20 );

function famical_share_buttons() {
	if ( ! is_singular( 'post' ) || ! get_theme_mod( 'share_buttons_enabled', true ) || get_post_meta( get_the_ID(), 'famical_no_share', true ) ) {
		return;
	}
	$url   = get_permalink();
	$title = wp_strip_all_tags( get_the_title() );
	$links = array(
		'facebook' => array( 'Facebook', 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( $url ) ),
		'line'     => array( 'LINE', 'https://social-plugins.line.me/lineit/share?url=' . rawurlencode( $url ) ),
		'x'        => array( 'X', 'https://twitter.com/intent/tweet?url=' . rawurlencode( $url ) . '&text=' . rawurlencode( $title ) ),
	);
	?>
	<div class="share-bar" data-url="<?php echo esc_url( $url ); ?>" data-title="<?php echo esc_attr( $title ); ?>">
		<span class="share-label"><?php esc_html_e( 'แชร์บทความนี้', 'famical' ); ?></span>
		<?php foreach ( $links as $key => $l ) : ?>
			<a class="share-btn share-<?php echo esc_attr( $key ); ?>" href="<?php echo esc_url( $l[1] ); ?>" target="_blank" rel="noopener nofollow"><?php echo esc_html( $l[0] ); ?></a>
		<?php endforeach; ?>
		<button type="button" class="share-btn share-copy" data-share-copy><?php esc_html_e( 'คัดลอกลิงก์', 'famical' ); ?></button>
		<button type="button" class="share-btn share-native" data-share-native hidden><?php esc_html_e( 'แชร์…', 'famical' ); ?></button>
	</div>
	<?php
}

function famical_share_assets() {
	if ( ! is_singular( 'post' ) || ! get_theme_mod( 'share_buttons_enabled', true ) ) {
		return;
	}
	?>
	<style id="famical-share">
		.share-bar { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin: 1.5em 0; padding: 12px 0; border-top: 1px solid var(--line, #e3e8f2); border-bottom: 1px solid var(--line, #e3e8f2); }
		.share-label { font-weight: 600; margin-right: 4px; }
		.share-btn { display: inline-flex; align-items: center; padding: 7px 14px; border-radius: 999px; border: 0; font: 600 14px/1 inherit; color: #fff; text-decoration: none; cursor: pointer; }
		.share-btn:hover { opacity: .9; color: #fff; }
		.share-facebook { background: #1877f2; }
		.share-line { background: #06c755; }
		.share-x { background: #111; }
		.share-copy, .share-native { background: var(--bg-soft, #f4f7fc); color: var(--ink, #0f1a33); border: 1px solid var(--line, #e3e8f2); }
		.share-copy:hover, .share-native:hover { color: var(--ink, #0f1a33); }
		.share-btn[hidden] { display: none; }
	</style>
	<script>
	( function () {
		document.querySelectorAll( '.share-bar' ).forEach( function ( bar ) {
			var url = bar.getAttribute( 'data-url' ), title = bar.getAttribute( 'data-title' );
			var copy = bar.querySelector( '[data-share-copy]' ), native = bar.querySelector( '[data-share-native]' );
			if ( native && navigator.share ) {
				native.hidden = false;
				native.addEventListener( 'click', function () { navigator.share( { title: title, url: url } ).catch( function () {} ); } );
			}
			if ( copy ) {
				copy.addEventListener( 'click', function () {
					var label = copy.textContent;
					var ok = function () { copy.textContent = 'คัดลอกแล้ว ✓'; setTimeout( function () { copy.textContent = label; }, 2000 ); };
					if ( navigator.clipboard && navigator.clipboard.writeText ) {
						navigator.clipboard.writeText( url ).then( ok, function () { window.prompt( 'คัดลอกลิงก์', url ); } );
					} else {
						window.prompt( 'คัดลอกลิงก์', url );
					}
				} );
			}
		} );
	} )();
	</script>
	<?php
}
add_action( 'wp_footer', 'famical_share_assets', 40 );
