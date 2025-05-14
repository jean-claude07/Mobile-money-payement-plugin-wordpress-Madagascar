<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php
    if (!empty($transactions)) {
        ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Opérateur</th>
                    <th>Numéro</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction) : ?>
                    <tr>
                        <td><?php echo esc_html($transaction->id); ?></td>
                        <td><?php echo esc_html($transaction->operator); ?></td>
                        <td><?php echo esc_html($transaction->number); ?></td>
                        <td><?php echo esc_html(number_format($transaction->amount, 2)); ?></td>
                        <td><?php echo esc_html($transaction->status); ?></td>
                        <td><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($transaction->created_at))); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p><?php _e('Aucune transaction trouvée.', 'mobile-money-mg'); ?></p>
    <?php } ?>
</div>