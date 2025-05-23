<?php namespace RealTimeAutoFindReplace\admin\options\functions;

/**
 * Class: Item List
 *
 * @package Admin
 * @since 1.0.0
 * @author M.Tuhin <info@codesolz.net>
 */

if ( ! defined( 'CS_RTAFAR_VERSION' ) ) {
	die();
}

use RealTimeAutoFindReplace\lib\Util;
use RealTimeAutoFindReplace\admin\functions\Masking;
use RealTimeAutoFindReplace\admin\options\pages\AdvScreenOptions\ScreenOptions;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class AllMaskingRulesList extends \WP_List_Table {
	var $item_per_page;
	var $total_post;

	public function __construct() {
		parent::__construct(
			array(
				'singular' => __( 'find_replace', 'real-time-auto-find-and-replace' ),
				'plural'   => __( 'finds_replaces', 'real-time-auto-find-and-replace' ),
				'ajax'     => false,
			)
		);

		$per_page            = ScreenOptions::rtafar_get_amr_per_page();
		$this->item_per_page = empty( $per_page ) ? 10 : $per_page;
	}

	/**
	 *
	 * @return typeGenerate column
	 */
	public function get_columns() {
		return apply_filters(
			'bfrp_all_masking_rules_tbl_rows',
			array(
				'cb'               => '<input type="checkbox" />',
				'find'             => __( 'Find', 'real-time-auto-find-and-replace' ),
				'replace'          => __( 'Replace by', 'real-time-auto-find-and-replace' ),
				'type'             => __( 'Rule Type', 'real-time-auto-find-and-replace' ),
				'where_to_replace' => __( 'Where to replace', 'real-time-auto-find-and-replace' ),
			)
		);
	}

	/**
	 * Column default info
	 */
	function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'find':
			case 'replace':
			case 'type':
			case 'where_to_replace':
				return $item->{$column_name};
			default:
				return '---'; // Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * Column cb
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="id[]" value="%1$s" />', $item->id );
	}

	/**
	 * Column Find
	 *
	 * @param [type] $item
	 * @return void
	 */
	public function column_find( $item ) {

		echo Util::cs_sanitize_prnt_str( $item->find );
		$edit_link = admin_url( "admin.php?page=cs-add-replacement-rule&action=update&rule_id={$item->id}" );

		if ( has_action( 'bfrp_column_find' ) ) {
			do_action( 'bfrp_column_find', $item );
		}

		echo '<div class="row-actions"><span class="edit">';
		echo '<a href="' . \esc_url( $edit_link ) . '">Edit</a>';
		echo '</span></div>';
	}

	public function column_replace( $item ) {
		echo Util::cs_sanitize_prnt_str( $item->replace );
	}

	public function column_type( $item ) {
		if ( $item->type == 'plain' ) {
			return __( 'Plain Text', 'real-time-auto-find-and-replace' );
		} elseif ( $item->type == 'regex' ) {
			return sprintf(
				/* translators: %1$s: opening <span> tag with a class, %2$s: closing </span> tag */
				__( 'Regular Expression %1$s Managed %2$s ', 'real-time-auto-find-and-replace' ),
				'<br><span class="dt-col-sm-des">',
				'</span>'
			);
		} elseif ( $item->type == 'ajaxContent' ) {
			/* translators: %s: HTML markup and text, including delay time and seconds */
			return sprintf( __( 'jQuery / Ajax %s', 'real-time-auto-find-and-replace' ), '<br> <span class="dt-col-sm-des"> Delay Time : ' . $item->delay . ' seconds</span>' );

		} elseif ( $item->type == 'advance_regex' ) {
			return sprintf(
				/* translators: %1$s: opening <span> tag with a class, %2$s: closing </span> tag */
				__( 'Advance Regular Expression %1$s (multiple lines at once / code blocks) %2$s ', 'real-time-auto-find-and-replace' ),
				'<span class="dt-col-sm-des">',
				'</span>'
			);
		} elseif ( $item->type == 'regexCustom' ) {
			return sprintf(
				/* translators: %1$s: opening <span> tag with a class, %2$s: closing </span> tag */
				__( 'Regular Expression %1$s Custom %2$s ', 'real-time-auto-find-and-replace' ),
				'<br><span class="dt-col-sm-des">',
				'</span>'
			);
		} elseif ( $item->type == 'multiByte' ) {
			/* translators: %s: HTML markup with encoding information */
			return sprintf( __( 'MultiByte %s', 'real-time-auto-find-and-replace' ), '<br> <span class="dt-col-sm-des"> Encoding : ' . $item->html_charset . '</span>' );
		} elseif ( has_filter( 'bfrp_column_type_text' ) ) {
				return apply_filters( 'bfrp_column_type_text', $item );
		}
	}

	public function column_where_to_replace( $item ) {
		if ( strtolower( $item->where_to_replace ) == 'all' ) {
			return __( 'All over the website', 'real-time-auto-find-and-replace' );
		} elseif ( has_filter( 'bfrp_where_to_replace' ) ) {
				return apply_filters( 'bfrp_where_to_replace', $item );
		}
	}

	public function column_skip_pages( $item ) {
		if ( has_action( 'bfrp_column_skip_pages' ) ) {
			do_action( 'bfrp_column_skip_pages', $item );
		} else {
			return '---';
		}
	}

	public function column_skip_posts( $item ) {
		if ( has_action( 'bfrp_column_skip_posts' ) ) {
			do_action( 'bfrp_column_skip_posts', $item );
		} else {
			return '---';
		}
	}

	public function column_country_rules( $item ) {
		if ( has_action( 'bfrp_column_country_rules' ) ) {
			do_action( 'bfrp_column_country_rules', $item );
		} else {
			return '---';
		}
	}

	public function column_lang_rules( $item ) {
		if ( has_action( 'bfrp_column_lang_rules' ) ) {
			do_action( 'bfrp_column_lang_rules', $item );
		} else {
			return '---';
		}
	}


	public function no_items() {
		esc_html_e( 'Sorry! No Rule Found!', 'real-time-auto-find-and-replace' );
	}

	function get_views() {
		$all_link     = admin_url( 'admin.php?page=cs-all-masking-rules' );
		$views['all'] = "<a href='{$all_link}' >All <span class='count'>({$this->total_post})</span></a>";
		return $views;
	}

	public function get_bulk_actions() {
		$actions = array(
			'delete' => __( 'Delete', 'real-time-auto-find-and-replace' ),
		);
		return $actions;
	}

	/**
	 * Extra table nav
	 */
	public function extra_tablenav( $which ) {
		do_action( 'rtafar_allmaskingrules_extra_tablenav' );
	}

	/**
	 * Get the data
	 *
	 * @global type $wpdb
	 * @return type
	 */
	private function poulate_the_data() {
		global $wpdb, $wapg_tables;
		$search = '';
		if ( isset( $_GET['s'] ) && ! empty( $skey = $_GET['s'] ) ) {
			$skey   = Util::cs_esc_sql( $skey );
			$search = $wpdb->prepare( ' where c.find like %s ', 'bfarPercent' . $skey . 'bfarPercent' );
			$search = \str_replace( 'bfarPercent', '%', $search );
		}

		if ( isset( $_GET['order'] ) && ! empty( $order = $_GET['order'] ) ) {
			$order = Util::cs_sanitize_sql_orderby( $order );
		} else {
			$order = 'c.id DESC';
		}

		$current_page = $this->get_pagenum();
		if ( 1 < $current_page ) {
				$offset = $this->item_per_page * ( $current_page - 1 );
		} else {
				$offset = 0;
		}

		$data   = array();
		$result = $wpdb->get_results(
			"SELECT * from {$wpdb->prefix}rtafar_rules as c "
				. "$search "
				. " order by {$order} limit $this->item_per_page offset {$offset}"
		);

		if ( $result ) {
			foreach ( $result as $item ) {
				$data[] = $item;
			}
		}
		$total         = $wpdb->get_var( "select count(id) as total from {$wpdb->prefix}rtafar_rules as c {$search} " );
		$data['count'] = $this->total_post = $total;

		return $data;
	}

	function process_bulk_action() {
		global $wpdb, $wapg_tables;
			// security check!
		if ( isset( $_GET['_wpnonce'] ) && ! empty( $_GET['_wpnonce'] ) ) {

			$action = 'bulk-' . $this->_args['plural'];

			if ( ! wp_verify_nonce( $_GET['_wpnonce'], $action ) ) {
				wp_die( 'Nope! Security check failed!' );
			}

			$action = $this->current_action();

			switch ( $action ) :
				case 'delete':
					$log_ids = $_GET['id'];
					if ( $log_ids ) {
						foreach ( $log_ids as $log ) {
							$wpdb->delete( "{$wpdb->prefix}rtafar_rules", array( 'id' => $log ) );
						}
					}
					$this->success_admin_notice();
					break;
			endswitch;
		}
		return;
	}

	public function success_admin_notice() {
		?>
		<div class="updated">
			<p><?php esc_html_e( 'Rule has been deleted successfully!', 'real-time-auto-find-and-replace' ); ?></p>
		</div>
		<?php
	}

	public function prepare_items() {
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		// Column headers
		$this->_column_headers = array( $columns, $hidden, $sortable = '' );
		$this->process_bulk_action();

		$data  = $this->poulate_the_data();
		$count = $data['count'];
		unset( $data['count'] );
		$this->items = $data;

		// Set the pagination
		$this->set_pagination_args(
			array(
				'total_items' => $count,
				'per_page'    => $this->item_per_page,
				'total_pages' => ceil( $count / $this->item_per_page ),
			)
		);
	}
}
