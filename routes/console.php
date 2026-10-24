<?php

use App\Services\BackupService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 | Sauvegarde de la base :  php artisan backup:run
 | Les fichiers vont dans storage/app/backups.
 */
Artisan::command('backup:run {--auto : Sauvegarde automatique (garde les 30 dernières)}', function (BackupService $backups) {
    $nom = $backups->creer((bool) $this->option('auto'));
    $this->info("Sauvegarde créée : storage/app/backups/$nom");
})->purpose('Sauvegarder la base de données du restaurant');

// Sauvegarde automatique chaque soir (nécessite « php artisan schedule:run » chaque minute,
// via le Planificateur de tâches Windows — voir LISEZ-MOI).
Schedule::command('backup:run --auto')->dailyAt('23:30');

/*
 | Donner un rôle à un compte :  php artisan user:role caisse@resto.com caissier
 | Rôles : admin, caissier, serveur, cuisinier
 */
Artisan::command('user:role {email} {role}', function (string $email, string $role) {
    $user = \App\Models\User::where('email', $email)->first();
    if (!$user) {
        $this->error("Aucun compte avec l'e-mail $email");
        return 1;
    }
    $user->update(['role' => strtolower($role)]);
    $this->info("$user->name ($email) a maintenant le rôle « $user->role ».");
    return 0;
})->purpose("Changer le rôle d'un utilisateur");
