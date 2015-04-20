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
			return;
		}
		$components = $this->choices;
		$order      = $this->value();
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
						if ( false == $v['status'] ) {
							$class = 'disabled';
						}
					?>
					<li id="<?php echo esc_attr( $k ); ?>" class="<?php echo $class; ?>"><span class="visibility"></span><?php echo esc_attr( $v['nice_name'] ); ?></li>
				<?php endforeach; ?>
			</ul>
			<input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr( $this->value() ); ?>"/>
		</label>
		<?php
	}

	/**
	 * Re-order the components in the given array, based on the stored order.
	 * @access  private
	 * @since   2.0.0
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
				if ( false !== strpos( $v, '[disabled]' ) ) {
					$v = str_replace( '[disabled]', '', $v );
					$components[ $v ] = array(
						'nice_name' => $original_components[ $v ],
						'status'	=> false,
					);
				} else {
					$components[ $v ] = array(
						'nice_name' => $original_components[ $v ],
						'status'	=> true,
					);
				}
				unset( $original_components[ $v ] );
			}

			// Loop through any components left in the original order
			if ( 0 < count( $original_components ) ) {
				foreach ( $original_components as $k => $v ) {
					$components[ $k ] = array(
						'nice_name' => $v,
						'status'	=> true,
					);
				}
			}
		}

		return $components;
	} // End _reorder_components()
}