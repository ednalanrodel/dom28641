<?php

// =============================================================================
// FUNCTIONS/NOTICES.PHP
// -----------------------------------------------------------------------------
// Plugin notices.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Notices
// =============================================================================

// Notices
// =============================================================================

function tco_terms_of_use_admin_notices() { ?>

  <?php if ( isset( $_POST['tco_terms_of_use_form_submitted'] ) ) : ?>
    <?php if ( strip_tags( $_POST['tco_terms_of_use_form_submitted'] ) == 'submitted' && current_user_can( 'manage_options' ) ) : ?>

      <div class="updated">
        <p><?php _e( '<strong>Huzzah!</strong> All settings have been successfully saved.', '__tco__' ); ?></p>
      </div>

    <?php endif; ?>
  <?php endif; ?>

<?php }

add_action( 'admin_notices', 'tco_terms_of_use_admin_notices' );