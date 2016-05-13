<?php
global $kingdom; 

if ( comments_open() ) :
$comments_count = wp_count_comments( $post->ID );
?>
<!-- Comments -->
<div class="comments">
	<?php if ( have_comments() ) : ?>
		<div class="kd_comments_header">
			<h2><?php _e('Comments', 'kingdom'); ?></h2>
			<p> <?php printf( _nx( 'One comment', '%1$s comments', $comments_count->approved, 'comments title', 'kingdom' ), number_format_i18n($comments_count->approved) ); ?> </p>
		</div>
		
		<ul class="media-list">
			<?php
				wp_list_comments( array(
					'callback'	  => array( $kingdom->coreFunctions, 'comment_template' ),
					'reply_text'  => __('Reply', 'kingdom'),
					'short_ping'  => true,
					'avatar_size' => 80
				) );
				
				echo paginate_comments_links();
			?>
		</ul>
	<?php endif; ?>
	
	
	<div class="kd_comment_form">
		<h2 class="leave-replay"><?php _e( 'Write a Reply or Comment:', 'kingdom' ); ?></h2>
		<?php
		$commenter = wp_get_current_commenter();
		$req = get_option( 'require_name_email' );
		$aria_req = ( $req ? " aria-required='true'" : '' );
		
		$commentForm_args = array(
			 // change the title of send button 
		    'label_submit' => __( 'Post Comment', 'kingdom' ),
		    // change the title of the reply section
		    'title_reply' => '',
		    'title_reply_to' => __( 'Leave a Reply to %s', 'kingdom' ),
		    'cancel_reply_link' => __( 'Cancel Reply', 'kingdom' ),
		    'logged_in_as' => '',
		    // remove "Text or HTML to be displayed before/after the set of comment fields"
		    'comment_notes_before' => '',
		    'comment_notes_after' => '',
		    // redefine your own textarea (the comment body)
		    'comment_field' => '<label for="comment">Comment <span>*</span> :</label><textarea id="comment" name="comment" rows="9" ' . $aria_req . '></textarea>',
		    'fields' => apply_filters( 'comment_form_default_fields', array(
				'author' =>
					'<fieldset><label for="author">' . __( 'Name', 'kingdom' ) .
					( $req ? ' <span>*</span> ' : '' ) .
					': <input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) .
					'" placeholder="Type something.." ' . $aria_req . ' /></label>',
				
				'email' =>
					'<label for="email">' . __( 'Email', 'kingdom' ) .
					( $req ? ' <span>*</span> ' : '' ) .
					': <input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) .
					'" placeholder="Type something.." ' . $aria_req . ' /></label></fieldset>'
			))
		);
		 
		comment_form($commentForm_args);
		?>
	</div>
	
	<div class="clearfix"></div>
</div>
<?php
endif;