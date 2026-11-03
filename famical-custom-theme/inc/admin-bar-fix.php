<?php
/**
 * แก้แถบแอดมิน (ด้านบนสุดตอนล็อกอิน) ซ้อนทับปุ่ม "บันทึก" ในหน้าแก้ไขบทความ
 *
 * บนมือถือแบบเปิดเป็นเว็บเต็ม / จอเล็ก เมนูในแถบแอดมิน (Rank Math, Performance, Security ฯลฯ)
 * มีมากจนล้นบรรทัด → ส่วน "สวัสดี ชื่อบัญชี" ถูกดันลงมาบรรทัดที่ 2 ไปทับปุ่มบันทึก/เผยแพร่
 * แก้: ซ่อนข้อความทักทาย (เหลือแค่รูปโปรไฟล์ แตะแล้วเมนูบัญชียังเปิดได้เหมือนเดิม)
 *      + ตรึงกลุ่มขวาของแถบไว้ในบรรทัดแรกเสมอ ไม่ให้ตกลงมาทับหน้าแก้ไข
 *
 * @package FAmiCal_Custom_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function famical_admin_bar_fix_css() {
	if ( ! is_admin_bar_showing() ) {
		return;
	}
	?>
	<style id="famical-admin-bar-fix">
		@media screen and (max-width: 1440px) {
			#wpadminbar #wp-admin-bar-my-account > a.ab-item { font-size: 0 !important; padding-right: 8px; }
			#wpadminbar #wp-admin-bar-my-account > a.ab-item img.avatar { margin-left: 0; }
		}
		@media screen and (min-width: 783px) {
			#wpadminbar { overflow: visible; }
			#wpadminbar #wp-admin-bar-top-secondary { position: absolute; top: 0; right: 0; background: #1d2327; }
		}
	</style>
	<?php
}
add_action( 'admin_head', 'famical_admin_bar_fix_css' );
add_action( 'wp_head', 'famical_admin_bar_fix_css' );
