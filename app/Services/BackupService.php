<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

/**
 * Sauvegarde complète de la base de données dans un fichier .sql.gz.
 *
 * - Écrit en PHP pur : fonctionne sans mysqldump (souvent absent sous XAMPP/Windows).
 * - Compatible MySQL/MariaDB (production) et SQLite.
 * - Fichiers rangés dans storage/app/backups (jamais accessibles depuis le web).
 * - Le fichier se restaure avec phpMyAdmin (onglet Importer) ou :
 *       mysql -u root -p resto_qr_db < sauvegarde.sql
 */
class BackupService
{
    /** Tables dont on garde la structure mais pas les données (temporaires). */
    private const SANS_DONNEES = ['sessions', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs'];

    /** Nombre de sauvegardes automatiques conservées. */
    private const GARDER_AUTO = 30;

    /** Nom de fichier autorisé (protège le téléchargement et la suppression). */
    public const MOTIF_FICHIER = '/^resto-kayy_\d{4}-\d{2}-\d{2}_\d{6}(_auto)?\.sql\.gz$/';

    public function dossier(): string
    {
        $dir = storage_path('app/backups');
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException("Impossible de créer le dossier $dir");
        }
        return $dir;
    }

    /**
     * Crée une sauvegarde et renvoie le nom du fichier.
     */
    public function creer(bool $auto = false): string
    {
        @set_time_limit(300);

        $nom    = 'resto-kayy_' . now()->format('Y-m-d_His') . ($auto ? '_auto' : '') . '.sql.gz';
        $chemin = $this->dossier() . DIRECTORY_SEPARATOR . $nom;
        $tmp    = $chemin . '.part';

        $gz = gzopen($tmp, 'wb6');
        if (!$gz) {
            throw new RuntimeException("Impossible d'écrire $tmp");
        }

        try {
            $this->ecrireDump(fn (string $sql) => gzwrite($gz, $sql));
        } catch (\Throwable $e) {
            gzclose($gz);
            @unlink($tmp);
            throw $e;
        }

        gzclose($gz);
        rename($tmp, $chemin);

        if ($auto) {
            $this->nettoyerAuto();
        }

        return $nom;
    }

    /**
     * Crée une sauvegarde automatique si la dernière date de plus de $heures heures.
     * Ne fait jamais échouer la page qui l'appelle.
     */
    public function autoSiNecessaire(int $heures = 24): ?string
    {
        try {
            $derniere = $this->liste()->first();
            if ($derniere && $derniere['date']->gt(now()->subHours($heures))) {
                return null;
            }
            return $this->creer(true);
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }

    /**
     * Liste des sauvegardes, la plus récente d'abord.
     */
    public function liste()
    {
        $fichiers = glob($this->dossier() . DIRECTORY_SEPARATOR . 'resto-kayy_*.sql.gz') ?: [];

        return collect($fichiers)
            ->filter(fn ($f) => preg_match(self::MOTIF_FICHIER, basename($f)))
            ->map(fn ($f) => [
                'nom'    => basename($f),
                'taille' => filesize($f),
                'date'   => \Carbon\Carbon::createFromTimestamp(filemtime($f))->setTimezone(config('app.timezone')),
                'auto'   => str_contains(basename($f), '_auto'),
            ])
            ->sortByDesc('date')
            ->values();
    }

    public function chemin(string $nom): string
    {
        if (!preg_match(self::MOTIF_FICHIER, $nom)) {
            abort(404);
        }
        $chemin = $this->dossier() . DIRECTORY_SEPARATOR . $nom;
        if (!is_file($chemin)) {
            abort(404);
        }
        return $chemin;
    }

    public function supprimer(string $nom): void
    {
        @unlink($this->chemin($nom));
    }

    /* ------------------------------------------------------------------ */

    private function ecrireDump(callable $out): void
    {
        $conn   = DB::connection();
        $driver = $conn->getDriverName();
        $pdo    = $conn->getPdo();
        $q      = fn (string $id) => $driver === 'sqlite' ? '"' . str_replace('"', '""', $id) . '"' : '`' . str_replace('`', '``', $id) . '`';

        $out("-- Resto Kay-Y : sauvegarde de la base « " . $conn->getDatabaseName() . " »\n");
        $out("-- Créée le " . now()->format('d/m/Y H:i:s') . " (" . $driver . ")\n\n");

        if ($driver === 'sqlite') {
            $out("PRAGMA foreign_keys=OFF;\nBEGIN TRANSACTION;\n\n");
        } else {
            $out("SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\nSET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\nSTART TRANSACTION;\n\n");
        }

        // Uniquement les tables de CETTE base (Schema::getTables() renvoie aussi
        // les tables des autres bases MySQL du serveur XAMPP).
        if ($driver === 'sqlite') {
            $tables = collect(DB::select("SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%' ORDER BY name"))
                ->pluck('name');
        } else {
            $tables = collect(DB::select("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'"))
                ->map(fn ($r) => array_values((array) $r)[0]);
        }
        $tables = $tables->filter()->values();

        foreach ($tables as $table) {
            // Structure
            if ($driver === 'sqlite') {
                $create = DB::selectOne("SELECT sql FROM sqlite_master WHERE type='table' AND name = ?", [$table])->sql ?? null;
            } else {
                $row    = (array) DB::selectOne('SHOW CREATE TABLE ' . $q($table));
                $create = $row['Create Table'] ?? array_values($row)[1] ?? null;
            }
            if (!$create) {
                continue;
            }

            $out("-- ----------------------------------------------------\n-- Table $table\n-- ----------------------------------------------------\n");
            $out('DROP TABLE IF EXISTS ' . $q($table) . ";\n" . $create . ";\n\n");

            if (in_array($table, self::SANS_DONNEES, true)) {
                continue;
            }

            // Données, par paquets de 200 lignes
            $colonnes = null;
            $paquet   = [];
            foreach (DB::table($table)->cursor() as $ligne) {
                $ligne = (array) $ligne;
                $colonnes ??= implode(', ', array_map($q, array_keys($ligne)));
                $paquet[] = '(' . implode(', ', array_map(fn ($v) => $this->valeur($v, $pdo), $ligne)) . ')';
                if (count($paquet) === 200) {
                    $out('INSERT INTO ' . $q($table) . " ($colonnes) VALUES\n" . implode(",\n", $paquet) . ";\n");
                    $paquet = [];
                }
            }
            if ($paquet) {
                $out('INSERT INTO ' . $q($table) . " ($colonnes) VALUES\n" . implode(",\n", $paquet) . ";\n");
            }
            $out("\n");
        }

        $out($driver === 'sqlite' ? "COMMIT;\nPRAGMA foreign_keys=ON;\n" : "COMMIT;\nSET FOREIGN_KEY_CHECKS=1;\n");
    }

    private function valeur($v, \PDO $pdo): string
    {
        if ($v === null) {
            return 'NULL';
        }
        if (is_bool($v)) {
            return $v ? '1' : '0';
        }
        if (is_int($v) || is_float($v)) {
            return (string) $v;
        }
        return $pdo->quote((string) $v);
    }

    private function nettoyerAuto(): void
    {
        $this->liste()
            ->where('auto', true)
            ->slice(self::GARDER_AUTO)
            ->each(fn ($f) => @unlink($this->dossier() . DIRECTORY_SEPARATOR . $f['nom']));
    }
}