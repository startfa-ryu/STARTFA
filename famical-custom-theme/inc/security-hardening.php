<?php
/**
 * ความปลอดภัยพื้นฐาน (เพิ่ม 2026-11-03 หลังพบลิงก์เด้งไป TikTok + บัญชีแปลกในหน้า "ผู้ใช้")
 *
 * 1) ใส่สคริปต์ / โค้ด HTML ดิบ (unfiltered_html) ได้เฉพาะ "ผู้ดูแล" (Administrator)
 *    WordPress ปกติให้ "ผู้ตรวจทาน" (Editor) ใส่ <script> ในบทความได้ = ถ้าบัญชี Editor ตกไปอยู่ในมือคนอื่น
 *    เขาฝังสคริปต์พาไปเว็บอื่นในบทความได้ทันที · Editor ยังเขียน/แก้บทความ ฝัง YouTube ได้ตามปกติ
 * 2) สมาชิกใหม่ที่สมัครเองได้บทบาท "ผู้รับข่าวสาร" (Subscriber) เสมอ — กันการตั้งค่าเผลอให้สมัครเป็น Editor
 * 3) ปิดเมนูแก้ไฟล์ธีม/ปลั๊กอินจากหลังบ้าน (Theme / Plugin File Editor) — ถ้ารหัสผ่านหลุด จะฝังโค้ดลงไฟล์ไม่ได้
 *
 * @package FAmiCal_Custom_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function famical_security_caps( $caps, $cap, $user_id ) {
	if ( in_array( $cap, array( 'edit_themes', 'edit_plugins', 'edit_files' ), true ) ) {
		return array( 'do_not_allow' );
	}
	if ( 'unfiltered_html' === $cap && ! user_can( $user_id, 'manage_options' ) ) {
		return array( 'do_not_allow' );
	}
	return $caps;
}
add_filter( 'map_meta_cap', 'famical_security_caps', 10, 3 );

add_filter(
	'pre_option_default_role',
	function () {
		return 'subscriber';
	}
);
