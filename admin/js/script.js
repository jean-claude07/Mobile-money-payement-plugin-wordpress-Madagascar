jQuery(document).ready(function($) {
    // Vérification du paiement
    $('.verify-payment').on('click', function(e) {
        e.preventDefault();
        var button = $(this);
        var transactionId = button.data('id');

        button.prop('disabled', true);
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'verify_mobile_money_payment',
                nonce: mobile_money_admin.nonce,
                transaction_id: transactionId
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || 'Une erreur est survenue');
                }
            },
            error: function() {
                alert('Erreur de communication avec le serveur');
            },
            complete: function() {
                button.prop('disabled', false);
            }
        });
    });

    // Remboursement
    $('.issue-refund').on('click', function(e) {
        e.preventDefault();
        var transactionId = $(this).data('id');
        
        var amount = prompt('Montant à rembourser:');
        if (!amount) return;

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'mobile_money_refund',
                nonce: mobile_money_admin.nonce,
                transaction_id: transactionId,
                amount: amount
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || 'Erreur lors du remboursement');
                }
            }
        });
    });
});