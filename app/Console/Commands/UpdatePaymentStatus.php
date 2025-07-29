<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Services\MoyasarService;
use Illuminate\Console\Command;
use Exception;

class UpdatePaymentStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:update-status 
                            {--payment-id= : ID du paiement spécifique à vérifier}
                            {--all : Vérifier tous les paiements en attente}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifie et met à jour le statut des paiements avec l\'API Moyasar';

    protected $moyasarService;

    public function __construct(MoyasarService $moyasarService)
    {
        parent::__construct();
        $this->moyasarService = $moyasarService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Vérification du statut des paiements...');

        if ($paymentId = $this->option('payment-id')) {
            // Vérifier un paiement spécifique
            $this->updateSinglePayment($paymentId);
        } elseif ($this->option('all')) {
            // Vérifier tous les paiements en attente
            $this->updateAllPendingPayments();
        } else {
            // Par défaut, vérifier les paiements récents (dernières 24h)
            $this->updateRecentPendingPayments();
        }

        $this->info('✅ Vérification terminée.');
        return 0;
    }

    /**
     * Met à jour un paiement spécifique
     */
    private function updateSinglePayment($paymentId)
    {
        $payment = Payment::find($paymentId);

        if (!$payment) {
            $this->error("❌ Paiement avec ID {$paymentId} introuvable.");
            return;
        }

        $this->info("🔍 Vérification du paiement #{$payment->id} (Moyasar: {$payment->payment_gateway_id})");
        $this->updatePaymentStatus($payment);
    }

    /**
     * Met à jour tous les paiements en attente
     */
    private function updateAllPendingPayments()
    {
        $pendingPayments = Payment::where('status', Payment::STATUS_PENDING)
            ->whereNotNull('payment_gateway_id')
            ->get();

        if ($pendingPayments->isEmpty()) {
            $this->info('ℹ️  Aucun paiement en attente trouvé.');
            return;
        }

        $this->info("🔍 {$pendingPayments->count()} paiement(s) en attente trouvé(s).");

        $bar = $this->output->createProgressBar($pendingPayments->count());
        $bar->start();

        foreach ($pendingPayments as $payment) {
            $this->updatePaymentStatus($payment);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    /**
     * Met à jour les paiements en attente des dernières 24h
     */
    private function updateRecentPendingPayments()
    {
        $pendingPayments = Payment::where('status', Payment::STATUS_PENDING)
            ->whereNotNull('payment_gateway_id')
            ->where('created_at', '>=', now()->subDay())
            ->get();

        if ($pendingPayments->isEmpty()) {
            $this->info('ℹ️  Aucun paiement récent en attente trouvé.');
            return;
        }

        $this->info("🔍 {$pendingPayments->count()} paiement(s) récent(s) en attente trouvé(s).");

        foreach ($pendingPayments as $payment) {
            $this->updatePaymentStatus($payment);
        }
    }

    /**
     * Vérifie et met à jour le statut d'un paiement
     */
    private function updatePaymentStatus(Payment $payment)
    {
        try {
            // Vérifier le statut avec l'API Moyasar
            $moyasarPayment = $this->moyasarService->verifyPayment($payment->payment_gateway_id);

            $oldStatus = $payment->status;
            $newStatus = $moyasarPayment['status'];

            $this->line("   💳 Paiement #{$payment->id}: {$oldStatus} → {$newStatus}");

            // Mettre à jour selon le statut Moyasar
            switch ($newStatus) {
                case 'paid':
                    if ($payment->status !== Payment::STATUS_SUCCEEDED) {
                        if ($payment->subscription_id) {
                            $this->moyasarService->handleSubscriptionPaymentSuccess($payment);
                            $this->info("   ✅ Abonnement activé pour l'utilisateur #{$payment->user_id}");
                        } elseif ($payment->template_id) {
                            $this->moyasarService->handleTemplatePaymentSuccess($payment);
                            $this->info("   ✅ Achat de template confirmé pour l'utilisateur #{$payment->user_id}");
                        } else {
                            $payment->update([
                                'status' => Payment::STATUS_SUCCEEDED,
                                'paid_at' => now(),
                            ]);
                            $this->info("   ✅ Paiement marqué comme réussi");
                        }
                    }
                    break;

                case 'failed':
                    if ($payment->status !== Payment::STATUS_FAILED) {
                        $errorMessage = $moyasarPayment['source']['message'] ?? 'Paiement échoué';
                        $this->moyasarService->handlePaymentFailure($payment, $errorMessage);
                        $this->warn("   ❌ Paiement marqué comme échoué: {$errorMessage}");
                    }
                    break;

                case 'pending':
                    $this->comment("   ⏳ Paiement toujours en attente");
                    break;

                default:
                    $this->comment("   ⚠️  Statut inconnu: {$newStatus}");
            }

        } catch (Exception $e) {
            $this->error("   ❌ Erreur lors de la vérification du paiement #{$payment->id}: {$e->getMessage()}");
        }
    }
}
