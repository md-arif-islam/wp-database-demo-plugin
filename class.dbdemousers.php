<?php

if ( ! class_exists( "WP_List_Table" ) ) {
	require_once( ABSPATH . "wp-admin/includes/class-wp-list-table.php" );
}

class DBTableUsers extends WP_List_Table {

	private $_items;

	function __construct( $data ) {
		parent::__construct();
		$this->_items = $data;
	}

	function get_columns() {
		return [
			'cb'     => '<input type="checkbox">',
			'name'   => __( 'Name', 'database-demo' ),
			'email'  => __( 'Email', 'database-demo' ),
			'action' => __( 'Action', 'database-demo' ),
		];
	}

	function column_cb( $item ) {
		return "<input type='checkbox' value='{$item['id']}'>";
	}

	function column_name( $item ) {
		$nonce   = wp_create_nonce( "dbdemo_edit" );
		$actions = [
			'edit'   => sprintf( '<a href="?page=dbdemo&pid=%s&n=%s">%s</a>', $item['id'], $nonce, __( 'Edit', 'database-demo' ) ),
			'delete' => sprintf( '<a href="?page=dbdemo&pid=%s&n=%s&action=%s">%s</a>', $item['id'], $nonce,'delete', __('Delete','database-demo') ),
		];

		return sprintf('%s %s',$item['name'],$this->row_actions($actions));
	}


	function column_action( $item ) {
		$nonce   = wp_create_nonce( "dbdemo_edit" );
		$link = wp_nonce_url( admin_url( 'admin.php?page=dbdemo&pid=' ) . $item['id'], "dbdemo_edit", 'n' );
		$delete_link = sprintf( '<a href="?page=dbdemo&pid=%s&n=%s&action=%s">%s</a>', $item['id'], $nonce,'delete', __('Delete','database-demo') );

		return "<a href='" . esc_url( $link ) . "'>Edit</a> || {$delete_link}";
	}

	function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	function prepare_items() {
		$paged                 = $_REQUEST['paged'] ?? 1;
		$per_page              = 4;
		$total_items           = count( $this->_items );
		$this->_column_headers = array( $this->get_columns(), [], $this->get_sortable_columns() );
		$data_chunks           = array_chunk( $this->_items, $per_page );
		$this->items           = $data_chunks[ $paged - 1 ];
		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( count( $this->_items ) / $per_page )
		] );
	}
}