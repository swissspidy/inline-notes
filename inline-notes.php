<?php
/**
 * Inline Notes
 *
 * Add elegant footnotes that expand directly in the content.
 *
 * @package   Voice_Search
 * @author    Pascal Birchler <pascal.birchler@spinpress.com>
 * @license   GPL-2.0+
 * @link      https://pascalbirchler.com
 * @copyright 2014 Pascal Birchler
 *
 * @wordpress-plugin
 * Plugin Name:       Inline Notes
 * Plugin URI:        https://spinpress.com/wordpress-web-speech-api/
 * Description:       Add elegant footnotes that expand directly in the content. Lightweight and print-friendly.
 * Version:           1.0.0
 * Author:            Pascal Birchler
 * Author URI:        https://pascalbirchler.com
 * Text Domain:       inline-notes
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// Don't call this file directly
defined( 'ABSPATH' ) or die;

/**
 * Class Inline_Notes
 */
final class Inline_Notes {

	/**
	 * @var array Footnotes Storage.
	 */
	static $footnotes = array();

	/**
	 * Add all hooks on init
	 */
	public static function init() {
		// Add the shortcode
		add_shortcode( 'note', array( __CLASS__, 'shortcode' ) );

		// Add high-priority hook to clear footnotes array
		add_filter( 'the_content', array( __CLASS__, 'clear_footnotes' ), 1 );

		// Add the footnotes at the end of the post
		add_filter( 'the_content', array( __CLASS__, 'the_content' ), 12 );

		// Load the stylesheet
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'add_styles' ), 15 );
	}

	/**
	 * Clear footnotes add the beginning of a new post.
	 *
	 * @param $content
	 *
	 * @return mixed
	 */
	public static function clear_footnotes( $content ) {
		self::$footnotes = array();

		return $content;
	}

	/**
	 * Render the shortcode.
	 *
	 * @param array  $atts    Shortcode attributes.
	 * @param string $content The encapsuled text.
	 *
	 * @return string
	 */
	public static function shortcode( $atts, $content = '' ) {
		global $post;

		$atts = shortcode_atts( array(
			'text' => '',
		), $atts, 'note' );

		$note = esc_html( $atts['text'] );

		// Enqueue the necessary stylesheet
		wp_enqueue_style( 'inline-notes' );

		self::$footnotes[] = array(
			'content' => $content,
			'note'    => $note,
		);

		$count   = count( self::$footnotes );
		$note_id = $post->ID . '-' . $count;

		if ( is_feed() ) {
			$content = '<span class="inline-note"><span class="inline-note__content">' . $content . '<sup>' . $count . '</sup></a></span>';
		} else {
			$content = '<span class="inline-note"><a onclick="this.classList.toggle(\'inline-note--active\');" class="inline-note__content">' . $content . '<sup>' . $count . '</sup></a>' .
			           '<span class="inline-note__explanation">' . $note . '</span></span>';
		}

		return $content;
	}

	/**
	 * Filter the content to append footnotes.
	 *
	 * These are only displayed when printing out the page.
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public static function the_content( $content ) {
		global $post;

		if ( empty( self::$footnotes ) ) {
			return $content;
		}

		$class = 'inline-notes';

		if ( is_feed() ) {
			$class .= ' inline-notes--feed';
		}

		// Append footnotes to content
		$content .= '<div class="' . $class . '">';
		$content .= '<h6 class="inline-notes__heading">' . __( 'Notes:', 'inline-notes' ) . '</h6><ol class="inline-notes__list">';

		$i = 1;

		// Loop through footnotes and format output
		foreach ( self::$footnotes as $note ) {
			$content .= '<li id="note' . $post->ID . '-' . $i . '">' . $note['note'] . '</li>';

			$i ++;
		}

		//close tags
		$content .= '</ol></div>';

		return $content;
	}

	/**
	 * Enqueue stylesheets
	 */
	public static function add_styles() {
		// Use minified CSS if SCRIPT_DEBUG is turned off
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_register_style(
			'inline-notes',
			plugins_url( '', __FILE__ ) . '/css/build/inline-notes' . $suffix . '.css',
			array(),
			'1.0.0'
		);
	}

}

add_action( 'plugins_loaded', array( 'Inline_Notes', 'init' ) );