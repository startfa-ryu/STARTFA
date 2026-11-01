<?php
/**
 * FAmiCal Custom Theme - functions.php
 *
 * หน้าที่หลักของไฟล์นี้:
 * - เปิดใช้ความสามารถของธีม (title-tag ให้ปลั๊กอิน SEO คุม <title>, รูปหน้าปก, เมนู, โลโก้)
 * - โหลด CSS / JS แบบเบา (ไม่พึ่ง jQuery)
 * - เพิ่มเมนู "ปรับแต่ง > FAmiCal" ให้แก้ข้อความ Hero, ลิงก์ FAmiCal และรีวิวได้โดยไม่ต้องแก้โค้ด
 *
 * @package FAmiCal_Custom_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FAMICAL_THEME_VERSION', '1.0.0' );

/* -------------------------------------------------------------------------
 * 1) Theme setup
 * ---------------------------------------------------------------------- */
function famical_setup() {
	load_theme_textdomain( 'famical', get_template_directory() . '/languages' );

	// ให้ WordPress + Rank Math / Yoast จัดการ <title> เอง (สำคัญต่อ SEO)
	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 64,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	// ขนาดรูปสำหรับการ์ดบทความหน้าแรก (16:9)
	add_image_size( 'famical-card', 640, 360, true );

	register_nav_menus(
		array(
			'primary' => __( 'เมนูหลัก (Header)', 'famical' ),
			'footer'  => __( 'เมนูส่วนท้าย (Footer)', 'famical' ),
		)
	);
}
add_action( 'after_setup_theme', 'famical_setup' );

function famical_content_width() {
	$GLOBALS['content_width'] = 760;
}
add_action( 'after_setup_theme', 'famical_content_width', 0 );

/* -------------------------------------------------------------------------
 * 2) Widgets (sidebar บทความ + footer)
 * ---------------------------------------------------------------------- */
function famical_widgets_init() {
	$common = array(
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	);

	register_sidebar(
		array_merge(
			$common,
			array(
				'name'        => __( 'Sidebar บทความ', 'famical' ),
				'id'          => 'sidebar-1',
				'description' => __( 'แสดงด้านข้างหน้าบทความ / หมวดหมู่ / ค้นหา', 'famical' ),
			)
		)
	);

	register_sidebar(
		array_merge(
			$common,
			array(
				'name'        => __( 'Footer', 'famical' ),
				'id'          => 'footer-1',
				'description' => __( 'แสดงในคอลัมน์ส่วนท้ายเว็บ', 'famical' ),
			)
		)
	);
}
add_action( 'widgets_init', 'famical_widgets_init' );

/* -------------------------------------------------------------------------
 * 3) CSS / JS
 * ---------------------------------------------------------------------- */
function famical_scripts() {
	// ฟอนต์ไทย (display=swap กันหน้าค้างตอนโหลดฟอนต์)
	wp_enqueue_style(
		'famical-fonts',
		'https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@400;500;600;700&display=swap',
		array(),
		null
	);

	wp_enqueue_style( 'famical-style', get_stylesheet_uri(), array( 'famical-fonts' ), FAMICAL_THEME_VERSION );

	wp_enqueue_script(
		'famical-navigation',
		get_template_directory_uri() . '/assets/js/navigation.js',
		array(),
		FAMICAL_THEME_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'famical_scripts' );

// preconnect ไปยัง Google Fonts ให้โหลดเร็วขึ้น
function famical_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array( 'href' => 'https://fonts.googleapis.com' );
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'famical_resource_hints', 10, 2 );

// ตัดสคริปต์ emoji ที่ไม่จำเป็นออก (ช่วยเรื่องความเร็ว ไม่กระทบ SEO)
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

/* -------------------------------------------------------------------------
 * 4) Customizer: ปรับแต่ง > FAmiCal
 * ---------------------------------------------------------------------- */

/**
 * ค่าเริ่มต้นทั้งหมดของธีม (แก้ได้จาก Customizer)
 */
function famical_defaults() {
	return array(
		'famical_url'        => 'https://famical.startfa.com/',
		'header_cta_text'    => 'เปิด FAmiCal',
		'hero_badge'         => 'ใช้งานฟรี · Beta Version',
		'hero_title'         => 'วางแผนการเงินทั้งครอบครัว ครบในที่เดียว',
		'hero_subtitle'      => 'FAmiCal ช่วยจัดการรายรับรายจ่าย วางแผนภาษี เงินเพื่อลูก และปกป้องมูลค่าครอบครัว ด้วยเครื่องมือที่ออกแบบโดยทีมที่ปรึกษาการเงินมืออาชีพ STARTFA',
		'hero_cta_text'      => 'เริ่มใช้งาน FAmiCal ฟรี',
		'hero_cta2_text'     => 'อ่านบทความการเงิน',
		'testimonial_1_text' => 'ตัวอย่างรีวิว: ใช้ Tax Shield วางแผนลดหย่อนภาษีแล้วเห็นภาพชัดขึ้นมาก ไม่ต้องนั่งทำ Excel เองอีกต่อไป',
		'testimonial_1_name' => 'ชื่อผู้ใช้งาน',
		'testimonial_1_role' => 'พนักงานบริษัท',
		'testimonial_2_text' => 'ตัวอย่างรีวิว: Kids Blueprint ทำให้รู้ว่าต้องเก็บเงินเดือนละเท่าไหร่สำหรับค่าเรียนลูก วางแผนง่ายขึ้นเยอะ',
		'testimonial_2_name' => 'ชื่อผู้ใช้งาน',
		'testimonial_2_role' => 'คุณแม่ลูกสอง',
		'testimonial_3_text' => 'ตัวอย่างรีวิว: Financial Reset ช่วยจัดลำดับการปิดหนี้ได้เป็นระบบ ใช้คู่กับการปรึกษาทีม STARTFA แล้วเห็นผลจริง',
		'testimonial_3_name' => 'ชื่อผู้ใช้งาน',
		'testimonial_3_role' => 'เจ้าของธุรกิจ',
	);
}

/**
 * อ่านค่าจาก Customizer พร้อม fallback เป็นค่าเริ่มต้น
 */
function famical_mod( $key ) {
	$defaults = famical_defaults();
	return get_theme_mod( $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

function famical_customize_register( $wp_customize ) {
	$defaults = famical_defaults();

	$wp_customize->add_panel(
		'famical_panel',
		array(
			'title'    => __( 'FAmiCal', 'famical' ),
			'priority' => 30,
		)
	);

	// --- ลิงก์ + Hero ---
	$wp_customize->add_section(
		'famical_hero',
		array(
			'title' => __( 'Hero & ลิงก์ FAmiCal', 'famical' ),
			'panel' => 'famical_panel',
		)
	);

	$hero_fields = array(
		'famical_url'     => array( __( 'URL ของ FAmiCal', 'famical' ), 'url' ),
		'header_cta_text' => array( __( 'ข้อความปุ่มบน Header', 'famical' ), 'text' ),
		'hero_badge'      => array( __( 'ป้ายเล็กเหนือพาดหัว', 'famical' ), 'text' ),
		'hero_title'      => array( __( 'พาดหัวหลัก (H1)', 'famical' ), 'text' ),
		'hero_subtitle'   => array( __( 'คำอธิบายใต้พาดหัว', 'famical' ), 'textarea' ),
		'hero_cta_text'   => array( __( 'ข้อความปุ่ม CTA หลัก', 'famical' ), 'text' ),
		'hero_cta2_text'  => array( __( 'ข้อความปุ่มรอง (ไปบทความ)', 'famical' ), 'text' ),
	);

	foreach ( $hero_fields as $id => $field ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $defaults[ $id ],
				'sanitize_callback' => 'url' === $field[1] ? 'esc_url_raw' : ( 'textarea' === $field[1] ? 'sanitize_textarea_field' : 'sanitize_text_field' ),
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $field[0],
				'section' => 'famical_hero',
				'type'    => $field[1],
			)
		);
	}

	// --- รีวิว ---
	$wp_customize->add_section(
		'famical_testimonials',
		array(
			'title'       => __( 'รีวิวจากผู้ใช้งาน', 'famical' ),
			'panel'       => 'famical_panel',
			'description' => __( 'ใส่รีวิวจริงจากผู้ใช้งาน (ควรได้รับอนุญาตจากเจ้าของรีวิว) ช่องที่เว้นว่างจะไม่แสดง', 'famical' ),
		)
	);

	for ( $i = 1; $i <= 3; $i++ ) {
		$parts = array(
			'text' => array( sprintf( __( 'รีวิว #%d - ข้อความ', 'famical' ), $i ), 'textarea', 'sanitize_textarea_field' ),
			'name' => array( sprintf( __( 'รีวิว #%d - ชื่อ', 'famical' ), $i ), 'text', 'sanitize_text_field' ),
			'role' => array( sprintf( __( 'รีวิว #%d - อาชีพ/บทบาท', 'famical' ), $i ), 'text', 'sanitize_text_field' ),
		);
		foreach ( $parts as $part => $field ) {
			$id = "testimonial_{$i}_{$part}";
			$wp_customize->add_setting(
				$id,
				array(
					'default'           => $defaults[ $id ],
					'sanitize_callback' => $field[2],
				)
			);
			$wp_customize->add_control(
				$id,
				array(
					'label'   => $field[0],
					'section' => 'famical_testimonials',
					'type'    => $field[1],
				)
			);
		}
	}
}
add_action( 'customize_register', 'famical_customize_register' );

/* -------------------------------------------------------------------------
 * 5) Helpers
 * ---------------------------------------------------------------------- */

/**
 * ลิงก์ไป FAmiCal พร้อม UTM เพื่อวัดผลใน Google Analytics ว่าคลิกมาจากจุดไหน
 *
 * @param string $placement ตำแหน่งปุ่ม เช่น header, hero, features, footer
 */
function famical_app_url( $placement = 'general' ) {
	$url = famical_mod( 'famical_url' );
	return add_query_arg(
		array(
			'utm_source'   => 'startfa.com',
			'utm_medium'   => 'referral',
			'utm_campaign' => 'landing',
			'utm_content'  => sanitize_key( $placement ),
		),
		$url
	);
}

/**
 * ลิงก์ไปหน้ารวมบทความ
 * (ถ้าตั้งค่า "หน้าบทความ" ไว้ใน การตั้งค่า > การอ่าน จะลิงก์ไปหน้านั้น)
 */
function famical_blog_url() {
	$page_for_posts = (int) get_option( 'page_for_posts' );
	if ( $page_for_posts ) {
		return get_permalink( $page_for_posts );
	}
	return home_url( '/#articles' );
}

/**
 * รายการรีวิวที่มีข้อความ (ช่องว่างจะถูกข้าม)
 */
function famical_get_testimonials() {
	$items = array();
	for ( $i = 1; $i <= 3; $i++ ) {
		$text = trim( (string) famical_mod( "testimonial_{$i}_text" ) );
		if ( '' === $text ) {
			continue;
		}
		$items[] = array(
			'text' => $text,
			'name' => famical_mod( "testimonial_{$i}_name" ),
			'role' => famical_mod( "testimonial_{$i}_role" ),
		);
	}
	return apply_filters( 'famical_testimonials', $items );
}

/**
 * ไอคอน SVG แบบ inline (ไม่ต้องโหลด icon font = เว็บเร็วขึ้น)
 */
function famical_icon( $name ) {
	$icons = array(
		'calc'     => '<rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="10" x2="8" y2="10.01"/><line x1="12" y1="10" x2="12" y2="10.01"/><line x1="16" y1="10" x2="16" y2="10.01"/><line x1="8" y1="14" x2="8" y2="14.01"/><line x1="12" y1="14" x2="12" y2="14.01"/><line x1="16" y1="14" x2="16" y2="18"/><line x1="8" y1="18" x2="12" y2="18"/>',
		'devices'  => '<rect x="2" y="4" width="14" height="10" rx="1.5"/><path d="M6 18h6"/><rect x="17" y="8" width="5" height="12" rx="1"/>',
		'shield'   => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/>',
		'user'     => '<circle cx="12" cy="8" r="4"/><path d="M4 21v-1a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v1"/>',
		'baby'     => '<circle cx="12" cy="12" r="9"/><path d="M9 10h.01M15 10h.01"/><path d="M9.5 15a3.5 3.5 0 0 0 5 0"/>',
		'reset'    => '<path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/>',
		'heart'    => '<path d="M12 21s-7-4.4-9.3-9A5.4 5.4 0 0 1 12 6a5.4 5.4 0 0 1 9.3 6c-2.3 4.6-9.3 9-9.3 9z"/>',
		'building' => '<rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4M8 6h.01M12 6h.01M16 6h.01M8 10h.01M12 10h.01M16 10h.01M8 14h.01M12 14h.01M16 14h.01"/>',
		'arrow'    => '<path d="M5 12h14M13 5l7 7-7 7"/>',
		'quote'    => '<path d="M7 7h4v4c0 3-2 5-4 6M15 7h4v4c0 3-2 5-4 6"/>',
		'menu'     => '<path d="M4 6h16M4 12h16M4 18h16"/>',
	);
	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}
	return '<svg class="icon icon-' . esc_attr( $name ) . '" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $icons[ $name ] . '</svg>';
}

/**
 * เมนูสำรองเมื่อยังไม่ได้กำหนดเมนูหลัก
 */
function famical_fallback_menu() {
	echo '<ul class="menu">';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'หน้าแรก', 'famical' ) . '</a></li>';
	echo '<li><a href="' . esc_url( home_url( '/#features' ) ) . '">' . esc_html__( 'ฟีเจอร์', 'famical' ) . '</a></li>';
	echo '<li><a href="' . esc_url( famical_blog_url() ) . '">' . esc_html__( 'บทความ', 'famical' ) . '</a></li>';
	echo '</ul>';
}

/**
 * excerpt สั้นลงสำหรับการ์ดบทความ
 */
function famical_excerpt_length( $length ) {
	return is_admin() ? $length : 28;
}
add_filter( 'excerpt_length', 'famical_excerpt_length' );

function famical_excerpt_more() {
	return '…';
}
add_filter( 'excerpt_more', 'famical_excerpt_more' );
