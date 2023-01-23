<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230522113847 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE annee_universitaire (id INT AUTO_INCREMENT NOT NULL, date_de_rentree DATE NOT NULL, date_de_fin DATE NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE archive (id INT AUTO_INCREMENT NOT NULL, etudiant_id INT NOT NULL, resultat_id INT NOT NULL, INDEX IDX_D5FC5D9CDDEAB1A3 (etudiant_id), INDEX IDX_D5FC5D9CD233E95C (resultat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE archive_note (id INT AUTO_INCREMENT NOT NULL, etudiant_id INT NOT NULL, resultat_id INT NOT NULL, examen_id INT NOT NULL, unite_enseignement_id INT NOT NULL, valeur DOUBLE PRECISION NOT NULL, INDEX IDX_FF982C8FDDEAB1A3 (etudiant_id), INDEX IDX_FF982C8FD233E95C (resultat_id), INDEX IDX_FF982C8F5C8659A (examen_id), INDEX IDX_FF982C8F18DEEBA5 (unite_enseignement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE archive_resultat (id INT AUTO_INCREMENT NOT NULL, niveau_id INT NOT NULL, semestre_id INT NOT NULL, examen_id INT NOT NULL, unite_enseignement_id INT NOT NULL, annee_universitaire_id INT NOT NULL, INDEX IDX_CAB4928EB3E9C81 (niveau_id), INDEX IDX_CAB4928E5577AFDB (semestre_id), INDEX IDX_CAB4928E5C8659A (examen_id), INDEX IDX_CAB4928E18DEEBA5 (unite_enseignement_id), INDEX IDX_CAB4928E544BFD58 (annee_universitaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE examen (id INT AUTO_INCREMENT NOT NULL, annee_universitaire_id INT NOT NULL, nom VARCHAR(255) NOT NULL, annee_examen DATE NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_514C8FEC544BFD58 (annee_universitaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE liste_des_etudiants (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE norme (id INT AUTO_INCREMENT NOT NULL, moyenne_admis DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE note (id INT AUTO_INCREMENT NOT NULL, etudiant_id INT NOT NULL, resultat_id INT DEFAULT NULL, examen_id INT NOT NULL, unite_enseignement_id INT NOT NULL, valeur DOUBLE PRECISION NOT NULL, INDEX IDX_CFBDFA14DDEAB1A3 (etudiant_id), INDEX IDX_CFBDFA14D233E95C (resultat_id), INDEX IDX_CFBDFA145C8659A (examen_id), INDEX IDX_CFBDFA1418DEEBA5 (unite_enseignement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE professeur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenoms VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, cin VARCHAR(255) NOT NULL, tel VARCHAR(255) NOT NULL, filename VARCHAR(255) DEFAULT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE resultat (id INT AUTO_INCREMENT NOT NULL, niveau_id INT NOT NULL, semestre_id INT NOT NULL, examen_id INT NOT NULL, unite_enseignement_id INT NOT NULL, annee_universitaire_id INT NOT NULL, INDEX IDX_E7DB5DE2B3E9C81 (niveau_id), INDEX IDX_E7DB5DE25577AFDB (semestre_id), INDEX IDX_E7DB5DE25C8659A (examen_id), INDEX IDX_E7DB5DE218DEEBA5 (unite_enseignement_id), INDEX IDX_E7DB5DE2544BFD58 (annee_universitaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE semestre (id INT AUTO_INCREMENT NOT NULL, niveau_id INT NOT NULL, nom VARCHAR(255) NOT NULL, statut TINYINT(1) NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_71688FBCB3E9C81 (niveau_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unite_enseignement (id INT AUTO_INCREMENT NOT NULL, professeur_id INT NOT NULL, nom VARCHAR(255) NOT NULL, credit INT NOT NULL, statut TINYINT(1) NOT NULL, INDEX IDX_46D07C4FBAB22EE9 (professeur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unite_enseignement_semestre (unite_enseignement_id INT NOT NULL, semestre_id INT NOT NULL, INDEX IDX_D7BE68E118DEEBA5 (unite_enseignement_id), INDEX IDX_D7BE68E15577AFDB (semestre_id), PRIMARY KEY(unite_enseignement_id, semestre_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unite_enseignement_niveau (unite_enseignement_id INT NOT NULL, niveau_id INT NOT NULL, INDEX IDX_4BC5A47A18DEEBA5 (unite_enseignement_id), INDEX IDX_4BC5A47AB3E9C81 (niveau_id), PRIMARY KEY(unite_enseignement_id, niveau_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE archive ADD CONSTRAINT FK_D5FC5D9CDDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id)');
        $this->addSql('ALTER TABLE archive ADD CONSTRAINT FK_D5FC5D9CD233E95C FOREIGN KEY (resultat_id) REFERENCES archive_resultat (id)');
        $this->addSql('ALTER TABLE archive_note ADD CONSTRAINT FK_FF982C8FDDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id)');
        $this->addSql('ALTER TABLE archive_note ADD CONSTRAINT FK_FF982C8FD233E95C FOREIGN KEY (resultat_id) REFERENCES archive_resultat (id)');
        $this->addSql('ALTER TABLE archive_note ADD CONSTRAINT FK_FF982C8F5C8659A FOREIGN KEY (examen_id) REFERENCES examen (id)');
        $this->addSql('ALTER TABLE archive_note ADD CONSTRAINT FK_FF982C8F18DEEBA5 FOREIGN KEY (unite_enseignement_id) REFERENCES unite_enseignement (id)');
        $this->addSql('ALTER TABLE archive_resultat ADD CONSTRAINT FK_CAB4928EB3E9C81 FOREIGN KEY (niveau_id) REFERENCES niveau (id)');
        $this->addSql('ALTER TABLE archive_resultat ADD CONSTRAINT FK_CAB4928E5577AFDB FOREIGN KEY (semestre_id) REFERENCES semestre (id)');
        $this->addSql('ALTER TABLE archive_resultat ADD CONSTRAINT FK_CAB4928E5C8659A FOREIGN KEY (examen_id) REFERENCES examen (id)');
        $this->addSql('ALTER TABLE archive_resultat ADD CONSTRAINT FK_CAB4928E18DEEBA5 FOREIGN KEY (unite_enseignement_id) REFERENCES unite_enseignement (id)');
        $this->addSql('ALTER TABLE archive_resultat ADD CONSTRAINT FK_CAB4928E544BFD58 FOREIGN KEY (annee_universitaire_id) REFERENCES annee_universitaire (id)');
        $this->addSql('ALTER TABLE examen ADD CONSTRAINT FK_514C8FEC544BFD58 FOREIGN KEY (annee_universitaire_id) REFERENCES annee_universitaire (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA14D233E95C FOREIGN KEY (resultat_id) REFERENCES resultat (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA145C8659A FOREIGN KEY (examen_id) REFERENCES examen (id)');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA1418DEEBA5 FOREIGN KEY (unite_enseignement_id) REFERENCES unite_enseignement (id)');
        $this->addSql('ALTER TABLE resultat ADD CONSTRAINT FK_E7DB5DE2B3E9C81 FOREIGN KEY (niveau_id) REFERENCES niveau (id)');
        $this->addSql('ALTER TABLE resultat ADD CONSTRAINT FK_E7DB5DE25577AFDB FOREIGN KEY (semestre_id) REFERENCES semestre (id)');
        $this->addSql('ALTER TABLE resultat ADD CONSTRAINT FK_E7DB5DE25C8659A FOREIGN KEY (examen_id) REFERENCES examen (id)');
        $this->addSql('ALTER TABLE resultat ADD CONSTRAINT FK_E7DB5DE218DEEBA5 FOREIGN KEY (unite_enseignement_id) REFERENCES unite_enseignement (id)');
        $this->addSql('ALTER TABLE resultat ADD CONSTRAINT FK_E7DB5DE2544BFD58 FOREIGN KEY (annee_universitaire_id) REFERENCES annee_universitaire (id)');
        $this->addSql('ALTER TABLE semestre ADD CONSTRAINT FK_71688FBCB3E9C81 FOREIGN KEY (niveau_id) REFERENCES niveau (id)');
        $this->addSql('ALTER TABLE unite_enseignement ADD CONSTRAINT FK_46D07C4FBAB22EE9 FOREIGN KEY (professeur_id) REFERENCES professeur (id)');
        $this->addSql('ALTER TABLE unite_enseignement_semestre ADD CONSTRAINT FK_D7BE68E118DEEBA5 FOREIGN KEY (unite_enseignement_id) REFERENCES unite_enseignement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE unite_enseignement_semestre ADD CONSTRAINT FK_D7BE68E15577AFDB FOREIGN KEY (semestre_id) REFERENCES semestre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE unite_enseignement_niveau ADD CONSTRAINT FK_4BC5A47A18DEEBA5 FOREIGN KEY (unite_enseignement_id) REFERENCES unite_enseignement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE unite_enseignement_niveau ADD CONSTRAINT FK_4BC5A47AB3E9C81 FOREIGN KEY (niveau_id) REFERENCES niveau (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE etudiant ADD liste_des_etudiants_id INT DEFAULT NULL, ADD statut TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE etudiant ADD CONSTRAINT FK_717E22E35FE9A6F3 FOREIGN KEY (liste_des_etudiants_id) REFERENCES liste_des_etudiants (id)');
        $this->addSql('CREATE INDEX IDX_717E22E35FE9A6F3 ON etudiant (liste_des_etudiants_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE archive_resultat DROP FOREIGN KEY FK_CAB4928E544BFD58');
        $this->addSql('ALTER TABLE examen DROP FOREIGN KEY FK_514C8FEC544BFD58');
        $this->addSql('ALTER TABLE resultat DROP FOREIGN KEY FK_E7DB5DE2544BFD58');
        $this->addSql('ALTER TABLE archive DROP FOREIGN KEY FK_D5FC5D9CD233E95C');
        $this->addSql('ALTER TABLE archive_note DROP FOREIGN KEY FK_FF982C8FD233E95C');
        $this->addSql('ALTER TABLE archive_note DROP FOREIGN KEY FK_FF982C8F5C8659A');
        $this->addSql('ALTER TABLE archive_resultat DROP FOREIGN KEY FK_CAB4928E5C8659A');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA145C8659A');
        $this->addSql('ALTER TABLE resultat DROP FOREIGN KEY FK_E7DB5DE25C8659A');
        $this->addSql('ALTER TABLE etudiant DROP FOREIGN KEY FK_717E22E35FE9A6F3');
        $this->addSql('ALTER TABLE unite_enseignement DROP FOREIGN KEY FK_46D07C4FBAB22EE9');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA14D233E95C');
        $this->addSql('ALTER TABLE archive_resultat DROP FOREIGN KEY FK_CAB4928E5577AFDB');
        $this->addSql('ALTER TABLE resultat DROP FOREIGN KEY FK_E7DB5DE25577AFDB');
        $this->addSql('ALTER TABLE unite_enseignement_semestre DROP FOREIGN KEY FK_D7BE68E15577AFDB');
        $this->addSql('ALTER TABLE archive_note DROP FOREIGN KEY FK_FF982C8F18DEEBA5');
        $this->addSql('ALTER TABLE archive_resultat DROP FOREIGN KEY FK_CAB4928E18DEEBA5');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA1418DEEBA5');
        $this->addSql('ALTER TABLE resultat DROP FOREIGN KEY FK_E7DB5DE218DEEBA5');
        $this->addSql('ALTER TABLE unite_enseignement_semestre DROP FOREIGN KEY FK_D7BE68E118DEEBA5');
        $this->addSql('ALTER TABLE unite_enseignement_niveau DROP FOREIGN KEY FK_4BC5A47A18DEEBA5');
        $this->addSql('DROP TABLE annee_universitaire');
        $this->addSql('DROP TABLE archive');
        $this->addSql('DROP TABLE archive_note');
        $this->addSql('DROP TABLE archive_resultat');
        $this->addSql('DROP TABLE examen');
        $this->addSql('DROP TABLE liste_des_etudiants');
        $this->addSql('DROP TABLE norme');
        $this->addSql('DROP TABLE note');
        $this->addSql('DROP TABLE professeur');
        $this->addSql('DROP TABLE resultat');
        $this->addSql('DROP TABLE semestre');
        $this->addSql('DROP TABLE unite_enseignement');
        $this->addSql('DROP TABLE unite_enseignement_semestre');
        $this->addSql('DROP TABLE unite_enseignement_niveau');
        $this->addSql('DROP INDEX IDX_717E22E35FE9A6F3 ON etudiant');
        $this->addSql('ALTER TABLE etudiant DROP liste_des_etudiants_id, DROP statut');
    }
}
