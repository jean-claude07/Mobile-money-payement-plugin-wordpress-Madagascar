document.addEventListener('DOMContentLoaded', function() {
    const paymentForm = document.querySelector('#mobile-money-payment-form');

    if (paymentForm) {
        paymentForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const mobileMoneyNumber = document.querySelector('input[name="mobile_money_number"]').value;
            const mobileMoneyOperator = document.querySelector('select[name="mobile_money_operator"]').value;

            if (!mobileMoneyNumber || !mobileMoneyOperator) {
                alert('Veuillez entrer votre numéro Mobile Money et sélectionner un opérateur.');
                return;
            }

            fetch('/wp-json/mobile-money/v1/process-payment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    number: mobileMoneyNumber,
                    operator: mobileMoneyOperator,
                    amount: paymentForm.dataset.amount,
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    alert('Échec du paiement Mobile Money: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Une erreur est survenue lors du traitement du paiement.');
            });
        });
    }
});