<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250830161159 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
    // this up() migration is auto-generated, please modify it to your needs
    $this->addSql('ALTER TABLE vehicle ADD vin VARCHAR(17) NOT NULL');
    $this->addSql('ALTER TABLE vehicle ADD itv TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    $this->addSql('ALTER TABLE vehicle ADD next_itv TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    $this->addSql('ALTER TABLE vehicle ADD next_maintenance_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    $this->addSql('COMMENT ON COLUMN vehicle.itv IS \'(DC2Type:datetime_immutable)\'');
    $this->addSql('COMMENT ON COLUMN vehicle.next_itv IS \'(DC2Type:datetime_immutable)\'');
    $this->addSql('COMMENT ON COLUMN vehicle.next_maintenance_at IS \'(DC2Type:datetime_immutable)\'');
    $this->addSql('CREATE UNIQUE INDEX UNIQ_1B80E486B1085141 ON vehicle (vin)');
    $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS uniq_vehicles_plate_ci ON vehicle (LOWER(plate));');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_1B80E486B1085141');
        $this->addSql('ALTER TABLE vehicle DROP vin');
        $this->addSql('ALTER TABLE vehicle DROP itv');
        $this->addSql('ALTER TABLE vehicle DROP next_itv');
        $this->addSql('ALTER TABLE vehicle DROP next_maintenance_at');
    }
}
