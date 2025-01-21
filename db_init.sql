/* Create the database and tables for the Quizstuff application */
CREATE DATABASE `quizstuff`;

/* Product Table */
CREATE TABLE `quizstuff`.`products`(
  `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Product ID',
  `name` VARCHAR(255) NOT NULL COMMENT 'Product name',
  `model` VARCHAR(255) NULL COMMENT 'Model number',
  `has_variants` BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Does this product have variants?',
  `has_options` BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Does this product have options?',
  `price` DECIMAL(10, 2) NOT NULL DEFAULT 0.00 COMMENT 'Price of product',
  `weight` DECIMAL(10, 2) NOT NULL DEFAULT 0.00 COMMENT 'Weight in pounds (lbs)',
  `flat_shipping` DECIMAL(10,2) NULL,
  `description` VARCHAR(1000) NOT NULL DEFAULT '' COMMENT 'Description of product',
  `image` VARCHAR(255) NULL COMMENT 'Image URL',
  PRIMARY KEY(`id`)
) ENGINE = InnoDB COMMENT = 'Product details';

/* Insert core products */
INSERT INTO `quizstuff`.`products` (`name`, `model`, `has_variants`, `has_options`, `price`, `weight`, `image`, `description`, `flat_shipping`) VALUES 
<<<<<<< Updated upstream
('Quizstuff Hardware Bundle', NULL, FALSE, FALSE, 360.00, 0.00, NULL, 'Includes one USB Interface Box and three strings of Chair Pads. Flat rate of $15.95 shipping via USPS.', 15.95 ),
=======
('Quizstuff Hardware Bundle', NULL, FALSE, FALSE, 360.00, 0.00, NULL, 'Includes one USB Interface Box and three strings of Chair Pads.\nFlat rate of $15.95 shipping via USPS (separate from list price).', 15.95 ),
>>>>>>> Stashed changes
('USB Interface Box', 'v0.9', FALSE, FALSE, 135.00, 0.00, NULL, 'USB Interface Box connects up to 3 strings of pads to a computer.\nUse QuizMachine software on Microsoft operating systems. Windows 7,8,10 all versions only.\nNo power cord required. Power is supplied via the USB port on the computer.', NULL),
('Chair Pads (5 Per String)', 'v18', TRUE, FALSE, 75.00, 0.00, NULL, 'NOTICE: Pads are being made as quickly as possible (within a 4-6 weeks provided parts are available) as another person is helping. Please contact me for more information.\nPads - strings of 5 pads. Colors: Red, Blue (old yellow) and Green.\nThese are intended to work with QuizMachine and either the USB or Parallel port interface boxes from Quizstuff, but we cannot necessarily guarantee the strings to work with other quiz equipment. Use with other equipment at your own risk.\nMade with Naugahyde leather and all sides sewn together professionally.', NULL),
('QuizMachine (1 User)', 'v5.4.J30', FALSE, FALSE, 75.00, 0.00, NULL, 'Single registration - includes lifetime upgrades.\nThe software called QuizMachine used in Nazarene style bible quizzing. Detects which person jumps, keeps score, records all options for the rounds.\nScores can be exported to QMServer (included) to tally overall quiz stats.\nScoresheets can be printed.', NULL),
('QuizMachine DQD (5 Users)', 'v5.4.J30', FALSE, FALSE, 75.00, 0.00, NULL, 'DISTRICT DIRECTORS ONLY!\n5 registrations - includes lifetime upgrades.\nThe software called QuizMachine used in Nazarene style bible quizzing. Detects which person jumps, keeps score, records all options for the rounds.\nScores can be exported to QMServer (included) to tally overall quiz stats.\nScoresheets can be printed.', NULL);

/* Product Variants */
CREATE TABLE `quizstuff`.`product_variants`(
  `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Variant ID',
  `product_id` INT NOT NULL COMMENT 'Product ID',
  `name` VARCHAR(255) NOT NULL COMMENT 'Variant name',
  `variant_type` VARCHAR(255) NOT NULL COMMENT 'Type of variant',
  `price` DECIMAL(10, 2) NULL DEFAULT 0.00 COMMENT 'Price of variant',
  `weight` DECIMAL(10, 2) NULL DEFAULT 0.00 COMMENT 'Weight in pounds (lbs)',
  `description` VARCHAR(1000) NULL COMMENT 'Description of variant',
  `image` VARCHAR(255) NULL COMMENT 'Image URL',
  PRIMARY KEY(`id`),
  FOREIGN KEY(`product_id`) REFERENCES `products`(`id`)
) ENGINE = InnoDB COMMENT = 'Product variant details';

/* Insert product variants */
INSERT INTO `quizstuff`.`product_variants` (`product_id`, `name`,`variant_type`, `price`, `weight`, `image`, `description`) VALUES
(3, 'Red', 'Color', NULL, NULL, NULL, NULL),
(3, 'Blue', 'Color', NULL, NULL, NULL, NULL),
(3, 'Green', 'Color', NULL, NULL, NULL, NULL);

/* Product Options */
CREATE TABLE `quizstuff`.`product_options`(
  `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Option ID',
  `product_id` INT NOT NULL COMMENT 'Product ID',
  `product_reference_id` INT NULL COMMENT 'Reference product ID',
  `name` VARCHAR(255) NOT NULL COMMENT 'Option name',
  `price` DECIMAL(10, 2) NOT NULL DEFAULT 0.00 COMMENT 'Price of option',
  `description` VARCHAR(1000) NOT NULL DEFAULT '' COMMENT 'Description of option',
  PRIMARY KEY(`id`),
  FOREIGN KEY(`product_id`) REFERENCES `products`(`id`)
) ENGINE = InnoDB COMMENT = 'Product option details';

/* Insert product options */

/* Orders */
CREATE TABLE `quizstuff`.`orders`(
   `id` INT NOT NULL AUTO_INCREMENT,
   `name_first` VARCHAR(255) NOT NULL,
   `name_last` VARCHAR(255) NOT NULL,
   `address_1` VARCHAR(255) NULL,
   `address_2` VARCHAR(255) NULL,
   `city` VARCHAR(255) NULL,
   `state` CHAR(2) NULL,
   `zip` VARCHAR(10) NULL,
   `phone` VARCHAR(20) NULL,
   `email` VARCHAR(255) NOT NULL,
   `total_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
   `status` ENUM('pending', 'paid', 'shipped', 'delivered', 'canceled') NOT NULL DEFAULT 'pending',
   `date_created` DATETIME DEFAULT CURRENT_TIMESTAMP,
   `date_updated` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   `date_shipped` DATETIME NULL,
   PRIMARY KEY(`id`)
) ENGINE = InnoDB COMMENT = 'Orders';

/* Insert sample order */
/* INSERT INTO `quizstuff`.`orders` (`name_first`, `name_last`, `address_1`, `address_2`, `city`, `state`, `zip`, `phone`, `email`, `total_price`)
VALUES ('John', 'Doe', '123 Main St', 'Apt 4B', 'Anytown', 'NY', '12345', '1234567890', 'john.doe@example.com', 435.00); */

/* Order Items */
CREATE TABLE `quizstuff`.`order_items`(
   `id` INT NOT NULL AUTO_INCREMENT,
   `order_id` INT NOT NULL,
   `product_id` INT NOT NULL,
   `variant_id` INT NULL,
   `option_id` INT NULL,
   `quantity` INT NOT NULL DEFAULT 1,
   `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
   `total_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
   PRIMARY KEY(`id`),
   FOREIGN KEY(`order_id`) REFERENCES `orders`(`id`),
   FOREIGN KEY(`product_id`) REFERENCES `products`(`id`),
   FOREIGN KEY(`variant_id`) REFERENCES `product_variants`(`id`),
   FOREIGN KEY(`option_id`) REFERENCES `product_options`(`id`)
) ENGINE = InnoDB COMMENT = 'Order items; Get by order_id';

/* Insert sample order items */
/* INSERT INTO `quizstuff`.`order_items` (`order_id`, `product_id`, `variant_id`, `option_id`, `quantity`, `price`, `total_price`) VALUES 
	!! Values here !!
 */