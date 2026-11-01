<?php
/**
 * ระบบช่องโฆษณาของธีม
 *
 * แนวคิด "โฆษณาสวย ไม่รก":
 * - หน้าแรก: ไม่มีโฆษณาด้านบน มีแค่ช่องเดียวด้านล่าง (หน้าแรกมีไว้พาคนไป FAmiCal)
 * - บทความ: สูงสุด 3 ตำแหน่ง = กลางบทความ (เฉพาะบทความยาว) + ท้ายบทความ + sidebar ติดตามตอนเลื่อน
 * - หน้ารวมบทความ/หมวดหมู่: 1 ช่องแทรกในรายการ ดีไซน์เหมือนการ์ดบทความ
 * - ทุกช่องมีป้าย "โฆษณา" และจองพื้นที่ไว้ กันหน้ากระตุก (CLS)
 * - ช่องที่ยังไม่ได้ใส่วิดเจ็ตจะไม่แสดงอะไรเลย
 *
 * วิธีใส่โฆษณา: รูปลักษณ์ > วิดเจ็ต > ลาก "HTML ที่กำหนดเอง" ไปใส่ช่อง แล้ววางโค้ด AdSense
 *
 * @package FAmiCal_Custom_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ลงทะเบียนช่องโฆษณา
 */
function famical_register_ad_areas() {
	$ad_areas = array(
		'ad-home-bottom'    => array( __( 'โฆษณา: หน้าแรก (ด้านล่าง ก่อนส่วนท้าย)', 'famical' ), __( 'แสดงที่หน้าแรก ใต้รายการบทความ', 'famical' ) ),
		'ad-in-content'     => array( __( 'โฆษณา: กลางบทความ (อัตโนมัติ)', 'famical' ), __( 'แทรกหลังย่อหน้าที่ 3 อัตโนมัติ เฉพาะบทความที่ยาว 6 ย่อหน้าขึ้นไป', 'famical' ) ),
		'ad-after-content'  => array( __( 'โฆษณา: ใต้เนื้อหาบทความ', 'famical' ), __( 'แสดงท้ายบทความทุกเรื่อง ก่อนกล่องชวนใช้ FAmiCal', 'famical' ) ),
		'ad-sidebar-sticky' => array( __( 'โฆษณา: Sidebar (ติดตามตอนเลื่อน)', 'famical' ), __( 'แสดงด้านข้างหน้าบทความบนคอมพิวเตอร์ และย้ายไปอยู่ใต้บทความบนมือถือ', 'famical' ) ),
		'ad-archive-feed'   => array( __( 'โฆษณา: ในรายการบทความ', 'famical' ), __( 'แทรกหลังการ์ดบทความใบที่ 4 ในหน้ารวมบทความ / หมวดหมู่ / ค้นหา', 'famical' ) ),
	);

	foreach ( $ad_areas as $id => $info ) {
		register_sidebar(
			array(
				'name'          => $info[0],
				'id'            => $id,
				'description'   => $info[1],
				'before_widget' => '<div id="%1$s" class="ad-widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<span class="screen-reader-text">',
				'after_title'   => '</span>',
			)
		);
	}
}
add_action( 'widgets_init', 'famical_register_ad_areas', 20 );

/**
 * ควรแสดงโฆษณาในหน้านี้หรือไม่
 * ปิดโฆษณาเฉพาะบทความได้ด้วยการใส่ Custom Field: famical_no_ads = 1
 */
function famical_ads_enabled() {
	$enabled = true;
	if ( is_singular() && get_post_meta( get_the_ID(), 'famical_no_ads', true ) ) {
		$enabled = false;
	}
	return (bool) apply_filters( 'famical_ads_enabled', $enabled );
}

/**
 * คืน HTML ของช่องโฆษณา (ว่างถ้าไม่มีวิดเจ็ต)
 *
 * @param string $area  id ของช่องโฆษณา
 * @param string $class class เพิ่มเติม
 */
function famical_get_ad_slot( $area, $class = '' ) {
	if ( ! famical_ads_enabled() || ! is_active_sidebar( $area ) ) {
		return '';
	}
	ob_start();
	dynamic_sidebar( $area );
	$inner = ob_get_clean();

	return '<aside class="ad-slot ' . esc_attr( $class ) . '" aria-label="' . esc_attr__( 'โฆษณา', 'famical' ) . '">'
		. '<span class="ad-label">' . esc_html__( 'โฆษณา', 'famical' ) . '</span>'
		. $inner
		. '</aside>';
}

/**
 * แสดงช่องโฆษณา
 */
function famical_ad_slot( $area, $class = '' ) {
	echo famical_get_ad_slot( $area, $class ); // phpcs:ignore WordPress.Security.EscapeOutput -- โค้ดโฆษณาจากวิดเจ็ตของแอดมิน
}

/**
 * มี sidebar หน้าบทความหรือไม่ (วิดเจ็ตปกติ หรือโฆษณา sidebar)
 */
function famical_has_sidebar() {
	if ( is_page() || is_404() ) {
		return false;
	}
	return is_active_sidebar( 'sidebar-1' ) || ( famical_ads_enabled() && is_active_sidebar( 'ad-sidebar-sticky' ) );
}

/**
 * แทรกโฆษณากลางบทความหลังย่อหน้าที่ 3 (เฉพาะบทความยาว)
 * จะไม่แทรกภายใน blockquote / ตาราง / รายการ เพื่อไม่ให้เลย์เอาต์พัง
 *
 * @param string $content เนื้อหาบทความ
 */
function famical_insert_in_content_ad( $content ) {
	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}
	if ( false !== strpos( $content, '<!--noads-->' ) ) {
		return $content;
	}

	$ad = famical_get_ad_slot( 'ad-in-content', 'ad-in-content' );
	if ( '' === $ad ) {
		return $content;
	}

	$after_paragraph = (int) apply_filters( 'famical_in_content_ad_after', 3 );
	$min_paragraphs  = (int) apply_filters( 'famical_in_content_ad_min_paragraphs', 6 );

	$parts = explode( '</p>', $content );
	if ( count( $parts ) - 1 < $min_paragraphs ) {
		return $content;
	}

	$output   = '';
	$inserted = false;
	$total    = count( $parts );

	foreach ( $parts as $i => $part ) {
		$output .= $part;
		if ( $i < $total - 1 ) {
			$output .= '</p>';
		}

		if ( ! $inserted && $i + 1 >= $after_paragraph && $i < $total - 2 ) {
			// ตรวจว่าตอนนี้ไม่ได้อยู่ภายใน tag ที่ห้ามแทรก
			$inside = false;
			foreach ( array( 'blockquote', 'table', 'ul', 'ol', 'figure' ) as $tag ) {
				if ( substr_count( $output, '<' . $tag ) > substr_count( $output, '</' . $tag . '>' ) ) {
					$inside = true;
					break;
				}
			}
			if ( ! $inside ) {
				$output  .= $ad;
				$inserted = true;
			}
		}
	}

	return $output;
}
add_filter( 'the_content', 'famical_insert_in_content_ad', 20 );
