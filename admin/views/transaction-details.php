<?php if (!defined('ABSPATH')) exit; ?>

<div class="mobile-money-transaction-details">
    <h2><?php _e('Détails de la transaction', 'mobile-money-mg'); ?> #<?php echo esc_html($transaction->id); ?></h2>
    
    <div class="transaction-status <?php echo esc_attr($transaction->status); ?>">
        <span class="status-label"><?php echo esc_html(ucfirst($transaction->status)); ?></span>
    </div>

    <div class="transaction-info">
        <table class="form-table">
            <tr>
                <th><?php _e('Référence', 'mobile-money-mg'); ?></th>
                <td><?php echo esc_html($transaction->reference); ?></td>
            </tr>
            <tr>
                <th><?php _e('Opérateur', 'mobile-money-mg'); ?></th>
                <td><?php echo esc_html(ucfirst($transaction->operator)); ?></td>
            </tr>
            <tr>
                <th><?php _e('Numéro', 'mobile-money-mg'); ?></th>
                <td><?php echo esc_html($transaction->number); ?></td>
            </tr>
            <tr>
                <th><?php _e('Montant', 'mobile-money-mg'); ?></th>
                <td><?php echo wc_price($transaction->amount); ?></td>
            </tr>
            <tr>
                <th><?php _e('Date', 'mobile-money-mg'); ?></th>
                <td><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($transaction->created_at))); ?></td>
            </tr>
            <tr>
                <th><?php _e('Commande', 'mobile-money-mg'); ?></th>
                <td>
                    <a href="<?php echo get_edit_post_link($transaction->order_id); ?>">
                        #<?php echo esc_html($transaction->order_id); ?>
                    </a>
                </td>
            </tr>
        </table>
    </div>

    <?php if ($transaction->metadata): ?>
    <div class="transaction-metadata">
        <h3><?php _e('Métadonnées', 'mobile-money-mg'); ?></h3>
        <pre><?php echo esc_html(print_r($transaction->metadata, true)); ?></pre>
    </div>
    <?php endif; ?>

    <div class="transaction-actions">
        <?php if ($transaction->status === 'pending'): ?>
            <button class="button verify-payment" data-id="<?php echo esc_attr($transaction->id); ?>">
                <?php _e('Vérifier le paiement', 'mobile-money-mg'); ?>
            </button>
        <?php endif; ?>
        
        <?php if ($transaction->status === 'completed'): ?>
            <button class="button issue-refund" data-id="<?php echo esc_attr($transaction->id); ?>">
                <?php _e('Effectuer un remboursement', 'mobile-money-mg'); ?>
            </button>
        <?php endif; ?>
    </div>
</div>