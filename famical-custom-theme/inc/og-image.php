<?php
/**
 * Share image (og:image) for posts and pages: the post's own picture first, the site default only as a
 * fallback (user, 2026-11-03: LINE/Facebook showed the site-wide image instead of the article's).
 *   1. featured image (ภาพประจำเรื่อง)
 *   2. the first image inside the content
 *   3. nothing -> the SEO plugin / site default stays as it is
 * Works with Yoast SEO or Rank Math (their image filters), or outputs its own Open Graph tags when no SEO
 * plugin is active. Social apps cache previews: re-scrape once (Facebook Sharing Debugger) after updating.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function famical_og_post_image( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}
	if ( has_post_thumbnail( $post ) ) {
		$src = wp_get_attachment_image_src( get_post_thumbnail_id( $post ), 'full' );
		if ( ! empty( $src[0] ) ) {
			return $src[0];
		}
	}
	if ( preg_match( '/<img[^>]+src=["\']([^"\']+)["\']/i', (string) $post->post_content, $m ) ) {
		$url = $m[1];
		// make relative URLs absolute; skip data: URIs and tiny tracking pixels
		if ( 0 === strpos( $url, '/' ) && 0 !== strpos( $url, '//' ) ) {
			$url = home_url( $url );
		}
		if ( 0 !== strpos( $url, 'data:' ) ) {
			return esc_url_raw( $url );
		}
	}
	return '';
}

function famical_og_current_image() {
	return is_singular() ? famical_og_post_image( get_queried_object_id() ) : '';
}

// Yoast SEO
add_filter( 'wpseo_opengraph_image', function ( $img ) {
	$own = famical_og_current_image();
	return $own ? $own : $img;
} );
add_filter( 'wpseo_twitter_image', function ( $img ) {
	$own = famical_og_current_image();
	return $own ? $own : $img;
} );

// Rank Math
add_filter( 'rank_math/opengraph/facebook/image', function ( $img ) {
	$own = famical_og_current_image();
	return $own ? $own : $img;
} );
add_filter( 'rank_math/opengraph/twitter/image', function ( $img ) {
	$own = famical_og_current_image();
	return $own ? $own : $img;
} );

// No SEO plugin: print the basic Open Graph tags ourselves (posts/pages only).
add_action( 'wp_head', function () {
	if ( defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) ) {
		return;
	}
	if ( ! is_singular() ) {
		return;
	}
	$id    = get_queried_object_id();
	$img   = famical_og_post_image( $id );
	$title = wp_strip_all_tags( get_the_title( $id ) );
	$desc  = wp_strip_all_tags( get_the_excerpt( $id ) );
	echo "\n" . '<meta property="og:type" content="article" />' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '" />' . "\n";
	echo '<meta property="og:url" content="' . esc_url( get_permalink( $id ) ) . '" />' . "\n";
	if ( $desc ) {
		echo '<meta property="og:description" content="' . esc_attr( wp_trim_words( $desc, 40 ) ) . '" />' . "\n";
	}
	if ( $img ) {
		echo '<meta property="og:image" content="' . esc_url( $img ) . '" />' . "\n";
		echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
		echo '<meta name="twitter:image" content="' . esc_url( $img ) . '" />' . "\n";
	}
}, 5 );
