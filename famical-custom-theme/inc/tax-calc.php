<?php
/**
 * เครื่องคิดภาษีย่อในบทความ → ส่งต่อไป FAmiCal (/tax) พร้อมเงินเดือนที่กรอก
 *
 * ใช้ได้ 2 แบบ
 * - วางเองในบทความ: [famical_tax_calc]
 * - อัตโนมัติท้ายบทความหมวดภาษี (ดู index.php)
 *
 * ข้อตกลงกับฝั่ง FAmiCal (repo startfamical, src/lib/startfaPrefill.ts):
 *   https://famical.startfa.com/tax?salary=<เงินเดือนต่อเดือน>
 *   ถ้าเปลี่ยนชื่อพารามิเตอร์ ต้องแก้ทั้งสองฝั่งพร้อมกัน
 *
 * ทำงานได้แม้ปิด JavaScript (เป็นฟอร์ม GET ธรรมดา) JS แค่เพิ่มตัวเลขภาษีโดยประมาณแบบสด
 *
 * @package FAmiCal_Custom_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * HTML ของเครื่องคิดภาษีย่อ
 *
 * @param string $placement ตำแหน่ง (ใช้ใน utm_content)
 */
function famical_tax_calc_html( $placement = 'shortcode' ) {
	wp_enqueue_script(
		'famical-tax-calc',
		get_template_directory_uri() . '/assets/js/tax-calc.js',
		array(),
		FAMICAL_THEME_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	$target = famical_tool_url( 'tax', 'taxcalc-' . $placement );
	$action = strtok( $target, '?' );
	parse_str( (string) wp_parse_url( $target, PHP_URL_QUERY ), $hidden );

	static $instance = 0;
	$instance++;
	$id = 'famical-tax-calc-' . $instance;

	ob_start();
	?>
	<aside class="tax-calc" data-famical-tax-calc>
		<div class="tax-calc-head">
			<span class="tool-cta-icon"><?php echo famical_icon( 'calc' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<div>
				<span class="tool-cta-eyebrow"><?php esc_html_e( 'เครื่องคิดภาษีฟรี · Tax Shield by FAmiCal', 'famical' ); ?></span>
				<strong class="tool-cta-title"><?php esc_html_e( 'ปีนี้คุณต้องเสียภาษีประมาณเท่าไหร่?', 'famical' ); ?></strong>
			</div>
		</div>

		<form class="tax-calc-form" method="get" action="<?php echo esc_url( $action ); ?>">
			<?php foreach ( $hidden as $name => $value ) : ?>
				<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>">
			<?php endforeach; ?>

			<label for="<?php echo esc_attr( $id ); ?>"><?php esc_html_e( 'เงินเดือน (บาท/เดือน)', 'famical' ); ?></label>
			<div class="tax-calc-row">
				<input id="<?php echo esc_attr( $id ); ?>" name="salary" type="text" inputmode="numeric" autocomplete="off"
					placeholder="<?php esc_attr_e( 'เช่น 35,000', 'famical' ); ?>" required pattern="[0-9,\s]+">
				<button type="submit" class="btn btn-primary">
					<?php esc_html_e( 'วางแผนลดหย่อนใน FAmiCal', 'famical' ); ?>
					<?php echo famical_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</button>
			</div>

			<div class="tax-calc-result" aria-live="polite" hidden>
				<div class="tax-calc-figure">
					<small><?php esc_html_e( 'ภาษีโดยประมาณต่อปี (ก่อนลดหย่อนเพิ่ม)', 'famical' ); ?></small>
					<strong data-out="tax">฿0</strong>
				</div>
				<div class="tax-calc-figure">
					<small><?php esc_html_e( 'ฐานภาษีสูงสุดของคุณ', 'famical' ); ?></small>
					<strong data-out="rate">0%</strong>
				</div>
				<p class="tax-calc-hint" data-out="hint"></p>
			</div>

			<p class="tax-calc-note">
				<?php esc_html_e( 'ตัวเลขประมาณการจากเงินเดือนอย่างเดียว (หักค่าใช้จ่าย 50% ไม่เกิน 100,000 ลดหย่อนส่วนตัว 60,000 และประกันสังคม) ใน FAmiCal เพิ่มรายการลดหย่อนอื่นได้ครบ เช่น SSF, RMF, ประกันชีวิต, บุตร', 'famical' ); ?>
			</p>
		</form>
	</aside>
	<?php
	return trim( ob_get_clean() );
}

/**
 * Shortcode: [famical_tax_calc]
 */
function famical_tax_calc_shortcode() {
	return famical_tax_calc_html( 'shortcode' );
}
add_shortcode( 'famical_tax_calc', 'famical_tax_calc_shortcode' );
