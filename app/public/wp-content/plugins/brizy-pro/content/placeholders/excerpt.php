<?php

class BrizyPro_Content_Placeholders_Excerpt extends Brizy_Content_Placeholders_Simple {

	/**
	 * Brizy_Editor_Content_GenericPlaceHolder constructor.
	 *
	 * @param string $label
	 * @param string $placeholder
	 */
	public function __construct( $label, $placeholder ) {
		parent::__construct( $label, $placeholder, function ( Brizy_Content_Context $context ) {
			return $this->get_the_excerpt( $context->getWpPost() );
		} );
	}

	/**
	 * It rewrites the function from wodpress core get_the_excerpt that applies the hook get_the_excerpt.
	 * The hook get_the_excerpt has a handle wp_trim_excerpt that applies the hook the_content.
	 * Applying the hook the_content will run an infinite loop because of some function like
	 * Brizy_Admin_Templates->filterPageContent() which are also hanging at this hook.
	 *
	 * @param WP_Post $post
	 *
	 * @return false|mixed|string
	 */
	public function get_the_excerpt( $post ) {

		if ( post_password_required( $post ) ) {
			return __( 'There is no excerpt because this is a protected post.', 'brizy' );
		}

		return $this->wp_trim_excerpt( $post->post_excerpt, $post );
	}

	/**
	 * It rewrite the wodpress function wp_trim_excerpt.
	 * The only thing we do is exclude the appling of the hook the_content.
	 * Further information read the description of the function getValue of this class.
	 *
	 * @param string $text
	 * @param null $post
	 *
	 * @return string
	 */
	private function wp_trim_excerpt( $text = '', $post = null ) {
		$raw_excerpt = $text;
		if ( '' == $text ) {
			$post = get_post( $post );
			$text = get_the_content( '', false, $post );

			$text = strip_shortcodes( $text );
			$text = excerpt_remove_blocks( $text );

			/** This filter is documented in wp-includes/post-template.php */
			//$text = apply_filters( 'the_content', $text );
			$text = str_replace( ']]>', ']]&gt;', $text );

			/**
			 * Filters the number of words in an excerpt.
			 *
			 * @since 2.7.0
			 *
			 * @param int $number The number of words. Default 55.
			 */
			$excerpt_length = apply_filters( 'excerpt_length', 55 );
			/**
			 * Filters the string in the "more" link displayed after a trimmed excerpt.
			 *
			 * @since 2.9.0
			 *
			 * @param string $more_string The string shown within the more link.
			 */
			$excerpt_more = apply_filters( 'excerpt_more', ' ' . '[&hellip;]' );
			$text         = wp_trim_words( $text, $excerpt_length, $excerpt_more );
		}
		/**
		 * Filters the trimmed excerpt string.
		 *
		 * @since 2.8.0
		 *
		 * @param string $text        The trimmed text.
		 * @param string $raw_excerpt The text prior to trimming.
		 */
		return apply_filters( 'wp_trim_excerpt', $text, $raw_excerpt );
	}
}