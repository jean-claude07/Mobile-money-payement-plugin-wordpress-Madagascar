<?php
if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') {
    echo '<div class="notice notice-success is-dismissible"><p>Paramètres sauvegardés avec succès.</p></div>';
}