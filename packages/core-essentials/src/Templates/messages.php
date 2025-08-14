<div class="bs-messages">
	<?php if ( $messages = \BetterStudio\Core\get_template_variable( 'messages' ) ): ?>
		<?php foreach ( $messages as $message ): ?>
			<?php $type = empty( $message['type'] ) ? 'error' : $message['type']; ?>
            <div class="bs-<?php echo $type ?>-item bs-<?php echo $type ?>-<?php echo esc_attr( $message['code'] ) ?>">
				<?php echo $message['message'] ?>
            </div>

		<?php endforeach ?>
	<?php endif ?>
</div>