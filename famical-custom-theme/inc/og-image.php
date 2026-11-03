<?php
/**
 * Share image (og:image) for posts and pages: the post's own picture first, the site default only as a
 * fallback (user, 2026-11-03: LINE/Facebook showed the site-wide image instead of the article's).
 *   1. featured image (ภาพประจำเรื่อง)
 *   2. the first image inside the content
 *   3. nothing -> whatever the SEO plugin / site default prints stays as it is
 *
 * v1.7.0: plugin filters alone were not enough (the live site kept the default image on LINE and Facebook
 * showed a grey box), so the theme now rewrites the finished <head>: every og:image / twitter:image tag
 * printed by ANY plugin (Rank Math, Yoast, AIOSEO, Jetpack, site-wide header code ...) is replaced by one
 * set for the post's picture, with its real width/height, at a size social apps accept, and with a
 * percent-encoded URL (Thai file names otherwise break some apps' fetchers).
 * Page caches keep the old <head>: purge the cache, then re-scrape (Facebook Sharing Debugger).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Percent-encode non-ASCII characters / spaces in a URL path (keeps already-encoded parts as they are).
 */
function famical_og_encode_url( $url ) {
	$parts = wp_parse_url( $url );
	if ( empty( $parts['host'] ) ) {
		return $url;
	}
	$path = isset( $parts['path'] ) ? $parts['path'] : '';
	$path = implode( '/', array_map( function ( $seg ) {
		return rawurlencode( rawurldecode( $seg ) );
	}, explode( '/', $path ) ) );
	$out = ( isset( $parts['scheme'] ) ? $parts['scheme'] : 'https' ) . '://' . $parts['host'] . ( isset( $parts['port'] ) ? ':' . $parts['port'] : '' ) . $path;
	if ( isset( $parts['query'] ) ) {
		$out .= '?' . $parts['query'];
	}
	return $out;
}

/**
 * Pick an attachment size social apps load reliably (very large originals often show as a grey box on
 * Facebook): the original if it is at most 2000px wide, otherwise the 1536 / large size.
 *
 * @return array{0:string,1:int,2:int}|null
 */
function famical_og_attachment( $attachment_id ) {
	foreach ( array( 'full', '1536x1536', 'large' ) as $size ) {
		$src = wp_get_attachment_image_src( $attachment_id, $size );
		if ( ! empty( $src[0] ) && ( 'large' === $size || (int) $src[1] <= 2000 ) ) {
			return array( $src[0], (int) $src[1], (int) $src[2] );
		}
	}
	return null;
}

/**
 * @return array{url:string,width:int,height:int}|null
 */
function famical_og_post_image_data( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return null;
	}
	if ( has_post_thumbnail( $post ) ) {
		$img = famical_og_attachment( get_post_thumbnail_id( $post ) );
		if ( $img ) {
			return array( 'url' => $img[0], 'width' => $img[1], 'height' => $img[2] );
		}
	}
	if ( preg_match( '/<img[^>]+src=["\']([^"\']+)["\']/i', (string) $post->post_content, $m ) ) {
		$url = html_entity_decode( $m[1] );
		if ( 0 === strpos( $url, 'data:' ) ) {
			return null;
		}
		if ( 0 === strpos( $url, '//' ) ) {
			$url = 'https:' . $url;
		} elseif ( 0 === strpos( $url, '/' ) ) {
			$url = home_url( $url );
		}
		// image from the media library (also when the content uses a resized copy like -1024x576.jpg)
		$id = attachment_url_to_postid( preg_replace( '/-\d+x\d+(\.\w+)$/', '$1', $url ) );
		if ( $id ) {
			$img = famical_og_attachment( $id );
			if ( $img ) {
				return array( 'url' => $img[0], 'width' => $img[1], 'height' => $img[2] );
			}
		}
		return array( 'url' => $url, 'width' => 0, 'height' => 0 );
	}
	return null;
}

/** Back-compat helper: the image URL only. */
function famical_og_post_image( $post = null ) {
	$data = famical_og_post_image_data( $post );
	return $data ? $data['url'] : '';
}

function famical_og_current_image_data() {
	static $cache = false;
	if ( false === $cache ) {
		$cache = is_singular() ? famical_og_post_image_data( get_queried_object_id() ) : null;
	}
	return $cache;
}

function famical_og_tags( $img ) {
	$url  = esc_url( famical_og_encode_url( $img['url'] ) );
	$out  = '<meta property="og:image" content="' . $url . '" />' . "\n";
	$out .= '<meta property="og:image:secure_url" content="' . $url . '" />' . "\n";
	if ( $img['width'] && $img['height'] ) {
		$out .= '<meta property="og:image:width" content="' . (int) $img['width'] . '" />' . "\n";
		$out .= '<meta property="og:image:height" content="' . (int) $img['height'] . '" />' . "\n";
	}
	$out .= '<meta property="og:image:alt" content="' . esc_attr( wp_strip_all_tags( get_the_title( get_queried_object_id() ) ) ) . '" />' . "\n";
	$out .= '<meta name="twitter:image" content="' . $url . '" />' . "\n";
	return $out;
}

/**
 * Replace every image tag the plugins printed with ours (placed where the first one was).
 */
function famical_og_rewrite_head( $html, $img ) {
	$pattern = '/<meta\s[^>]*(?:property|name)=["\'](?:og:image(?::[a-z_]+)?|twitter:image(?::[a-z_]+)?)["\'][^>]*>\s*/i';
	$first   = true;
	$ours    = famical_og_tags( $img );
	$html    = preg_replace_callback(
		$pattern,
		function () use ( &$first, $ours ) {
			if ( $first ) {
				$first = false;
				return $ours;
			}
			return '';
		},
		$html
	);
	if ( $first ) {
		$html .= "\n" . $ours; // nobody printed an image tag: add ours at the end of <head>
	}
	if ( false === stripos( $html, 'twitter:card' ) ) {
		$html .= '<meta name="twitter:card" content="summary_large_image" />' . "\n";
	}
	return $html;
}

add_action( 'wp_head', function () {
	if ( famical_og_current_image_data() ) {
		$GLOBALS['famical_og_buffering'] = ob_start();
	}
}, PHP_INT_MIN );

add_action( 'wp_head', function () {
	if ( empty( $GLOBALS['famical_og_buffering'] ) ) {
		return;
	}
	$GLOBALS['famical_og_buffering'] = false;
	$html = ob_get_clean();
	echo famical_og_rewrite_head( (string) $html, famical_og_current_image_data() ); // phpcs:ignore WordPress.Security.EscapeOutput -- head markup from WP/plugins + our escaped tags
}, PHP_INT_MAX );

// No SEO plugin: print the basic Open Graph tags ourselves (posts/pages only); the image tags are added by
// the rewrite above.
add_action( 'wp_head', function () {
	if ( defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) ) {
		return;
	}
	if ( ! is_singular() ) {
		return;
	}
	$id    = get_queried_object_id();
	$title = wp_strip_all_tags( get_the_title( $id ) );
	$desc  = wp_strip_all_tags( get_the_excerpt( $id ) );
	echo "\n" . '<meta property="og:type" content="article" />' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '" />' . "\n";
	echo '<meta property="og:url" content="' . esc_url( get_permalink( $id ) ) . '" />' . "\n";
	if ( $desc ) {
		echo '<meta property="og:description" content="' . esc_attr( wp_trim_words( $desc, 40 ) ) . '" />' . "\n";
	}
}, 5 );
