<?php

class Homepage_Control_Customizer_Control extends WP_Customize_Control {

	/**
	 * Enqueue jQuery Sortable and its dependencies.
	 * @access  public
	 * @since   2.0.0
	 * @return  void
	 */
	public function enqueue() {
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-sortable' );
	}

	/**
	 * Display list of ordered components.
	 * @access  public
	 * @since   2.0.0
	 * @return  void
	 */
	public function render_content() {
		if ( ! is_array( $this->choices ) || ! count( $this->choices ) ) {
			printf( __( 'No homepage components found. See the %sdocs%s for details of available homepage component plugins/themes.', 'homepage-control' ), '<a href="' . esc_url( 'http://docs.woothemes.com/document/homepage-control/' ) . '">', '</a>' );
		} else {
			$components         = $this->choices;
			$order              = $this->value();
			$disabled			= $this->_get_disabled_components( $this->value() );
			?>
			<label>
				<?php
					if ( ! empty( $this->label ) ) : ?>
						<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<?php endif;
					if ( ! empty( $this->description ) ) : ?>
						<span class="description customize-control-description"><?php echo $this->description ; ?></span>
					<?php endif;
				?>
				<ul class="homepage-control">
					<?php $components = $this->_reorder_components( $components, $order ); ?>
					<?php foreach ( $components as $k => $v ) : ?>
						<?php
							$class = '';
							if ( in_array( $k, $disabled ) ) {
								$class = 'disabled';
							}
						?>
						<li id="<?php echo esc_attr( $k ); ?>" class="<?php echo $class; ?>"><span class="visibility"></span><?php echo esc_attr( $v ); ?></li>
					<?php endforeach; ?>
				</ul>
				<input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr( $this->value() ); ?>"/>
			</label>
			<?php
		}
	}

	/**
	 * Re-order the components in the given array, based on the stored order.
	 * @access  private
	 * @since   1.0.0
	 * @return  array An array of components, in the correct order.
	 */
	private function _reorder_components ( $components, $order ) {
		$order_entries = array();
		if ( '' != $order ) {
			$order_entries = explode( ',', $order );
		}

		// Re-order the components according to the stored order.
		if ( 0 < count( $order_entries ) ) {
			$original_components = $components; // Make a backup before we overwrite.
			$components = array();
			foreach ( $order_entries as $k => $v ) {
				if ( $this->_is_component_disabled( $v ) ) {
					$v = str_replace( '[disabled]', '', $v );
				}
				$components[ $v ] = $original_components[ $v ];
				unset( $original_components[ $v ] );
			}
			if ( 0 < count( $original_components ) ) {
				$components = array_merge( $components, $original_components );
			}
		}

		return $components;
	} // End _reorder_components()

	/**
	 * Check if a component is disabled.
	 * @access  private
	 * @since   2.0.0
	 * @return  boolean True if a component if disabled.
	 */
	private function _is_component_disabled ( $component ) {
		if ( false !== strpos( $component, '[disabled]' ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Return the disabled components in the given array, based on the format of the key.
	 * @access  private
	 * @since   2.0.0
	 * @return  array An array of disabled components.
	 */
	private function _get_disabled_components ( $components ) {
		$disabled = array();
		if ( '' != $components ) {
			$components = explode( ',', $components );

			if ( 0 < count( $components ) ) {
				foreach ( $components as $k => $v ) {
					if ( $this->_is_component_disabled( $v ) ) {
						$disabled[] = str_replace( '[disabled]', '', $v );
					}
				}
			}
		}
		return $disabled;
	}
}