CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `user_type` varchar(20) NOT NULL DEFAULT 'user',
  `address` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `gender` varchar(10) NOT NULL,
  `phone_number` varchar(10) NOT NULL,
  `aadhar_no` varchar(12) NOT NULL,
  `education` varchar(100) NOT NULL,
  `profile_picture` varchar(255) DEFAULT 'uploads/default.png',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
