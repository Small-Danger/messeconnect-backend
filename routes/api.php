<?php

use App\Http\Controllers\Api\Admin\AbonnementController as AdminAbonnementController;
use App\Http\Controllers\Api\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\DioceseController as AdminDioceseController;
use App\Http\Controllers\Api\Admin\FideleController as AdminFideleController;
use App\Http\Controllers\Api\Admin\JournalAuditController as AdminJournalAuditController;
use App\Http\Controllers\Api\Admin\ModerationController as AdminModerationController;
use App\Http\Controllers\Api\Admin\PaiementController as AdminPaiementController;
use App\Http\Controllers\Api\Admin\ParoisseController as AdminParoisseController;
use App\Http\Controllers\Api\Admin\TicketSupportController as AdminTicketSupportController;
use App\Http\Controllers\Api\Fidele\AuthController as FideleAuthController;
use App\Http\Controllers\Api\Fidele\GoogleAuthController;
use App\Http\Controllers\Api\Fidele\DemandeMesseController as FideleDemandeMesseController;
use App\Http\Controllers\Api\Fidele\FavoriParoisseController;
use App\Http\Controllers\Api\Fidele\NotificationController as FideleNotificationController;
use App\Http\Controllers\Api\Fidele\PaiementController as FidelePaiementController;
use App\Http\Controllers\Api\Fidele\ParoisseController as FideleParoisseController;
use App\Http\Controllers\Api\Fidele\ProfileController as FideleProfileController;
use App\Http\Controllers\Api\Fidele\RegisterController as FideleRegisterController;
use App\Http\Controllers\Api\Paroisse\AuthController as ParoisseAuthController;
use App\Http\Controllers\Api\Paroisse\CampagneCollecteController;
use App\Http\Controllers\Api\Paroisse\DashboardController;
use App\Http\Controllers\Api\Paroisse\DemandeMesseController;
use App\Http\Controllers\Api\Paroisse\IntentionGuichetController;
use App\Http\Controllers\Api\Paroisse\MediaParoisseController;
use App\Http\Controllers\Api\Paroisse\MesseController;
use App\Http\Controllers\Api\Paroisse\ModeleMesseController;
use App\Http\Controllers\Api\Paroisse\MoyenPaiementController;
use App\Http\Controllers\Api\Paroisse\PaiementParoisseController;
use App\Http\Controllers\Api\Paroisse\PlanningIntentionController;
use App\Http\Controllers\Api\Paroisse\ProfileController;
use App\Http\Controllers\Api\Paroisse\PublicationController;
use App\Http\Controllers\Api\Paroisse\RegisterController;
use App\Http\Controllers\Api\Paroisse\TicketSupportController;
use App\Http\Controllers\Api\Paroisse\TypeOffrandeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API MesseConnect
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::post('login', [AdminAuthController::class, 'login'])->name('login');

    Route::middleware(['auth:admin', 'account.active'])->group(function () {
        Route::get('me', [AdminAuthController::class, 'me'])->name('me');
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('journal-audit', [AdminJournalAuditController::class, 'index'])->name('journal-audit.index');

        Route::get('paiements', [AdminPaiementController::class, 'index'])->name('paiements.index');
        Route::get('paiements/{paiement}', [AdminPaiementController::class, 'show'])->name('paiements.show');

        Route::apiResource('dioceses', AdminDioceseController::class);

        Route::get('paroisses', [AdminParoisseController::class, 'index'])->name('paroisses.index');
        Route::get('paroisses/{paroisse}', [AdminParoisseController::class, 'show'])->name('paroisses.show');
        Route::patch('paroisses/{paroisse}/statut', [AdminParoisseController::class, 'updateStatut'])
            ->name('paroisses.statut');
        Route::patch('paroisses/{paroisse}/actif', [AdminParoisseController::class, 'updateActif'])
            ->name('paroisses.actif');

        Route::get('fideles', [AdminFideleController::class, 'index'])->name('fideles.index');
        Route::get('fideles/{fidele}', [AdminFideleController::class, 'show'])->name('fideles.show');
        Route::patch('fideles/{fidele}/actif', [AdminFideleController::class, 'updateActif'])
            ->name('fideles.actif');

        Route::get('tickets-support', [AdminTicketSupportController::class, 'index'])->name('tickets-support.index');
        Route::get('tickets-support/{ticket_support}', [AdminTicketSupportController::class, 'show'])
            ->name('tickets-support.show');
        Route::patch('tickets-support/{ticket_support}/statut', [AdminTicketSupportController::class, 'updateStatut'])
            ->name('tickets-support.statut');

        Route::get('abonnements', [AdminAbonnementController::class, 'index'])->name('abonnements.index');
        Route::post('abonnements', [AdminAbonnementController::class, 'store'])->name('abonnements.store');
        Route::get('abonnements/{abonnement}', [AdminAbonnementController::class, 'show'])->name('abonnements.show');
        Route::put('abonnements/{abonnement}', [AdminAbonnementController::class, 'update'])->name('abonnements.update');

        Route::get('publications', [AdminModerationController::class, 'publications'])->name('publications.index');
        Route::patch('publications/{publication}/visible', [AdminModerationController::class, 'updatePublicationVisible'])
            ->name('publications.visible');
        Route::get('campagnes', [AdminModerationController::class, 'campagnes'])->name('campagnes.index');
    });
});

Route::prefix('paroisse')->name('paroisse.')->group(function () {
    Route::post('login', [ParoisseAuthController::class, 'login'])->name('login');
    Route::get('dioceses', [RegisterController::class, 'dioceses'])->name('dioceses.index');
    Route::post('register', [RegisterController::class, 'store'])->name('register');

    Route::middleware(['auth:paroisse', 'account.active'])->group(function () {
        Route::get('me', [ParoisseAuthController::class, 'me'])->name('me');
        Route::post('logout', [ParoisseAuthController::class, 'logout'])->name('logout');

        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('profile/image', [ProfileController::class, 'uploadImage'])
            ->name('profile.upload-image');

        Route::apiResource('medias', MediaParoisseController::class);
        Route::apiResource('moyen-paiements', MoyenPaiementController::class);
        Route::apiResource('type-offrandes', TypeOffrandeController::class);

        Route::post('modele-messes/{modele_messe}/generer', [ModeleMesseController::class, 'generer'])
            ->name('modele-messes.generer');
        Route::apiResource('modele-messes', ModeleMesseController::class);

        Route::get('messes', [MesseController::class, 'index'])->name('messes.index');
        Route::post('messes', [MesseController::class, 'store'])->name('messes.store');
        Route::get('messes/{messe}', [MesseController::class, 'show'])->name('messes.show');
        Route::put('messes/{messe}', [MesseController::class, 'update'])->name('messes.update');
        Route::delete('messes/{messe}', [MesseController::class, 'destroy'])->name('messes.destroy');
        Route::post('messes/{messe}/celebrer', [MesseController::class, 'celebrer'])->name('messes.celebrer');
        Route::post('messes/{messe}/annuler', [MesseController::class, 'annuler'])->name('messes.annuler');

        Route::get('demandes', [DemandeMesseController::class, 'index'])->name('demandes.index');
        Route::get('demandes/{demande_messe}', [DemandeMesseController::class, 'show'])->name('demandes.show');
        Route::patch('demandes/{demande_messe}/statut', [DemandeMesseController::class, 'updateStatut'])
            ->name('demandes.statut');

        Route::get('planning-intentions', [PlanningIntentionController::class, 'index'])
            ->name('planning-intentions.index');
        Route::post('intentions/guichet', [IntentionGuichetController::class, 'store'])
            ->name('intentions.guichet.store');
        Route::get('paiements/en-attente', [PaiementParoisseController::class, 'indexEnAttente'])
            ->name('paiements.en-attente');
        Route::post('paiements/{paiement}/confirmer', [PaiementParoisseController::class, 'confirmer'])
            ->name('paiements.confirmer');
        Route::post('paiements/{paiement}/annuler', [PaiementParoisseController::class, 'annuler'])
            ->name('paiements.annuler');

        Route::post('publications/images', [PublicationController::class, 'uploadImages'])
            ->name('publications.upload-images');
        Route::apiResource('publications', PublicationController::class);
        Route::post('campagnes/image', [CampagneCollecteController::class, 'uploadImage'])
            ->name('campagnes.upload-image');
        Route::get('campagnes/{campagne_collecte}/dons', [CampagneCollecteController::class, 'dons'])
            ->name('campagnes.dons');
        Route::apiResource('campagnes', CampagneCollecteController::class)->parameters([
            'campagnes' => 'campagne_collecte',
        ]);

        Route::get('tickets-support', [TicketSupportController::class, 'index'])->name('tickets-support.index');
        Route::post('tickets-support', [TicketSupportController::class, 'store'])->name('tickets-support.store');
        Route::get('tickets-support/{ticket_support}', [TicketSupportController::class, 'show'])
            ->name('tickets-support.show');
    });
});

Route::prefix('fidele')->name('fidele.')->group(function () {
    Route::post('login', [FideleAuthController::class, 'login'])->name('login');
    Route::post('register', [FideleRegisterController::class, 'store'])->name('register');
    Route::post('auth/google', GoogleAuthController::class)->name('auth.google');

    Route::middleware('fidele.optional')->group(function () {
        Route::get('paroisses', [FideleParoisseController::class, 'index'])->name('paroisses.index');
        Route::get('paroisses/{paroisse}', [FideleParoisseController::class, 'show'])->name('paroisses.show');
        Route::get('paroisses/{paroisse}/messes', [FideleParoisseController::class, 'messes'])->name('paroisses.messes');
        Route::get('paroisses/{paroisse}/type-offrandes', [FideleParoisseController::class, 'typeOffrandes'])
            ->name('paroisses.type-offrandes');
        Route::get('paroisses/{paroisse}/moyen-paiements', [FideleParoisseController::class, 'moyenPaiements'])
            ->name('paroisses.moyen-paiements');
        Route::get('paroisses/{paroisse}/publications', [FideleParoisseController::class, 'publications'])
            ->name('paroisses.publications');
        Route::get('paroisses/{paroisse}/campagnes', [FideleParoisseController::class, 'campagnes'])
            ->name('paroisses.campagnes');

        Route::post('demandes', [FideleDemandeMesseController::class, 'store'])->name('demandes.store');
        Route::get('demandes/reference/{reference}', [FideleDemandeMesseController::class, 'showByReference'])
            ->name('demandes.reference');

        Route::post('demandes/{demande_messe}/paiements', [FidelePaiementController::class, 'storeForDemande'])
            ->name('demandes.paiements.store');
        Route::post('campagnes/{campagne_collecte}/paiements', [FidelePaiementController::class, 'storeForCampagne'])
            ->name('campagnes.paiements.store');
    });

    Route::middleware(['auth:fidele', 'account.active'])->group(function () {
        Route::get('me', [FideleAuthController::class, 'me'])->name('me');
        Route::post('logout', [FideleAuthController::class, 'logout'])->name('logout');
        Route::put('profile', [FideleProfileController::class, 'update'])->name('profile.update');

        Route::get('favoris', [FavoriParoisseController::class, 'index'])->name('favoris.index');
        Route::post('favoris/{paroisse}', [FavoriParoisseController::class, 'store'])->name('favoris.store');
        Route::delete('favoris/{paroisse}', [FavoriParoisseController::class, 'destroy'])->name('favoris.destroy');

        Route::get('demandes', [FideleDemandeMesseController::class, 'index'])->name('demandes.index');
        Route::get('demandes/{demande_messe}', [FideleDemandeMesseController::class, 'show'])->name('demandes.show');

        Route::get('paiements', [FidelePaiementController::class, 'index'])->name('paiements.index');

        Route::get('notifications', [FideleNotificationController::class, 'index'])->name('notifications.index');
        Route::patch('notifications/{notification}/lue', [FideleNotificationController::class, 'marquerCommeLue'])
            ->name('notifications.lue');
    });

    if (app()->environment(['local', 'testing'])) {
        Route::post('paiements/{paiement}/confirmer', [FidelePaiementController::class, 'confirmer'])
            ->name('paiements.confirmer');
    }
});
