<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221223153034 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE agenda (id INT AUTO_INCREMENT NOT NULL, work_post_id INT NOT NULL, name VARCHAR(255) NOT NULL, paragraphe LONGTEXT DEFAULT NULL, INDEX IDX_2CEDC8779E42BB0E (work_post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE announcement (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, content LONGTEXT NOT NULL, imgs LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', files LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_4DB9D91C4B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, eagle_id INT NOT NULL, article_category_id INT NOT NULL, title VARCHAR(255) NOT NULL, date DATE NOT NULL, img VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, INDEX IDX_23A0E66C23885A (eagle_id), INDEX IDX_23A0E6688C5F785 (article_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE article_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE attendance (id INT AUTO_INCREMENT NOT NULL, eagle_id INT NOT NULL, engagement_post_id INT NOT NULL, attendance TINYINT(1) NOT NULL, INDEX IDX_6DE30D91C23885A (eagle_id), INDEX IDX_6DE30D913CA6A281 (engagement_post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE attendance_approval (id INT AUTO_INCREMENT NOT NULL, eagle_id INT NOT NULL, engagement_post_id INT NOT NULL, date DATETIME NOT NULL, INDEX IDX_F8E933E9C23885A (eagle_id), INDEX IDX_F8E933E93CA6A281 (engagement_post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE attendance_disapproval (id INT AUTO_INCREMENT NOT NULL, eagle_id INT NOT NULL, engagement_post_id INT NOT NULL, date DATETIME NOT NULL, justification LONGTEXT NOT NULL, files LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_F3E2FB5C23885A (eagle_id), INDEX IDX_F3E2FB53CA6A281 (engagement_post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE biblio_iris (id INT AUTO_INCREMENT NOT NULL, workshop_id INT DEFAULT NULL, training_id INT DEFAULT NULL, posted_by_id INT DEFAULT NULL, content LONGTEXT NOT NULL, files LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_8ABBB5051FDCE57C (workshop_id), UNIQUE INDEX UNIQ_8ABBB505BEFD98D1 (training_id), INDEX IDX_8ABBB5055A6D2235 (posted_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blame (id INT AUTO_INCREMENT NOT NULL, eagle_id INT NOT NULL, date DATETIME NOT NULL, reason VARCHAR(255) NOT NULL, INDEX IDX_F0BEBAA1C23885A (eagle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, eagle_id INT NOT NULL, post_id INT NOT NULL, date DATETIME NOT NULL, content LONGTEXT NOT NULL, INDEX IDX_9474526CC23885A (eagle_id), INDEX IDX_9474526C4B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE department (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discount (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, rate DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE engagement_post (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, place VARCHAR(255) DEFAULT NULL, date DATETIME NOT NULL, link VARCHAR(255) DEFAULT NULL, start DATETIME NOT NULL, end DATETIME NOT NULL, canceled TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_D0EAC9004B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE field (id INT AUTO_INCREMENT NOT NULL, previous_field_id INT DEFAULT NULL, form_id INT NOT NULL, label VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, required TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_5BF545585BDC1FB4 (previous_field_id), INDEX IDX_5BF545585FF69B7D (form_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE form (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, crea_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE history (id INT AUTO_INCREMENT NOT NULL, eagle_id INT NOT NULL, roles JSON NOT NULL, valid_until DATETIME NOT NULL, INDEX IDX_27BA704BC23885A (eagle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, image_name VARCHAR(255) NOT NULL, INDEX IDX_C53D045F4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mandate (id INT AUTO_INCREMENT NOT NULL, next_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, start DATE NOT NULL, UNIQUE INDEX UNIQ_197D0FEEAA23F6C8 (next_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meeting (id INT AUTO_INCREMENT NOT NULL, work_post_id INT NOT NULL, type VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_F515E1399E42BB0E (work_post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `option` (id INT AUTO_INCREMENT NOT NULL, field_id INT NOT NULL, value VARCHAR(255) NOT NULL, INDEX IDX_5A8600B0443707B0 (field_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, eagle_id INT NOT NULL, product_id INT NOT NULL, qty INT NOT NULL, date DATETIME NOT NULL, _option VARCHAR(255) DEFAULT NULL, INDEX IDX_F5299398C23885A (eagle_id), INDEX IDX_F52993984584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE poll (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, end DATETIME NOT NULL, UNIQUE INDEX UNIQ_84BCFA454B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE poll_option (id INT AUTO_INCREMENT NOT NULL, poll_id INT NOT NULL, value VARCHAR(255) NOT NULL, INDEX IDX_B68343EB3C947C0F (poll_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE polling (id INT AUTO_INCREMENT NOT NULL, eagle_id INT NOT NULL, poll_id INT NOT NULL, poll_option_id INT NOT NULL, date DATETIME NOT NULL, INDEX IDX_CA3A2250C23885A (eagle_id), INDEX IDX_CA3A22503C947C0F (poll_id), INDEX IDX_CA3A22506C13349B (poll_option_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, name VARCHAR(255) NOT NULL, publish_date DATETIME NOT NULL, targets VARCHAR(255) DEFAULT NULL, INDEX IDX_5A8A6C8DF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_department (post_id INT NOT NULL, department_id INT NOT NULL, INDEX IDX_EE65E84B4B89032C (post_id), INDEX IDX_EE65E84BAE80F5DF (department_id), PRIMARY KEY(post_id, department_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE price_proposal (id INT AUTO_INCREMENT NOT NULL, discount_id INT NOT NULL, prospect_id INT NOT NULL, object VARCHAR(255) NOT NULL, creation_date DATE NOT NULL, currency VARCHAR(255) NOT NULL, INDEX IDX_DD53AF654C7C611F (discount_id), INDEX IDX_DD53AF65D182060A (prospect_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE price_proposal_service (price_proposal_id INT NOT NULL, service_id INT NOT NULL, INDEX IDX_4742B746F3A06C4E (price_proposal_id), INDEX IDX_4742B746ED5CA9E6 (service_id), PRIMARY KEY(price_proposal_id, service_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE price_proposal_feature (id INT AUTO_INCREMENT NOT NULL, price_proposal_id INT NOT NULL, description LONGTEXT NOT NULL, qty INT NOT NULL, price DOUBLE PRECISION NOT NULL, discount DOUBLE PRECISION DEFAULT NULL, INDEX IDX_B90858F2F3A06C4E (price_proposal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', description LONGTEXT DEFAULT NULL, INDEX IDX_D34A04AD12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_action (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, next_id INT DEFAULT NULL, quantity INT NOT NULL, unit_price INT NOT NULL, date DATETIME NOT NULL, note LONGTEXT DEFAULT NULL, entry_state VARCHAR(255) NOT NULL, actual_state VARCHAR(255) DEFAULT NULL, action_type VARCHAR(255) NOT NULL, actual_state_date DATETIME DEFAULT NULL, INDEX IDX_25B0CD364584665A (product_id), UNIQUE INDEX UNIQ_25B0CD36AA23F6C8 (next_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prospect (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, agent VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE request (id INT AUTO_INCREMENT NOT NULL, eagle_id INT NOT NULL, date DATETIME NOT NULL, complaint TINYINT(1) NOT NULL, content LONGTEXT NOT NULL, INDEX IDX_3B978F9FC23885A (eagle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE response (id INT AUTO_INCREMENT NOT NULL, field_id INT NOT NULL, eagle_id INT NOT NULL, response LONGTEXT NOT NULL, INDEX IDX_3E7B0BFB443707B0 (field_id), INDEX IDX_3E7B0BFBC23885A (eagle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, department_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_E19D9AD2AE80F5DF (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_feature (id INT AUTO_INCREMENT NOT NULL, service_id INT NOT NULL, description LONGTEXT NOT NULL, price DOUBLE PRECISION NOT NULL, INDEX IDX_D05327FAED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sponsor (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, note LONGTEXT NOT NULL, emails LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', phones LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', linkedin VARCHAR(255) NOT NULL, facebook VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE study_field (id INT AUTO_INCREMENT NOT NULL, university_id INT NOT NULL, field VARCHAR(255) NOT NULL, INDEX IDX_48F15B8309D1878 (university_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, eagle_id INT NOT NULL, department_id INT NOT NULL, title LONGTEXT NOT NULL, note LONGTEXT NOT NULL, date DATETIME NOT NULL, start_time TIME NOT NULL, end_time TIME NOT NULL, created_at DATETIME NOT NULL, is_completed TINYINT(1) DEFAULT NULL, remind INT NOT NULL, repetition VARCHAR(255) NOT NULL, color VARCHAR(255) NOT NULL, is_personal TINYINT(1) NOT NULL, INDEX IDX_527EDB25C23885A (eagle_id), INDEX IDX_527EDB25AE80F5DF (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trainer (id INT AUTO_INCREMENT NOT NULL, f_name VARCHAR(255) NOT NULL, l_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trainer_training (trainer_id INT NOT NULL, training_id INT NOT NULL, INDEX IDX_DE056A97FB08EDF6 (trainer_id), INDEX IDX_DE056A97BEFD98D1 (training_id), PRIMARY KEY(trainer_id, training_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE training (id INT AUTO_INCREMENT NOT NULL, engagement_post_id INT NOT NULL, satisfaction_form_id INT NOT NULL, UNIQUE INDEX UNIQ_D5128A8F3CA6A281 (engagement_post_id), UNIQUE INDEX UNIQ_D5128A8FC1C6FB27 (satisfaction_form_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE university (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, university_id INT NOT NULL, study_field_id INT NOT NULL, department_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, f_name VARCHAR(255) NOT NULL, l_name VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, birthday DATE NOT NULL, adress VARCHAR(255) NOT NULL, img VARCHAR(255) DEFAULT NULL, linkedin VARCHAR(255) DEFAULT NULL, token_fcm VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649309D1878 (university_id), INDEX IDX_8D93D649E7BE1239 (study_field_id), INDEX IDX_8D93D649AE80F5DF (department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE work_post (id INT AUTO_INCREMENT NOT NULL, engagement_post_id INT NOT NULL, sg_id INT NOT NULL, UNIQUE INDEX UNIQ_A349DDB53CA6A281 (engagement_post_id), INDEX IDX_A349DDB547A1BE9A (sg_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE workshop (id INT AUTO_INCREMENT NOT NULL, work_post_id INT NOT NULL, satisfaction_form_id INT NOT NULL, UNIQUE INDEX UNIQ_9B6F02C49E42BB0E (work_post_id), UNIQUE INDEX UNIQ_9B6F02C4C1C6FB27 (satisfaction_form_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE agenda ADD CONSTRAINT FK_2CEDC8779E42BB0E FOREIGN KEY (work_post_id) REFERENCES work_post (id)');
        $this->addSql('ALTER TABLE announcement ADD CONSTRAINT FK_4DB9D91C4B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66C23885A FOREIGN KEY (eagle_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E6688C5F785 FOREIGN KEY (article_category_id) REFERENCES article_category (id)');
        $this->addSql('ALTER TABLE attendance ADD CONSTRAINT FK_6DE30D91C23885A FOREIGN KEY (eagle_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE attendance ADD CONSTRAINT FK_6DE30D913CA6A281 FOREIGN KEY (engagement_post_id) REFERENCES engagement_post (id)');
        $this->addSql('ALTER TABLE attendance_approval ADD CONSTRAINT FK_F8E933E9C23885A FOREIGN KEY (eagle_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE attendance_approval ADD CONSTRAINT FK_F8E933E93CA6A281 FOREIGN KEY (engagement_post_id) REFERENCES engagement_post (id)');
        $this->addSql('ALTER TABLE attendance_disapproval ADD CONSTRAINT FK_F3E2FB5C23885A FOREIGN KEY (eagle_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE attendance_disapproval ADD CONSTRAINT FK_F3E2FB53CA6A281 FOREIGN KEY (engagement_post_id) REFERENCES engagement_post (id)');
        $this->addSql('ALTER TABLE biblio_iris ADD CONSTRAINT FK_8ABBB5051FDCE57C FOREIGN KEY (workshop_id) REFERENCES workshop (id)');
        $this->addSql('ALTER TABLE biblio_iris ADD CONSTRAINT FK_8ABBB505BEFD98D1 FOREIGN KEY (training_id) REFERENCES training (id)');
        $this->addSql('ALTER TABLE biblio_iris ADD CONSTRAINT FK_8ABBB5055A6D2235 FOREIGN KEY (posted_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE blame ADD CONSTRAINT FK_F0BEBAA1C23885A FOREIGN KEY (eagle_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CC23885A FOREIGN KEY (eagle_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C4B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE engagement_post ADD CONSTRAINT FK_D0EAC9004B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE field ADD CONSTRAINT FK_5BF545585BDC1FB4 FOREIGN KEY (previous_field_id) REFERENCES field (id)');
        $this->addSql('ALTER TABLE field ADD CONSTRAINT FK_5BF545585FF69B7D FOREIGN KEY (form_id) REFERENCES form (id)');
        $this->addSql('ALTER TABLE history ADD CONSTRAINT FK_27BA704BC23885A FOREIGN KEY (eagle_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE mandate ADD CONSTRAINT FK_197D0FEEAA23F6C8 FOREIGN KEY (next_id) REFERENCES mandate (id)');
        $this->addSql('ALTER TABLE meeting ADD CONSTRAINT FK_F515E1399E42BB0E FOREIGN KEY (work_post_id) REFERENCES work_post (id)');
        $this->addSql('ALTER TABLE `option` ADD CONSTRAINT FK_5A8600B0443707B0 FOREIGN KEY (field_id) REFERENCES field (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398C23885A FOREIGN KEY (eagle_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993984584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE poll ADD CONSTRAINT FK_84BCFA454B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE poll_option ADD CONSTRAINT FK_B68343EB3C947C0F FOREIGN KEY (poll_id) REFERENCES poll (id)');
        $this->addSql('ALTER TABLE polling ADD CONSTRAINT FK_CA3A2250C23885A FOREIGN KEY (eagle_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE polling ADD CONSTRAINT FK_CA3A22503C947C0F FOREIGN KEY (poll_id) REFERENCES poll (id)');
        $this->addSql('ALTER TABLE polling ADD CONSTRAINT FK_CA3A22506C13349B FOREIGN KEY (poll_option_id) REFERENCES poll_option (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE post_department ADD CONSTRAINT FK_EE65E84B4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_department ADD CONSTRAINT FK_EE65E84BAE80F5DF FOREIGN KEY (department_id) REFERENCES department (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE price_proposal ADD CONSTRAINT FK_DD53AF654C7C611F FOREIGN KEY (discount_id) REFERENCES discount (id)');
        $this->addSql('ALTER TABLE price_proposal ADD CONSTRAINT FK_DD53AF65D182060A FOREIGN KEY (prospect_id) REFERENCES prospect (id)');
        $this->addSql('ALTER TABLE price_proposal_service ADD CONSTRAINT FK_4742B746F3A06C4E FOREIGN KEY (price_proposal_id) REFERENCES price_proposal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE price_proposal_service ADD CONSTRAINT FK_4742B746ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE price_proposal_feature ADD CONSTRAINT FK_B90858F2F3A06C4E FOREIGN KEY (price_proposal_id) REFERENCES price_proposal (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE product_action ADD CONSTRAINT FK_25B0CD364584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_action ADD CONSTRAINT FK_25B0CD36AA23F6C8 FOREIGN KEY (next_id) REFERENCES product_action (id)');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9FC23885A FOREIGN KEY (eagle_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE response ADD CONSTRAINT FK_3E7B0BFB443707B0 FOREIGN KEY (field_id) REFERENCES field (id)');
        $this->addSql('ALTER TABLE response ADD CONSTRAINT FK_3E7B0BFBC23885A FOREIGN KEY (eagle_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE service_feature ADD CONSTRAINT FK_D05327FAED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE study_field ADD CONSTRAINT FK_48F15B8309D1878 FOREIGN KEY (university_id) REFERENCES university (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25C23885A FOREIGN KEY (eagle_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE trainer_training ADD CONSTRAINT FK_DE056A97FB08EDF6 FOREIGN KEY (trainer_id) REFERENCES trainer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trainer_training ADD CONSTRAINT FK_DE056A97BEFD98D1 FOREIGN KEY (training_id) REFERENCES training (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE training ADD CONSTRAINT FK_D5128A8F3CA6A281 FOREIGN KEY (engagement_post_id) REFERENCES engagement_post (id)');
        $this->addSql('ALTER TABLE training ADD CONSTRAINT FK_D5128A8FC1C6FB27 FOREIGN KEY (satisfaction_form_id) REFERENCES form (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649309D1878 FOREIGN KEY (university_id) REFERENCES university (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649E7BE1239 FOREIGN KEY (study_field_id) REFERENCES study_field (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('ALTER TABLE work_post ADD CONSTRAINT FK_A349DDB53CA6A281 FOREIGN KEY (engagement_post_id) REFERENCES engagement_post (id)');
        $this->addSql('ALTER TABLE work_post ADD CONSTRAINT FK_A349DDB547A1BE9A FOREIGN KEY (sg_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE workshop ADD CONSTRAINT FK_9B6F02C49E42BB0E FOREIGN KEY (work_post_id) REFERENCES work_post (id)');
        $this->addSql('ALTER TABLE workshop ADD CONSTRAINT FK_9B6F02C4C1C6FB27 FOREIGN KEY (satisfaction_form_id) REFERENCES form (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agenda DROP FOREIGN KEY FK_2CEDC8779E42BB0E');
        $this->addSql('ALTER TABLE announcement DROP FOREIGN KEY FK_4DB9D91C4B89032C');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66C23885A');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E6688C5F785');
        $this->addSql('ALTER TABLE attendance DROP FOREIGN KEY FK_6DE30D91C23885A');
        $this->addSql('ALTER TABLE attendance DROP FOREIGN KEY FK_6DE30D913CA6A281');
        $this->addSql('ALTER TABLE attendance_approval DROP FOREIGN KEY FK_F8E933E9C23885A');
        $this->addSql('ALTER TABLE attendance_approval DROP FOREIGN KEY FK_F8E933E93CA6A281');
        $this->addSql('ALTER TABLE attendance_disapproval DROP FOREIGN KEY FK_F3E2FB5C23885A');
        $this->addSql('ALTER TABLE attendance_disapproval DROP FOREIGN KEY FK_F3E2FB53CA6A281');
        $this->addSql('ALTER TABLE biblio_iris DROP FOREIGN KEY FK_8ABBB5051FDCE57C');
        $this->addSql('ALTER TABLE biblio_iris DROP FOREIGN KEY FK_8ABBB505BEFD98D1');
        $this->addSql('ALTER TABLE biblio_iris DROP FOREIGN KEY FK_8ABBB5055A6D2235');
        $this->addSql('ALTER TABLE blame DROP FOREIGN KEY FK_F0BEBAA1C23885A');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CC23885A');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C4B89032C');
        $this->addSql('ALTER TABLE engagement_post DROP FOREIGN KEY FK_D0EAC9004B89032C');
        $this->addSql('ALTER TABLE field DROP FOREIGN KEY FK_5BF545585BDC1FB4');
        $this->addSql('ALTER TABLE field DROP FOREIGN KEY FK_5BF545585FF69B7D');
        $this->addSql('ALTER TABLE history DROP FOREIGN KEY FK_27BA704BC23885A');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F4584665A');
        $this->addSql('ALTER TABLE mandate DROP FOREIGN KEY FK_197D0FEEAA23F6C8');
        $this->addSql('ALTER TABLE meeting DROP FOREIGN KEY FK_F515E1399E42BB0E');
        $this->addSql('ALTER TABLE `option` DROP FOREIGN KEY FK_5A8600B0443707B0');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398C23885A');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993984584665A');
        $this->addSql('ALTER TABLE poll DROP FOREIGN KEY FK_84BCFA454B89032C');
        $this->addSql('ALTER TABLE poll_option DROP FOREIGN KEY FK_B68343EB3C947C0F');
        $this->addSql('ALTER TABLE polling DROP FOREIGN KEY FK_CA3A2250C23885A');
        $this->addSql('ALTER TABLE polling DROP FOREIGN KEY FK_CA3A22503C947C0F');
        $this->addSql('ALTER TABLE polling DROP FOREIGN KEY FK_CA3A22506C13349B');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DF675F31B');
        $this->addSql('ALTER TABLE post_department DROP FOREIGN KEY FK_EE65E84B4B89032C');
        $this->addSql('ALTER TABLE post_department DROP FOREIGN KEY FK_EE65E84BAE80F5DF');
        $this->addSql('ALTER TABLE price_proposal DROP FOREIGN KEY FK_DD53AF654C7C611F');
        $this->addSql('ALTER TABLE price_proposal DROP FOREIGN KEY FK_DD53AF65D182060A');
        $this->addSql('ALTER TABLE price_proposal_service DROP FOREIGN KEY FK_4742B746F3A06C4E');
        $this->addSql('ALTER TABLE price_proposal_service DROP FOREIGN KEY FK_4742B746ED5CA9E6');
        $this->addSql('ALTER TABLE price_proposal_feature DROP FOREIGN KEY FK_B90858F2F3A06C4E');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('ALTER TABLE product_action DROP FOREIGN KEY FK_25B0CD364584665A');
        $this->addSql('ALTER TABLE product_action DROP FOREIGN KEY FK_25B0CD36AA23F6C8');
        $this->addSql('ALTER TABLE request DROP FOREIGN KEY FK_3B978F9FC23885A');
        $this->addSql('ALTER TABLE response DROP FOREIGN KEY FK_3E7B0BFB443707B0');
        $this->addSql('ALTER TABLE response DROP FOREIGN KEY FK_3E7B0BFBC23885A');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD2AE80F5DF');
        $this->addSql('ALTER TABLE service_feature DROP FOREIGN KEY FK_D05327FAED5CA9E6');
        $this->addSql('ALTER TABLE study_field DROP FOREIGN KEY FK_48F15B8309D1878');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25C23885A');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25AE80F5DF');
        $this->addSql('ALTER TABLE trainer_training DROP FOREIGN KEY FK_DE056A97FB08EDF6');
        $this->addSql('ALTER TABLE trainer_training DROP FOREIGN KEY FK_DE056A97BEFD98D1');
        $this->addSql('ALTER TABLE training DROP FOREIGN KEY FK_D5128A8F3CA6A281');
        $this->addSql('ALTER TABLE training DROP FOREIGN KEY FK_D5128A8FC1C6FB27');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649309D1878');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649E7BE1239');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649AE80F5DF');
        $this->addSql('ALTER TABLE work_post DROP FOREIGN KEY FK_A349DDB53CA6A281');
        $this->addSql('ALTER TABLE work_post DROP FOREIGN KEY FK_A349DDB547A1BE9A');
        $this->addSql('ALTER TABLE workshop DROP FOREIGN KEY FK_9B6F02C49E42BB0E');
        $this->addSql('ALTER TABLE workshop DROP FOREIGN KEY FK_9B6F02C4C1C6FB27');
        $this->addSql('DROP TABLE agenda');
        $this->addSql('DROP TABLE announcement');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE article_category');
        $this->addSql('DROP TABLE attendance');
        $this->addSql('DROP TABLE attendance_approval');
        $this->addSql('DROP TABLE attendance_disapproval');
        $this->addSql('DROP TABLE biblio_iris');
        $this->addSql('DROP TABLE blame');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE department');
        $this->addSql('DROP TABLE discount');
        $this->addSql('DROP TABLE engagement_post');
        $this->addSql('DROP TABLE field');
        $this->addSql('DROP TABLE form');
        $this->addSql('DROP TABLE history');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE mandate');
        $this->addSql('DROP TABLE meeting');
        $this->addSql('DROP TABLE `option`');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE poll');
        $this->addSql('DROP TABLE poll_option');
        $this->addSql('DROP TABLE polling');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE post_department');
        $this->addSql('DROP TABLE price_proposal');
        $this->addSql('DROP TABLE price_proposal_service');
        $this->addSql('DROP TABLE price_proposal_feature');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_action');
        $this->addSql('DROP TABLE prospect');
        $this->addSql('DROP TABLE request');
        $this->addSql('DROP TABLE response');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE service_feature');
        $this->addSql('DROP TABLE sponsor');
        $this->addSql('DROP TABLE study_field');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE trainer');
        $this->addSql('DROP TABLE trainer_training');
        $this->addSql('DROP TABLE training');
        $this->addSql('DROP TABLE university');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE work_post');
        $this->addSql('DROP TABLE workshop');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
