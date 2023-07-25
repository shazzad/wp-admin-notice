<?php
namespace Shazzad\WpAdminNotice;

class Provider {

	/**
	 * User meta key to stored notices.
	 */
	const USER_META_KEY = '_swpan_notice';

	/**
	 * Notices.
	 */
	private static $notices = array();

	/**
	 * Setup the provider.
	 */
	public static function setup() {
		add_action( 'admin_notices', array( __CLASS__, 'display_notices' ) );
		add_action( 'swpan_user_notice', array( __CLASS__, 'add_user_notice' ) );
		add_action( 'swpan_screen_notice', array( __CLASS__, 'add_screen_notice' ), 10, 2 );
	}

	/**
	 * Display admin notices.
	 * 
	 * @return void
	 */
	public static function display_notices() {
		// Display notices stored for the current user.
		if ( get_current_user_id() ) {
			$user_notices = get_user_meta( get_current_user_id(), static::USER_META_KEY );

			if ( ! empty( $user_notices ) ) {
				delete_user_meta( get_current_user_id(), static::USER_META_KEY );
				self::$notices = array_merge( self::$notices, $user_notices );
			}
		}

		foreach ( self::$notices as $notice ) {
			$classes = [ 
				'notice',
				'notice-' . $notice['type'],
			];

			if ( ! array_key_exists( 'dismissable', $notice ) || $notice['dismissable'] ) {
				$classes[] = 'is-dismissible';
			}
			?>
			<div id="notice-<?php echo $notice['id']; ?>" class="<?php echo join( ' ', $classes ); ?>">
				<p>
					<?php echo $notice['message']; ?>
				</p>
			</div>
			<?php
		}
	}

	/**
	 * Add a user notice.
	 * 
	 * @param array $data Notice data.
	 * 
	 * @return void
	 */
	public static function add_user_notice( $data ) {
		if ( ! get_current_user_id() ) {
			return;
		}

		// Allow to pass multiple notices at once.
		if ( isset( $data[0] ) && is_array( $data[0] ) ) {
			foreach ( $data as $notice ) {
				static::add_user_notice( $notice );
			}

			return;
		}

		// Allow to pass notice text as first parameter without key.
		if ( isset( $data[0] ) && is_string( $data[0] ) ) {
			$data['message'] = $data[0];
		}

		$notice = static::prepare_notice( $data );

		if ( false !== $notice ) {
			add_user_meta( get_current_user_id(), static::USER_META_KEY, $notice );
		}
	}

	/**
	 * Add a screen notice.
	 * 
	 * @param array $data Notice data.
	 * 
	 * @return void
	 */
	public static function add_screen_notice( $data ) {
		// Allow to pass multiple notices at once.
		if ( isset( $data[0] ) && is_array( $data[0] ) ) {
			foreach ( $data as $notice ) {
				static::add_screen_notice( $notice );
			}

			return;
		}

		// Allow to pass notice text as first parameter without key.
		if ( isset( $data[0] ) && is_string( $data[0] ) ) {
			$data['message'] = $data[0];
		}

		$notice = static::prepare_notice( $data );

		if ( false !== $notice ) {
			self::$notices[] = $data;
		}
	}

	/**
	 * Prepare notice data.
	 * 
	 * @param array $data Notice data.
	 * 
	 * @return array|bool
	 */
	protected static function prepare_notice( $data ) {
		$types = [ 'error', 'success', 'warning', 'info' ];

		// notice text can be also passed using notice type as key.
		foreach ( $types as $type ) {
			if ( ! empty( $data[ $type ] ) ) {
				$data['type']    = $type;
				$data['message'] = $data[ $type ];

				unset( $data[ $type ] );

				break;
			}
		}

		if ( empty( $data['message'] ) ) {
			return false;
		}

		if ( empty( $data['type'] ) || ! in_array( $data['type'], $types ) ) {
			$data['type'] = 'success';
		}

		if ( empty( $data['id'] ) ) {
			$data['id'] = wp_generate_password( 12, false );
		}

		if ( array_key_exists( 'dismissable', $data ) ) {
			$data['dismissable'] = (bool) $data['dismissable'];
		} else {
			$data['dismissable'] = true;
		}

		return $data;
	}
}
