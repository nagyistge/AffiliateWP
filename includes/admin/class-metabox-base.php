<?php
namespace AffWP\Meta_Box;

/**
 * AffiliateWP Admin Meta Box Base class.
 * Provides a base structure for AffiliateWP content meta boxes.
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Metaboxes
 * @copyright   Copyright (c) 2016, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.9
 */

// Exit if accessed directly
if ( ! defined('ABSPATH') ) {
	exit;
}

/**
 * The main AffWP\Meta_Box\Base class.
 * This class may be extended using the example below.
 *
 * An AffiliateWP meta box can be added to AffiliateWP by any
 * 3rd-party source, by extending this class.
 *
 * A functional example of extending this class may also
 * be seen in our AffiliateWP Developer Docs:
 * docs.affiliatewp.com/#TODO
 *
 * Example:
 *
 *    namespace AffWP\Meta_Box;
 *    require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/class-metabox-base.php';
 *
 *    class My_Integration extends Base {
 *
 *        public function init() {
 *            $this->meta_box_id   = 'my_integration_affwp_metabox';
 *            $this->meta_box_name = __( 'My Integration AffWP Meta box', 'affiliate-wp' );
 *
 *            // Optionally, you may define:
 *            //
 *            // $this->action:  The AffiliateWP action on which the meta box loads.
 *            //                 Defaults to the Overview page action, `affwp_overview_meta_boxes`.
 *            //                 Note that a corresponding `do_metaboxes()` must be called at the
 *            //                 location where this action fires in order for the meta box to show.
 *            //
 *            // $this->context: Define the context here. Defaults to `primary`.
 *            //                 Options are `primary`,`secondary`, or `tertiary`.
 *        }
 *
 *        public function content() {
 *            $this->my_meta_box_content();
 *        }
 *
 *        public function my_meta_box_content() {
 *            _e( 'Here is some content I\'d like to share with AffiliateWP users!', 'affiliate-wp' );
 *        }
 *
 *    }
 *
 *    new My_Integration;
 *
 * @abstract
 * @since  1.9
 */
abstract class Base {

	/**
	 * The ID of the meta box. Must be unique.
	 *
	 * @abstract
	 * @access  public
	 * @var     $meta_box_id The ID of the meta box
	 * @since   1.9
	 */
	public $meta_box_id;

	/**
	 * The name of the meta box.
	 * This should very briefly describe the contents of the meta box.
	 *
	 * @abstract
	 * @access  public
	 * @var     $meta_box_name The name of the meta box
	 * @since   1.9
	 */
	public $meta_box_name;

	/**
	 * The AffiliateWP screen on which to show the meta box.
	 * Defaults to affiliates_page_affiliate-wp-reports,
	 * the AffiliateWP Reports Overview tab page.
	 *
	 * The uri of this page is: admin.php?page=affiliate-wp-reports.
	 *
	 * @access  private
	 * @var     $affwp_screen The screen ID of the page on which to display this meta box.
	 * @since   1.9
	 */
	private $affwp_screen = array(
		'toplevel_page_affiliate-wp',
		'affiliates_page_affiliate-wp-affiliates',
		'affiliates_page_affiliate-wp-referrals',
		'affiliates_page_affiliate-wp-visits',
		'affiliates_page_affiliate-wp-creatives',
		'affiliates_page_affiliate-wp-reports',
		'affiliates_page_affiliate-wp-tools',
		'affiliates_page_affiliate-wp-settings',
		'affiliates_page_affiliate-wp-add-ons'
	);
	/**
	 * The position in which the meta box will be loaded.
	 * AffiliateWP uses custom meta box contexts.
	 * These contexts are listed below.
	 *
	 * 'primary':   Loads in the left column.
	 * 'secondary': Loads in the center column.
	 * 'tertiary':  Loads in the right column.
	 *
	 * All columns will collapse as needed on smaller screens,
	 * as WordPress core meta boxes are in use.
	 *
	 * @access  public
	 * @var     $context
	 * @since   1.9
	 */
	public $context = 'primary';

	/**
	 * The action on which the meta box will be loaded.
	 * AffiliateWP uses custom meta box actions.
	 * These contexts are listed below:
	 *
	 * 'affwp_overview_meta_boxes': Loads on the Overview page.
	 *
	 *
	 * @access  public
	 * @var     $action
	 * @since   1.9
	 */
	public $action = 'affwp_overview_meta_boxes';

	/**
	 * Constructor
	 *
	 * @access  public
	 * @return void
	 * @since   1.9
	 */
	public function __construct() {
		$this->init();
		add_action( 'add_meta_box', array( $this, 'add_meta_box' ) );
		add_action( $this->action,  array( $this, 'add_meta_box' ) );
	}

	/**
	 * Initialize.
	 *
	 * Define the meta box name,
	 * and the action on which to hook the meta box here.
	 *
	 * Example:
	 *
	 *    $this->action        = 'affwp_overview_meta_boxes';
	 *    $this->meta_box_name = __( 'Name of the meta box', 'affiliate-wp' );
	 *
	 * @access  public
	 * @return  void
	 * @since   1.9
	 */
	abstract public function init();

	/**
	 * Adds the meta box
	 *
	 * @return  A meta box which will display on the specified AffiliateWP Reports admin page.
	 * @uses    add_meta_box
	 * @since   1.9
	 */
	public function add_meta_box() {
		add_meta_box(
			$this->meta_box_id,
			__( $this->meta_box_name, 'affiliate-wp' ),
			array( $this, 'get_content' ),
			$this->affwp_screen,
			$this->context,
			'default'
		);
	}

	/**
	 * Gets the content set in $this->content(),
	 * which is retrieved by $this->_content().
	 *
	 * @return mixed string The content of the meta box.
	 * @since  1.9
	 */
	public function get_content() {
		/**
		 * Filter the title tag content for an admin page.
		 *
		 * @param string $content The content of the meta box, set in $this->content()
		 * @since 1.9
		 *
		 */
		$content = $this->_content();
		return apply_filters( 'affwp_meta_box_' . $this->meta_box_id, $content );
	}

	/**
	 * A protected method which echoes the $this->content().
	 *
	 * @return mixed string The content of the meta box.
	 * @since  1.9
	 */
	protected function _content() {
		return $this->content();
	}

	/**
	 * Defines the meta box content, as well as a
	 * filter by which the content may be adjusted.
	 *
	 * Use this method in your child class to define
	 * the content of your meta box.
	 *
	 * Given a $meta_box_id value of 'my-metabox-id',
	 * the filter would be: affwp_meta_box_my-meta-box-id.
	 *
	 * @return mixed string The content of the meta box
	 * @since  1.9
	 */
	abstract public function content();
}
