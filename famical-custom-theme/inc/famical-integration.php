<?php
/**
 * เชื่อม startfa.com กับ FAmiCal
 *
 * - เลือกเครื่องมือ FAmiCal ที่ตรงกับเนื้อหาบทความโดยอัตโนมัติ (ดูจากหมวดหมู่ / แท็ก / ชื่อบทความ)
 *   เช่น บทความภาษี → ชวนใช้ Tax Shield, บทความประกัน → Value Ensure
 * - Shortcode [famical_cta tool="tax"] วางกล่องชวนใช้เครื่องมือตรงไหนของบทความก็ได้
 * - กล่องชวนใช้เครื่องมือในหน้าหมวดหมู่
 * - Structured data (JSON-LD) ของ FAmiCal บนหน้าแรก
 *
 * @package FAmiCal_Custom_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * รายการเครื่องมือ FAmiCal + คำค้นที่ใช้จับคู่กับบทความ
 * ลำดับมีผล: ตัวที่อยู่บนจะถูกเลือกก่อนถ้าบทความตรงหลายหมวด
 *
 * 'path' ใช้ต่อท้าย URL ของ FAmiCal ถ้าแอปรองรับลิงก์ตรงไปเครื่องมือ (ปัจจุบันเว้นว่าง = ไปหน้าแรกแอป)
 */
function famical_tools() {
	$tools = array(
		'tax'       => array(
			'name'     => 'Tax Shield',
			'icon'     => 'calc',
			'title'    => __( 'คำนวณภาษีและสิทธิลดหย่อนของคุณ', 'famical' ),
			'text'     => __( 'ใส่รายได้แล้วดูทันทีว่าต้องเสียภาษีเท่าไหร่ และควรลดหย่อนด้วยอะไรให้คุ้มที่สุด', 'famical' ),
			'keywords' => array( 'ภาษี', 'ลดหย่อน', 'tax', 'ssf', 'rmf', 'thaiesg', 'ยื่นภาษี' ),
			'path'     => 'tax', // หน้า Tax Cal ที่ใช้งานจริงใน FAmiCal
		),
		'insurance' => array(
			'name'     => 'Value Ensure',
			'icon'     => 'shield',
			'title'    => __( 'เปรียบเทียบแบบประกันด้วยตัวเลขจริง', 'famical' ),
			'text'     => __( 'คำนวณ IRR และเปรียบเทียบความคุ้มค่าของแบบประกัน ก่อนตัดสินใจซื้อ', 'famical' ),
			'keywords' => array( 'ประกัน', 'insurance', 'สะสมทรัพย์', 'บำนาญ', 'ชีวิต', 'สุขภาพ' ),
			'path'     => '',
		),
		'kids'      => array(
			'name'     => 'Kids Blueprint',
			'icon'     => 'baby',
			'title'    => __( 'วางแผนเงินเพื่อลูกตั้งแต่วันนี้', 'famical' ),
			'text'     => __( 'รู้ทันทีว่าต้องเก็บเดือนละเท่าไหร่ สำหรับค่าคลอด ค่าเล่าเรียน และอนาคตของลูก', 'famical' ),
			'keywords' => array( 'เพื่อลูก', 'ของลูก', 'ลูกน้อย', 'ลูกหลาน', 'เด็ก', 'การศึกษา', 'ค่าเทอม', 'คลอด', 'kid' ), // ไม่ใช้คำว่า "ลูก" เดี่ยวๆ เพราะจะไปตรงกับ "ลูกค้า"
			'path'     => '',
		),
		'debt'      => array(
			'name'     => 'Financial Reset',
			'icon'     => 'reset',
			'title'    => __( 'จัดการหนี้และรายจ่ายให้เป็นระบบ', 'famical' ),
			'text'     => __( 'แยกรายรับรายจ่าย 3 สี และจำลองแผนปลดหนี้ เห็นวันที่หนี้หมดชัดเจน', 'famical' ),
			'keywords' => array( 'หนี้', 'debt', 'รายจ่าย', 'บัตรเครดิต', 'ผ่อน', 'งบประมาณ' ),
			'path'     => '',
		),
		'business'  => array(
			'name'     => 'Business Pilot',
			'icon'     => 'building',
			'title'    => __( 'แยกบัญชีธุรกิจออกจากเงินส่วนตัว', 'famical' ),
			'text'     => __( 'บันทึกรายรับ-รายจ่ายธุรกิจ และเตรียมข้อมูลภาษีได้ง่ายขึ้น', 'famical' ),
			'keywords' => array( 'ธุรกิจ', 'business', 'sme', 'ฟรีแลนซ์', 'freelance', 'ร้านค้า' ),
			'path'     => '',
		),
		'life'      => array(
			'name'     => 'Life Blueprint',
			'icon'     => 'user',
			'title'    => __( 'สร้างพิมพ์เขียวการเงินของคุณ', 'famical' ),
			'text'     => __( 'เห็นภาพรวมการเงินทั้งชีวิต ทั้งเป้าหมาย การออม การลงทุน และเกษียณ ในที่เดียว', 'famical' ),
			'keywords' => array( 'เกษียณ', 'ออม', 'ลงทุน', 'กองทุน', 'วางแผน' ),
			'path'     => '',
		),
	);
	return apply_filters( 'famical_tools', $tools );
}

/**
 * ลิงก์ไปเครื่องมือ FAmiCal พร้อม UTM
 *
 * @param string $tool_key  key ของเครื่องมือ
 * @param string $placement ตำแหน่งที่วางลิงก์
 */
function famical_tool_url( $tool_key, $placement ) {
	$tools = famical_tools();
	$url   = famical_app_url( $placement . '-' . $tool_key );
	if ( isset( $tools[ $tool_key ] ) && '' !== $tools[ $tool_key ]['path'] ) {
		$base = trailingslashit( famical_mod( 'famical_url' ) ) . ltrim( $tools[ $tool_key ]['path'], '/' );
		$url  = add_query_arg( wp_parse_args( wp_parse_url( $url, PHP_URL_QUERY ) ), $base );
	}
	return $url;
}

/**
 * เลือกเครื่องมือที่เข้ากับบทความ
 * ดูหมวดหมู่ + แท็กก่อน ถ้าไม่เจอค่อยดูชื่อบทความ ไม่เจอเลยใช้ Life Blueprint
 *
 * ระบุเองได้ด้วย Custom Field: famical_tool = tax / insurance / kids / debt / business / life
 *
 * @param int|null $post_id
 */
function famical_match_tool( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$tools   = famical_tools();

	$manual = get_post_meta( $post_id, 'famical_tool', true );
	if ( $manual && isset( $tools[ $manual ] ) ) {
		return $manual;
	}

	$terms = wp_get_post_terms( $post_id, array( 'category', 'post_tag' ), array( 'fields' => 'all' ) );
	$haystacks = array();
	if ( ! is_wp_error( $terms ) && $terms ) {
		$haystacks[] = implode( ' ', wp_list_pluck( $terms, 'name' ) ) . ' ' . implode( ' ', array_map( 'urldecode', wp_list_pluck( $terms, 'slug' ) ) );
	}
	$haystacks[] = get_the_title( $post_id );

	foreach ( $haystacks as $haystack ) {
		$haystack = function_exists( 'mb_strtolower' ) ? mb_strtolower( $haystack ) : strtolower( $haystack );
		foreach ( $tools as $key => $tool ) {
			foreach ( $tool['keywords'] as $keyword ) {
				if ( '' !== $keyword && false !== strpos( $haystack, $keyword ) ) {
					return $key;
				}
			}
		}
	}

	return isset( $tools['life'] ) ? 'life' : key( $tools );
}

/**
 * HTML กล่องชวนใช้เครื่องมือ
 *
 * @param string $tool_key
 * @param string $placement
 * @param string $variant   'card' (กล่องใหญ่) หรือ 'slim' (แถบบาง)
 */
function famical_get_tool_cta( $tool_key, $placement = 'single-post', $variant = 'card' ) {
	$tools = famical_tools();
	if ( ! isset( $tools[ $tool_key ] ) ) {
		return '';
	}
	$tool = $tools[ $tool_key ];

	ob_start();
	?>
	<aside class="tool-cta tool-cta-<?php echo esc_attr( $variant ); ?>">
		<span class="tool-cta-icon"><?php echo famical_icon( $tool['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		<div class="tool-cta-body">
			<span class="tool-cta-eyebrow"><?php echo esc_html( sprintf( __( 'เครื่องมือฟรีใน FAmiCal · %s', 'famical' ), $tool['name'] ) ); ?></span>
			<strong class="tool-cta-title"><?php echo esc_html( $tool['title'] ); ?></strong>
			<?php if ( 'card' === $variant ) : ?>
				<p><?php echo esc_html( $tool['text'] ); ?></p>
			<?php endif; ?>
		</div>
		<a class="btn btn-primary" href="<?php echo esc_url( famical_tool_url( $tool_key, $placement ) ); ?>">
			<?php echo esc_html( sprintf( __( 'ลองใช้ %s', 'famical' ), $tool['name'] ) ); ?>
			<?php echo famical_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</a>
	</aside>
	<?php
	return trim( ob_get_clean() );
}

/**
 * Shortcode: [famical_cta] หรือ [famical_cta tool="tax"] หรือ [famical_cta tool="kids" style="slim"]
 */
function famical_cta_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'tool'  => '',
			'style' => 'card',
		),
		$atts,
		'famical_cta'
	);
	$tool  = $atts['tool'] ? sanitize_key( $atts['tool'] ) : famical_match_tool();
	$style = 'slim' === $atts['style'] ? 'slim' : 'card';
	return famical_get_tool_cta( $tool, 'shortcode', $style );
}
add_shortcode( 'famical_cta', 'famical_cta_shortcode' );

/**
 * กล่องชวนใช้เครื่องมือในหน้าหมวดหมู่ / แท็ก
 */
function famical_archive_tool_cta() {
	if ( ! ( is_category() || is_tag() ) ) {
		return;
	}
	$term  = get_queried_object();
	$tools = famical_tools();
	if ( ! $term || empty( $term->name ) ) {
		return;
	}
	$haystack = $term->name . ' ' . urldecode( $term->slug );
	$haystack = function_exists( 'mb_strtolower' ) ? mb_strtolower( $haystack ) : strtolower( $haystack );
	foreach ( $tools as $key => $tool ) {
		foreach ( $tool['keywords'] as $keyword ) {
			if ( false !== strpos( $haystack, $keyword ) ) {
				echo famical_get_tool_cta( $key, 'archive', 'slim' ); // phpcs:ignore WordPress.Security.EscapeOutput
				return;
			}
		}
	}
}

/**
 * Structured data ของ FAmiCal บนหน้าแรก (ช่วยให้ Google เข้าใจว่าเว็บนี้มีแอปอะไร)
 * ไม่ซ้ำกับ Rank Math / Yoast เพราะปลั๊กอินพวกนั้นสร้าง WebSite / Organization ไม่ได้สร้าง SoftwareApplication
 */
function famical_front_page_schema() {
	if ( ! is_front_page() ) {
		return;
	}
	$schema = array(
		'@context'            => 'https://schema.org',
		'@type'               => 'SoftwareApplication',
		'name'                => 'FAmiCal',
		'url'                 => famical_mod( 'famical_url' ),
		'applicationCategory' => 'FinanceApplication',
		'operatingSystem'     => 'Web',
		'inLanguage'          => array( 'th', 'en' ),
		'description'         => wp_strip_all_tags( famical_mod( 'hero_subtitle' ) ),
		'publisher'           => array(
			'@type' => 'Organization',
			'name'  => get_bloginfo( 'name' ),
			'url'   => home_url( '/' ),
		),
	);
	$schema = apply_filters( 'famical_front_page_schema', $schema );
	if ( empty( $schema ) ) {
		return;
	}
	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'famical_front_page_schema', 20 );
