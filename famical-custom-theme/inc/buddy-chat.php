<?php
/**
 * ปุ่มแชท "ถามจิ๋วแจ๋ว" มุมขวาล่างทุกหน้า (ใช้แทนปุ่มแชท GetButton เดิม)
 *
 * - เปิดหน้าต่างเล็ก: คำถามยอดฮิต (แตะแล้วจิ๋วแจ๋วตอบทันที ไม่ต้องโหลดอะไรเพิ่ม)
 * - ปุ่มหลัก "ออกแบบประกันกับจิ๋วแจ๋ว" → หน้า /design ของ FAmiCal (Lead เข้าแอดมิน STARTFA)
 * - ปุ่ม "คุยกับคนจริงทาง LINE" (ใส่ลิงก์ใน ปรับแต่ง > FAmiCal > ปุ่มแชทจิ๋วแจ๋ว)
 * - ปิดทั้งปุ่มได้จาก Customizer
 *
 * หน้า FAmiCal ห้ามฝังใน iframe (กันการหลอกกด) จึงใช้วิธีเปิดลิงก์แทน
 *
 * @package FAmiCal_Custom_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function famical_buddy_enabled() {
	return (bool) get_theme_mod( 'buddy_chat_enabled', true );
}

/**
 * ลิงก์หน้า "ออกแบบประกันกับจิ๋วแจ๋ว" พร้อม UTM (utm_source=startfa.com → Lead เข้าแอดมิน STARTFA)
 *
 * @param string $placement ตำแหน่งปุ่ม เช่น chat, hero, home-section
 */
function famical_design_url( $placement = 'chat' ) {
	$base = trailingslashit( famical_mod( 'famical_url' ) ) . 'design';
	return add_query_arg(
		array(
			'utm_source'   => 'startfa.com',
			'utm_medium'   => 'referral',
			'utm_campaign' => 'buddy',
			'utm_content'  => sanitize_key( $placement ),
		),
		$base
	);
}

/**
 * คำถามยอดฮิต + คำตอบสั้นๆ ของจิ๋วแจ๋ว (ข้อมูลทั่วไป ไม่ใช่คำแนะนำเฉพาะบุคคล)
 */
function famical_buddy_faq() {
	$faq = array(
		array(
			'q' => __( 'ควรมีประกันชีวิตทุนเท่าไหร่?', 'famical' ),
			'a' => __( 'หลักทั่วไปคือ 5–10 เท่าของรายได้ต่อปี บวกหนี้ที่ยังค้าง (บ้าน/รถ) เพื่อให้ครอบครัวอยู่ต่อได้ถ้าเกิดอะไรขึ้นค่ะ แต่ทุนที่ "พอดีงบ" ของแต่ละคนไม่เท่ากัน — ให้จิ๋วแจ๋วคำนวณจากอายุและงบจริงของคุณได้ใน 2 นาทีค่ะ', 'famical' ),
		),
		array(
			'q' => __( 'ประกันลดหย่อนภาษีได้เท่าไหร่?', 'famical' ),
			'a' => __( 'ประกันชีวิต (คุ้มครอง 10 ปีขึ้นไป) ลดได้สูงสุด 100,000 บาท · ประกันสุขภาพตัวเองสูงสุด 25,000 บาท (รวมกับประกันชีวิตไม่เกิน 100,000) · ประกันบำนาญ 15% ของเงินได้ ไม่เกิน 200,000 บาท (รวมกลุ่มเกษียณไม่เกิน 500,000) ค่ะ — ลองหน้าออกแบบประกัน จิ๋วแจ๋วบอกให้ว่าแผนที่เลือกลดหย่อนได้เท่าไหร่', 'famical' ),
		),
		array(
			'q' => __( 'ต้องตรวจสุขภาพก่อนทำประกันไหม?', 'famical' ),
			'a' => __( 'ขึ้นกับอายุและทุนประกันรวมค่ะ ทุนไม่สูงมากส่วนใหญ่ไม่ต้องตรวจ แค่ตอบคำถามสุขภาพตามจริง ถ้าทุนสูงหรืออายุมากขึ้นจะมีรายการตรวจเพิ่ม — จิ๋วแจ๋วเช็คตามเกณฑ์ของบริษัทให้ในหน้าออกแบบประกันค่ะ', 'famical' ),
		),
		array(
			'q' => __( 'ยูนิตลิงค์ ต่างจากประกันตลอดชีพยังไง?', 'famical' ),
			'a' => __( 'ตลอดชีพ: เบี้ยและทุนคงที่ตามสัญญา เรียบง่าย · ยูนิตลิงค์: ได้ทั้งความคุ้มครองและเงินลงทุนในกองทุน ปรับทุน/เบี้ยได้ยืดหยุ่น แต่ผลตอบแทนไม่การันตีและมีความเสี่ยงจากการลงทุนค่ะ จิ๋วแจ๋วแนะนำทุนประมาณ 50–70% ของทุนสูงสุด ให้เหลือเงินลงทุนเติบโต', 'famical' ),
		),
	);
	return apply_filters( 'famical_buddy_faq', $faq );
}

function famical_buddy_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'famical_buddy',
		array(
			'title'       => __( 'ปุ่มแชทจิ๋วแจ๋ว', 'famical' ),
			'panel'       => 'famical_panel',
			'description' => __( 'ปุ่ม "ถามจิ๋วแจ๋ว" มุมขวาล่างทุกหน้า — ถ้าเปิดใช้ ควรปิดปลั๊กอินแชทเดิม (GetButton) เพื่อไม่ให้ปุ่มซ้อนกัน', 'famical' ),
		)
	);
	$wp_customize->add_setting(
		'buddy_chat_enabled',
		array(
			'default'           => true,
			'sanitize_callback' => 'wp_validate_boolean',
		)
	);
	$wp_customize->add_control(
		'buddy_chat_enabled',
		array(
			'label'   => __( 'แสดงปุ่มแชทจิ๋วแจ๋ว', 'famical' ),
			'section' => 'famical_buddy',
			'type'    => 'checkbox',
		)
	);
	$wp_customize->add_setting(
		'buddy_line_url',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'buddy_line_url',
		array(
			'label'       => __( 'ลิงก์ LINE สำหรับ "คุยกับคนจริง" (เว้นว่าง = ไม่แสดงปุ่ม)', 'famical' ),
			'description' => __( 'เช่น https://lin.ee/xxxx หรือ https://line.me/R/ti/p/@xxxx', 'famical' ),
			'section'     => 'famical_buddy',
			'type'        => 'url',
		)
	);
}
add_action( 'customize_register', 'famical_buddy_customize_register', 20 );

function famical_buddy_chat_render() {
	if ( is_admin() || ! famical_buddy_enabled() ) {
		return;
	}
	$line = get_theme_mod( 'buddy_line_url', '' );
	$cat  = get_template_directory_uri() . '/assets/img/jijjaew.png';
	$cat2 = get_template_directory_uri() . '/assets/img/jijjaew-happy.png';
	?>
	<div class="buddy-chat" id="buddy-chat">
		<div class="buddy-panel" id="buddy-panel" role="dialog" aria-label="<?php esc_attr_e( 'แชทกับจิ๋วแจ๋ว', 'famical' ); ?>" hidden>
			<div class="buddy-head">
				<img class="buddy-avatar" src="<?php echo esc_url( $cat2 ); ?>" alt="" width="40" height="40" />
				<span><strong><?php esc_html_e( 'จิ๋วแจ๋ว', 'famical' ); ?></strong><small><?php esc_html_e( 'ผู้ช่วยออกแบบประกันของ STARTFA', 'famical' ); ?></small></span>
				<button type="button" class="buddy-close" data-buddy-close aria-label="<?php esc_attr_e( 'ปิด', 'famical' ); ?>">×</button>
			</div>
			<div class="buddy-log" id="buddy-log" aria-live="polite">
				<p class="buddy-msg"><?php esc_html_e( 'สวัสดีค่ะ จิ๋วแจ๋วช่วยออกแบบประกันให้พอดีงบได้ใน 2 นาที ไม่ต้องสมัครสมาชิก หรือเลือกคำถามด้านล่างได้เลยค่ะ', 'famical' ); ?></p>
			</div>
			<div class="buddy-chips">
				<?php foreach ( famical_buddy_faq() as $i => $item ) : ?>
					<button type="button" class="buddy-chip" data-buddy-q="<?php echo (int) $i; ?>"><?php echo esc_html( $item['q'] ); ?></button>
					<template id="buddy-a-<?php echo (int) $i; ?>"><?php echo esc_html( $item['a'] ); ?></template>
				<?php endforeach; ?>
			</div>
			<div class="buddy-actions">
				<a class="btn btn-primary" href="<?php echo esc_url( famical_design_url( 'chat' ) ); ?>" target="_blank" rel="noopener">🐱 <?php esc_html_e( 'ออกแบบประกันกับจิ๋วแจ๋ว', 'famical' ); ?></a>
				<?php if ( $line ) : ?>
					<a class="btn btn-ghost" href="<?php echo esc_url( $line ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'คุยกับคนจริงทาง LINE', 'famical' ); ?></a>
				<?php endif; ?>
			</div>
			<p class="buddy-note"><?php esc_html_e( 'ข้อมูลทั่วไปเพื่อประกอบการตัดสินใจ ผลประโยชน์เป็นไปตามเงื่อนไขกรมธรรม์', 'famical' ); ?></p>
		</div>
		<p class="buddy-peek" id="buddy-peek" hidden><?php esc_html_e( 'มีคำถามเรื่องประกัน/ลดหย่อนภาษีไหมคะ? 🐾', 'famical' ); ?></p>
		<button type="button" class="buddy-fab" id="buddy-fab" aria-expanded="false" aria-controls="buddy-panel" aria-label="<?php esc_attr_e( 'ถามจิ๋วแจ๋ว', 'famical' ); ?>">
			<img src="<?php echo esc_url( $cat ); ?>" alt="" width="64" height="64" />
			<span class="buddy-fab-label"><?php esc_html_e( 'ถามจิ๋วแจ๋ว', 'famical' ); ?></span>
		</button>
	</div>
	<style id="famical-buddy-chat">
		/* ปุ่มลอยกลางขอบขวา (ระดับสายตา) · พื้นกรมท่า + ขอบทอง ให้แมวสีส้มเด่นไม่กลืน */
		.buddy-chat { position: fixed; right: 12px; top: 58%; z-index: 9990; font-family: inherit; }
		.buddy-fab { position: relative; display: flex; flex-direction: column; align-items: center; gap: 4px; border: 0; background: none; padding: 0; cursor: pointer; animation: buddy-bob 3s ease-in-out infinite; }
		.buddy-fab img { width: 64px; height: 64px; border-radius: 50%; background: var(--navy-800, #111d3d); border: 3px solid var(--amber-400, #f7c04a); box-shadow: 0 8px 22px rgba(15, 26, 51, .35); image-rendering: pixelated; object-fit: cover; }
		.buddy-fab-label { background: #fff; color: var(--navy-900, #0b1530); font: 700 12px/1 inherit; padding: 5px 9px; border-radius: 999px; box-shadow: 0 3px 10px rgba(15, 26, 51, .2); white-space: nowrap; }
		.buddy-fab::after { content: ""; position: absolute; top: 2px; right: 2px; width: 12px; height: 12px; border-radius: 50%; background: #ff4d4f; border: 2px solid #fff; }
		.buddy-chat.is-seen .buddy-fab::after { display: none; }
		.buddy-chat.is-open .buddy-fab, .buddy-chat.is-open .buddy-peek { display: none; }
		.buddy-peek { position: absolute; right: 78px; top: 8px; width: max-content; max-width: 190px; margin: 0; background: #fff; color: var(--ink, #0f1a33); padding: 9px 12px; border-radius: 14px 14px 4px 14px; font-size: 13px; line-height: 1.45; box-shadow: 0 8px 22px rgba(15, 26, 51, .22); cursor: pointer; animation: buddy-pop .35s ease-out; }
		.buddy-peek[hidden] { display: none; }
		@keyframes buddy-bob { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }
		@keyframes buddy-pop { from { opacity: 0; transform: scale(.85); } to { opacity: 1; transform: scale(1); } }
		@media (prefers-reduced-motion: reduce) { .buddy-fab, .buddy-peek { animation: none; } }
		.buddy-chat.is-open { top: auto; bottom: 16px; }
		.buddy-panel { position: relative; width: min(360px, calc(100vw - 32px)); max-height: min(560px, calc(100vh - 100px)); display: flex; flex-direction: column; background: #fff; color: var(--ink, #0f1a33); border-radius: 16px; box-shadow: 0 16px 40px rgba(15, 26, 51, .25); overflow: hidden; }
		.buddy-panel[hidden] { display: none; }
		.buddy-head { display: flex; align-items: center; gap: 10px; padding: 12px 14px; background: var(--navy-800, #111d3d); color: #fff; }
		.buddy-head small { display: block; opacity: .75; font-size: 12px; }
		.buddy-avatar { width: 40px; height: 40px; border-radius: 50%; background: #fff; image-rendering: pixelated; }
		.buddy-close { margin-left: auto; background: none; border: 0; color: #fff; font-size: 24px; cursor: pointer; line-height: 1; }
		.buddy-log { padding: 12px 14px; overflow-y: auto; flex: 1; display: flex; flex-direction: column; gap: 8px; }
		.buddy-msg, .buddy-me { margin: 0; padding: 9px 12px; border-radius: 12px; font-size: 14px; line-height: 1.55; max-width: 90%; }
		.buddy-msg { background: var(--bg-soft, #f4f7fc); align-self: flex-start; }
		.buddy-me { background: var(--blue-600, #1e4fa3); color: #fff; align-self: flex-end; }
		.buddy-chips { display: flex; flex-wrap: wrap; gap: 6px; padding: 0 14px 10px; }
		.buddy-chip { border: 1px solid var(--line, #e3e8f2); background: #fff; border-radius: 999px; padding: 6px 10px; font-size: 13px; cursor: pointer; color: inherit; }
		.buddy-chip:hover { border-color: var(--blue-500, #2a66c9); }
		.buddy-actions { display: flex; flex-direction: column; gap: 6px; padding: 0 14px 8px; }
		.buddy-actions .btn { justify-content: center; text-align: center; }
		.buddy-actions .btn-ghost { background: #06c755; color: #fff; border-color: #06c755; }
		.buddy-note { margin: 0; padding: 0 14px 10px; font-size: 11px; color: var(--ink-soft, #4a5672); }
		@media (max-width: 480px) { .buddy-chat { right: 8px; } .buddy-fab img { width: 56px; height: 56px; } .buddy-chat.is-open { right: 12px; bottom: 12px; } }
		@media print { .buddy-chat { display: none; } }
	</style>
	<script>
	( function () {
		var root = document.getElementById( 'buddy-chat' ), fab = document.getElementById( 'buddy-fab' ), panel = document.getElementById( 'buddy-panel' ), log = document.getElementById( 'buddy-log' ), peek = document.getElementById( 'buddy-peek' );
		if ( ! fab || ! panel ) { return; }
		function store( k, v ) { try { if ( v === undefined ) { return window.sessionStorage.getItem( k ); } window.sessionStorage.setItem( k, v ); } catch ( e ) { return null; } }
		if ( store( 'buddySeen' ) ) { root.classList.add( 'is-seen' ); }
		function toggle( open ) {
			panel.hidden = ! open;
			root.classList.toggle( 'is-open', open );
			fab.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			if ( open ) { peek.hidden = true; root.classList.add( 'is-seen' ); store( 'buddySeen', '1' ); }
		}
		// ทักครั้งเดียวต่อการเข้าเว็บ: หลังอ่านไป 8 วินาที หรือเลื่อนลงเกิน 35% ของหน้า
		var peeked = !! store( 'buddyPeeked' );
		function showPeek() {
			if ( peeked || ! panel.hidden ) { return; }
			peeked = true; store( 'buddyPeeked', '1' ); peek.hidden = false;
			setTimeout( function () { peek.hidden = true; }, 9000 );
		}
		setTimeout( showPeek, 8000 );
		window.addEventListener( 'scroll', function () {
			var h = document.documentElement.scrollHeight - window.innerHeight;
			if ( h > 0 && window.scrollY / h > 0.35 ) { showPeek(); }
		}, { passive: true } );
		peek.addEventListener( 'click', function () { toggle( true ); } );
		fab.addEventListener( 'click', function () { toggle( panel.hidden ); } );
		panel.querySelector( '[data-buddy-close]' ).addEventListener( 'click', function () { toggle( false ); } );
		document.addEventListener( 'keydown', function ( e ) { if ( 'Escape' === e.key ) { toggle( false ); } } );
		function bubble( text, cls ) {
			var p = document.createElement( 'p' );
			p.className = cls;
			p.textContent = text;
			log.appendChild( p );
			log.scrollTop = log.scrollHeight;
		}
		panel.querySelectorAll( '[data-buddy-q]' ).forEach( function ( chip ) {
			chip.addEventListener( 'click', function () {
				var tpl = document.getElementById( 'buddy-a-' + chip.getAttribute( 'data-buddy-q' ) );
				bubble( chip.textContent, 'buddy-me' );
				if ( tpl ) { bubble( tpl.content.textContent, 'buddy-msg' ); }
			} );
		} );
	} )();
	</script>
	<?php
}
add_action( 'wp_footer', 'famical_buddy_chat_render', 50 );
