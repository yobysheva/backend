<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251105143236 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE request_id_seq CASCADE');
        $this->addSql('CREATE TABLE application (id SERIAL NOT NULL, applicant_id INT NOT NULL, wanted_house_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A45BDDC197139001 ON application (applicant_id)');
        $this->addSql('CREATE INDEX IDX_A45BDDC1D2F45EC0 ON application (wanted_house_id)');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC197139001 FOREIGN KEY (applicant_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC1D2F45EC0 FOREIGN KEY (wanted_house_id) REFERENCES house (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE request DROP CONSTRAINT fk_3b978f9f97139001');
        $this->addSql('ALTER TABLE request DROP CONSTRAINT fk_3b978f9fd2f45ec0');
        $this->addSql('DROP TABLE request');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT fk_8d93d6499552c794');
        $this->addSql('DROP INDEX uniq_8d93d6499552c794');
        $this->addSql('ALTER TABLE "user" RENAME COLUMN сcurrent_house_id TO current_house_id');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649F6CF3F3F FOREIGN KEY (current_house_id) REFERENCES house (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F6CF3F3F ON "user" (current_house_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE request (id SERIAL NOT NULL, applicant_id INT NOT NULL, wanted_house_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_3b978f9f97139001 ON request (applicant_id)');
        $this->addSql('CREATE INDEX idx_3b978f9fd2f45ec0 ON request (wanted_house_id)');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT fk_3b978f9f97139001 FOREIGN KEY (applicant_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT fk_3b978f9fd2f45ec0 FOREIGN KEY (wanted_house_id) REFERENCES house (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE application DROP CONSTRAINT FK_A45BDDC197139001');
        $this->addSql('ALTER TABLE application DROP CONSTRAINT FK_A45BDDC1D2F45EC0');
        $this->addSql('DROP TABLE application');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649F6CF3F3F');
        $this->addSql('DROP INDEX UNIQ_8D93D649F6CF3F3F');
        $this->addSql('ALTER TABLE "user" RENAME COLUMN current_house_id TO "сcurrent_house_id"');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT fk_8d93d6499552c794 FOREIGN KEY ("сcurrent_house_id") REFERENCES house (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d6499552c794 ON "user" ("сcurrent_house_id")');
    }
}
