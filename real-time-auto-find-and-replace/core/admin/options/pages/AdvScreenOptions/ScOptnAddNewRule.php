<?php namespace RealTimeAutoFindReplace\admin\options\pages\AdvScreenOptions;

/**
 * Class: Screen options
 *
 * @package Options
 * @since 1.3.8
 * @author M.Tuhin <info@codesolz.net>
 */

if ( ! defined( 'CS_RTAFAR_VERSION' ) ) {
	die();
}


class ScOptnAddNewRule {

	/**
	 * screen option
	 *
	 * @return void
	 */
	public static function rtafar_arr_screen_options() {
		$screen = \get_current_screen();
		if ( self::help_tabs() ) {
			foreach ( self::help_tabs() as $tab ) {
				$tab = (object) $tab;
				$screen->add_help_tab(
					array(
						'id'       => $tab->id,
						'title'    => $tab->title,
						'content'  => $tab->content,
						'callback' => $tab->callback,
						'priority' => $tab->priority,
					)
				);
			}
		}
		$screen->set_help_sidebar( self::arr_help_sidebar_content() );
	}

	/**
	 * Help tabs
	 *
	 * @return void
	 */
	public static function help_tabs() {
		return array(
			array(
				'id'       => 'overview',
				'title'    => esc_html__( 'Overview', 'real-time-auto-find-and-replace' ),
				'content'  => '',
				'callback' => array( __CLASS__, 'arr_overview' ),
				'priority' => 1,
			),
			array(
				'id'       => 'available_features',
				'title'    => esc_html__( 'Available Features', 'real-time-auto-find-and-replace' ),
				'content'  => '',
				'callback' => array( __CLASS__, 'arr_available_features' ),
				'priority' => 1,
			),
		);
	}

	/**
	 * Overview
	 *
	 * @return void
	 */
	public static function arr_overview() {
		\printf(
			esc_html__( '%1$s This screen provides the  the functionalities to add new rule. You can add real-time rules as well as some specific rules in Database. After installing the pro version, these muted pro features will be activated automatically. %2$s', 'real-time-auto-find-and-replace' ),
			'<p>',
			'</p>'
		);
	}


	/**
	 * available Features
	 *
	 * @return void
	 */
	public static function arr_available_features() {
		?>
			<p>
				<?php esc_html_e( 'You can perform the following Features:', 'real-time-auto-find-and-replace' ); ?>
			</p>
			<ul>
				<li>
					<?php printf( esc_html__( '%1$s Rule\'s Type %2$s allows to specify the type of a search and replacement rule. You can add real-time rule or rule for database. Real-time (masking) rules will take places before your website renders to the browser. It it will not effect anything on database.', 'real-time-auto-find-and-replace' ), '<strong>', '</strong>' ); ?>
				</li>
				<li>
					<?php printf( esc_html__( '%1$s Where to Replace %2$s allows to specify where you want to apply the rules. You need pro version to avail the muted features.', 'real-time-auto-find-and-replace' ), '<strong>', '</strong>' ); ?>
				</li>
			</ul>
			
			<p>
				<?php printf( esc_html__( '%1$s Tutorial %2$s : To read more about the features,  %3$scheck plugin\'s documentation%4$s from our website', 'real-time-auto-find-and-replace' ), '<strong>', '</strong>', '<a href="https://docs.codesolz.net/better-find-and-replace/" target="_blank">', '</a>' ); ?>
			</p>
		<?php
	}

	/**
	 * Help Sidebar Content
	 *
	 * @return void
	 */
	public static function arr_help_sidebar_content() {
		ob_start();
		?>
			<p><strong><?php esc_html_e( 'For more information: ', 'real-time-auto-find-and-replace' ); ?></strong></p>
			<p>
				<?php esc_html_e( 'Looking for features details? Check plugin\'s ', 'real-time-auto-find-and-replace' ); ?>
				<a href="https://docs.codesolz.net/better-find-and-replace/" target="_blank"><?php esc_html_e( 'Documentation', 'real-time-auto-find-and-replace' ); ?></a></p>
			<p><a href="https://codesolz.net/our-products/wordpress-plugin/real-time-auto-find-and-replace/" target="_blank"><?php esc_html_e( 'Support', 'real-time-auto-find-and-replace' ); ?></a></p>					
		<?php
		$html = ob_get_clean();

		return $html;
	}
}