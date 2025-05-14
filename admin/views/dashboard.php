<div id="mobile-money-dashboard" class="wrap">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Statistiques en temps réel -->
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-bold mb-2">Aujourd'hui</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-3xl font-bold">{{ stats.today.count }}</span>
                    <p class="text-gray-600">Transactions</p>
                </div>
                <div>
                    <span class="text-3xl font-bold">{{ formatAmount(stats.today.amount) }}</span>
                    <p class="text-gray-600">Volume</p>
                </div>
            </div>
        </div>
        
        <!-- Autres widgets... -->
    </div>

    <!-- Graphiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <canvas id="transactionsChart"></canvas>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <canvas id="operatorsChart"></canvas>
        </div>
    </div>
</div>